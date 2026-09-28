<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transction;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Razorpay\Api\Api;
use App\Models\DeviceSession;
use App\Helpers\SubscriptionNotificationHelper;

class WebhookController extends Controller
{
    
// Event	                            UserSubscription-Status     Transaction-Status	            User-Subscription
//---------------------------------------------------------------------------------------------------------------------
// Initial creation (before payment)	0 (Created)	                    -	                                No
// subscription.authenticated	        0 (Created)	                1 (Active)	                            Yes
// subscription.activated	            1 (Active)	                1 (Active)	                            Yes
// subscription.charged (renewal)	    1 (Active)	                Old: 4 (Completed), New: 1 (Active)	    Yes
// subscription.pending	                1 (Active - retrying)	    1 (Active - retrying)	                Yes
// subscription.halted	                5 (Halted)	                5 (Halted)	                            No
// subscription.paused	                6 (Paused)	                1 (Active - but paused)	                No
// subscription.resumed	                1 (Active)	                1 (Active)	                            Yes
// subscription.cancelled	            3 (Cancelled)	            3 (Cancelled)	                        No
// subscription.completed	            4 (Completed)	            4 (Completed)	                        No
// payment.failed	                    1 (Active - retrying)	    1 (Active - retrying)	                Yes

    // Status Constants
    const STATUS_CREATED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_FAILED = 2;
    const STATUS_CANCELLED = 3;
    const STATUS_COMPLETED = 4;
    const STATUS_HALTED = 5;
    const STATUS_PAUSED = 6;

    /**
     * Helper to get configured Razorpay API instance
     */
    private function getRazorpayApi(): ?Api
    {
        $key = config('app.razorpay_key') ?: env('RAZORPAY_KEY');
        $secret = config('app.razorpay_secret') ?: env('RAZORPAY_SECRET');
        if (empty($key) || empty($secret)) {
            Log::warning('Razorpay API credentials missing (key or secret is empty)');
            return null;
        }
        return new Api($key, $secret);
    }

    public function handleWebhook(Request $request)
    {
        $event = $request->input('event', 'unknown');
        Log::info('Webhook received', ['event' => $event]);

        // Verify signature
        try {
            $this->verifySignature($request);
        } catch (\Exception $e) {
            Log::error('Webhook signature verification failed: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        try {
            DB::beginTransaction();

            switch ($event) {
                case 'subscription.authenticated':
                    $this->handleSubscriptionAuthenticated($request);
                    break;
                    
                case 'subscription.activated':
                    $this->handleSubscriptionActivated($request);
                    break;
                    
                case 'subscription.charged':
                    $this->handleSubscriptionCharged($request);
                    break;
                    
                case 'subscription.pending':
                    $this->handleSubscriptionPending($request);
                    break;
                    
                case 'subscription.halted':
                    $this->handleSubscriptionHalted($request);
                    break;
                    
                case 'subscription.paused':
                    $this->handleSubscriptionPaused($request);
                    break;
                    
                case 'subscription.resumed':
                    $this->handleSubscriptionResumed($request);
                    break;
                    
                case 'subscription.cancelled':
                    $this->handleSubscriptionCancelled($request);
                    break;
                    
                case 'subscription.completed':
                    $this->handleSubscriptionCompleted($request);
                    break;
                    
                // case 'subscription.updated':
                //     $this->handleSubscriptionUpdated($request);
                //     break;
                    
                // case 'order.paid':
                //     $this->handleInvoicePaid($request);
                //     break;
                    
                case 'payment.failed':
                    $this->handlePaymentFailed($request);
                    break;
                    
                default:
                    Log::info('Unhandled webhook event', ['event' => $event]);
            }

            DB::commit();
            Log::info('Webhook processed successfully', ['event' => $event]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Webhook processing failed', [
                'event' => $event,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Processing failed'], 500);
        }

        return response()->json(['status' => 'success']);
    }

    private function verifySignature(Request $request)
    {
        $webhookSecret = config('app.razorpay_webhook_secret') ?: env('RAZORPAY_WEBHOOK_SECRET');
        $payload = $request->getContent();
        $signature = $request->header('X-Razorpay-Signature');
        
        // Check if signature is missing
        if (empty($signature)) {
            Log::error('Webhook signature missing from request headers');
            throw new \Exception('Signature header missing');
        }
        
        $generatedSignature = hash_hmac('sha256', $payload, $webhookSecret);

        // Use hash_equals for timing-safe comparison
        if (!hash_equals($generatedSignature, $signature)) {
            throw new \Exception('Invalid signature');
        }
    }

    /**
     * Handle subscription.authenticated event
     * First payment is successful but not yet activated
     */
    private function handleSubscriptionAuthenticated(Request $request)
    {
        Log::info('── handleSubscriptionAuthenticated START ──');

        $payload = $request->all();
        $subscriptionId = $payload['payload']['subscription']['entity']['id'] ?? null;
        $paymentId = $payload['payload']['payment']['entity']['id'] ?? null;
        $paymentData = $payload['payload']['payment']['entity'] ?? [];

        if (!$subscriptionId) {
            Log::error('Missing subscription_id', ['subscription_id' => $subscriptionId]);
            return;
        }

        // Check for duplicate payment
        if ($paymentId && Transction::where('payment_id', $paymentId)->exists()) {
            Log::info('Duplicate payment skipped', ['payment_id' => $paymentId]);
            return;
        }

        // Find user subscription
        $userSubscription = UserSubscription::where('razorpay_subscription_id', $subscriptionId)
            ->latest()
            ->first();

        // Prefer subscription entity from webhook payload first
        $subscriptionEntity = $payload['payload']['subscription']['entity'] ?? [];
        $api = $this->getRazorpayApi();
        $subscription = null;
        if ($api) {
            try {
                $subscription = $api->subscription->fetch($subscriptionId);
            } catch (\Throwable $eSubFetch) {
                Log::warning('Webhook: Failed to fetch subscription from Razorpay API: ' . $eSubFetch->getMessage());
            }
        }

        $packageIdFromNotes = (int) ($subscription['notes']['package_id'] ?? ($subscriptionEntity['notes']['package_id'] ?? 0));

        if (!$userSubscription) {
            $user = User::where('razorpay_subscription_id', $subscriptionId)->first();
            if (!$user) {
                $email = strtolower(trim($paymentData['email'] ?? ''));
                $contact = trim($paymentData['contact'] ?? '');
                if ($email || $contact) {
                    $user = User::where('email', $email)->orWhere('mobile_no', $contact)->first();
                }
            }
            if ($user) {
                $resolvedPkgId = $packageIdFromNotes ?: 1;
                $userSubscription = UserSubscription::updateOrCreate(
                    ['razorpay_subscription_id' => $subscriptionId],
                    [
                        'user_id' => $user->id,
                        'package_id' => $resolvedPkgId,
                        'status' => self::STATUS_ACTIVE,
                    ]
                );
            }
        }

        if (!$userSubscription) {
            Log::error('UserSubscription not found', ['subscription_id' => $subscriptionId]);
            return;
        }

        // Update package_id if it was missing or default
        if ($packageIdFromNotes && $userSubscription->package_id != $packageIdFromNotes) {
            $userSubscription->update(['package_id' => $packageIdFromNotes]);
        }

        // GUARD: If this subscription is already fully active, skip to avoid
        // duplicate processing and duplicate ACTIVATED emails.
        $user = User::find($userSubscription->user_id);
        if ($userSubscription->status === self::STATUS_ACTIVE
            && $user
            && $user->subscription === 'Yes'
            && $user->razorpay_subscription_id === $subscriptionId) {
            Log::info('subscription.authenticated skipped — already active', [
                'subscription_id' => $subscriptionId,
                'user_id' => $user->id,
            ]);
            return;
        }

        if (!$user) {
            Log::error('User not found', ['user_id' => $userSubscription->user_id]);
            return;
        }

        // Get expiry date: prefer payload's current_end, fallback to API, fallback to +30 days
        $currentEndUnix = $subscriptionEntity['current_end'] ?? ($subscription['current_end'] ?? null);
        $newExpiryDate = !empty($currentEndUnix)
            ? Carbon::createFromTimestamp($currentEndUnix)->format('Y-m-d H:i:s')
            : Carbon::now()->addDays(30)->format('Y-m-d H:i:s');

        if ($paymentId) {
            // Create transaction with status 1 (active) - first payment
            Transction::create([
                'user_id' => $user->id,
                'package_id' => $userSubscription->package_id,
                'amount' => ($paymentData['amount'] ?? 0) / 100,
                'currency_code' => $paymentData['currency'] ?? 'INR',
                'description' => 'Subscription - First Payment',
                'razorpay_subscription_id' => $subscriptionId,
                'payment_id' => $paymentId,
                'expiry_date' => $newExpiryDate,
                'transaction_count' => 1,
                'status' => self::STATUS_ACTIVE,
            ]);
        }

        // Update user
        $user->update([
            'subscription' => 'Yes',
            'razorpay_subscription_id' => $subscriptionId,
            'subscription_end_date' => $newExpiryDate,
            'cancelled_at' => null,
        ]);

        $userSubscription->update([
            'status' => self::STATUS_ACTIVE,
            'starts_at' => $userSubscription->starts_at ?? now(),
            'ends_at' => $newExpiryDate,
        ]);
        SubscriptionController::cancelPreviousSubscriptionsForUser($user, $subscriptionId);

        SubscriptionNotificationHelper::sendNotification(
            'ACTIVATED',
            $user,
            $userSubscription->package_id,
            $subscriptionId,
            $paymentId,
            'Subscription Authenticated via Webhook',
            ['expiry_date' => $newExpiryDate]
        );

        Log::info('── handleSubscriptionAuthenticated END — success', ['user_id' => $user->id]);
    }

    /**
     * Handle subscription.activated event
     * Subscription is now active and ready for recurring charges
     */
    private function handleSubscriptionActivated(Request $request)
    {
        Log::info('── handleSubscriptionActivated START ──');

        $payload = $request->all();
        $subscriptionId = $payload['payload']['subscription']['entity']['id'] ?? null;

        if (!$subscriptionId) {
            Log::error('Missing subscription_id', ['subscription_id' => $subscriptionId]);
            return;
        }

        $userSubscription = UserSubscription::where('razorpay_subscription_id', $subscriptionId)
            ->latest()
            ->first();

        $subscriptionEntity = $payload['payload']['subscription']['entity'] ?? [];
        $api = $this->getRazorpayApi();
        $subscription = null;
        if ($api) {
            try {
                $subscription = $api->subscription->fetch($subscriptionId);
            } catch (\Throwable $eSubFetch) {
                Log::warning('handleSubscriptionActivated: Failed to fetch subscription: ' . $eSubFetch->getMessage());
            }
        }

        $packageIdFromNotes = (int) ($subscription['notes']['package_id'] ?? ($subscriptionEntity['notes']['package_id'] ?? 0));

        if (!$userSubscription) {
            $user = User::where('razorpay_subscription_id', $subscriptionId)->first();
            if ($user) {
                $resolvedPkgId = $packageIdFromNotes ?: 1;
                $userSubscription = UserSubscription::updateOrCreate(
                    ['razorpay_subscription_id' => $subscriptionId],
                    [
                        'user_id' => $user->id,
                        'package_id' => $resolvedPkgId,
                        'status' => self::STATUS_ACTIVE,
                    ]
                );
            }
        }

        if (!$userSubscription) {
            Log::error('UserSubscription not found', ['subscription_id' => $subscriptionId]);
            return;
        }

        if ($packageIdFromNotes && $userSubscription->package_id != $packageIdFromNotes) {
            $userSubscription->update(['package_id' => $packageIdFromNotes]);
        }

        $user = User::find($userSubscription->user_id);

        // GUARD: If subscription.authenticated already activated this subscription,
        // skip re-processing to prevent sending a duplicate ACTIVATED email.
        if ($userSubscription->status === self::STATUS_ACTIVE
            && $user
            && $user->subscription === 'Yes'
            && $user->razorpay_subscription_id === $subscriptionId) {
            Log::info('subscription.activated skipped — already active (handled by subscription.authenticated)', [
                'subscription_id' => $subscriptionId,
                'user_id' => $user->id,
            ]);
            return;
        }

        // Prefer payload's current_end, fallback to API
        $currentEndUnix = $subscriptionEntity['current_end'] ?? ($subscription['current_end'] ?? null);
        $newExpiryDate = $currentEndUnix ? Carbon::createFromTimestamp($currentEndUnix)->format('Y-m-d H:i:s') : Carbon::now()->addDays(30)->format('Y-m-d H:i:s');

        // If we reach here, the subscription was NOT yet active — process it
        $subUpdate = ['status' => self::STATUS_ACTIVE];
        if ($newExpiryDate) {
            $subUpdate['ends_at'] = $newExpiryDate;
        }
        $userSubscription->update($subUpdate);

        if ($user) {
            $userUpdate = [
                'subscription' => 'Yes',
                'razorpay_subscription_id' => $subscriptionId,
                'cancelled_at' => null,
            ];
            if ($newExpiryDate) {
                $userUpdate['subscription_end_date'] = $newExpiryDate;
            }
            $user->update($userUpdate);
            SubscriptionController::cancelPreviousSubscriptionsForUser($user, $subscriptionId);

            SubscriptionNotificationHelper::sendNotification(
                'ACTIVATED',
                $user,
                $userSubscription->package_id,
                $subscriptionId,
                null,
                'Subscription Activated via Webhook'
            );
        }

        Log::info('── handleSubscriptionActivated END — success', [
            'user_subscription_id' => $userSubscription->id,
            'user_id' => $userSubscription->user_id
        ]);
    }

    /**
     * Handle subscription.charged event (successful renewal) 
     */
    private function handleSubscriptionCharged(Request $request)
    {
        Log::info('── handleSubscriptionCharged START ──');

        $payload = $request->all();
        $subscriptionId = $payload['payload']['subscription']['entity']['id'] ?? null;
        $paymentId = $payload['payload']['payment']['entity']['id'] ?? null;
        $paymentData = $payload['payload']['payment']['entity'] ?? [];

        if (!$subscriptionId || !$paymentId) {
            Log::error('Missing subscription_id or payment_id', ['subscription_id' => $subscriptionId, 'payment_id' => $paymentId]);
            return;
        }

        // Check for duplicate
        if (Transction::where('payment_id', $paymentId)->exists()) {
            Log::info('Duplicate payment skipped', ['payment_id' => $paymentId]);
            return;
        }

        // Find user subscription
        $userSubscription = UserSubscription::where('razorpay_subscription_id', $subscriptionId)
            ->latest()
            ->first();

        $subscriptionEntity = $payload['payload']['subscription']['entity'] ?? [];
        $api = $this->getRazorpayApi();
        $subscription = null;
        if ($api) {
            try {
                $subscription = $api->subscription->fetch($subscriptionId);
            } catch (\Throwable $eSubFetch) {
                Log::warning('handleSubscriptionCharged: Failed to fetch subscription via API: ' . $eSubFetch->getMessage());
            }
        }

        $packageIdFromNotes = (int) ($subscription['notes']['package_id'] ?? ($subscriptionEntity['notes']['package_id'] ?? 0));

        if (!$userSubscription) {
            $user = User::where('razorpay_subscription_id', $subscriptionId)->first();
            if (!$user) {
                $email = strtolower(trim($paymentData['email'] ?? ''));
                $contact = trim($paymentData['contact'] ?? '');
                if ($email || $contact) {
                    $user = User::where('email', $email)->orWhere('mobile_no', $contact)->first();
                }
            }
            if ($user) {
                $resolvedPkgId = $packageIdFromNotes ?: 1;
                $userSubscription = UserSubscription::updateOrCreate(
                    ['razorpay_subscription_id' => $subscriptionId],
                    [
                        'user_id' => $user->id,
                        'package_id' => $resolvedPkgId,
                        'status' => self::STATUS_ACTIVE,
                    ]
                );
            }
        }

        if (!$userSubscription) {
            Log::error('UserSubscription not found', ['subscription_id' => $subscriptionId]);
            return;
        }

        if ($packageIdFromNotes && $userSubscription->package_id != $packageIdFromNotes) {
            $userSubscription->update(['package_id' => $packageIdFromNotes]);
        }

        $user = User::find($userSubscription->user_id);
        if (!$user) {
            Log::error('User not found', ['user_id' => $userSubscription->user_id]);
            return;
        }

        // Get expiry date: prefer payload's current_end, fallback to API, fallback to +30 days
        $currentEndUnix = $subscriptionEntity['current_end'] ?? ($subscription['current_end'] ?? null);
        $newExpiryDate = !empty($currentEndUnix) 
            ? Carbon::createFromTimestamp($currentEndUnix)->format('Y-m-d H:i:s')
            : Carbon::now()->addDays(30)->format('Y-m-d H:i:s');

        // Check if this is the first payment (no existing active/complete transaction for this subscription)
        $existingTransaction = Transction::where('razorpay_subscription_id', $subscriptionId)
            ->whereIn('status', [self::STATUS_ACTIVE, self::STATUS_COMPLETED])
            ->first();

        $isFirstPayment = !$existingTransaction;

        if (!$isFirstPayment) {
            // Auto-renewal: Archive current active transaction
            Transction::where('user_id', $user->id)
                ->where('razorpay_subscription_id', $subscriptionId)
                ->where('status', self::STATUS_ACTIVE)
                ->update(['status' => self::STATUS_COMPLETED]);
        }

        // Create new transaction with status active
        $transaction = Transction::create([
            'user_id' => $user->id,
            'package_id' => $userSubscription->package_id,
            'amount' => ($paymentData['amount'] ?? 0) / 100,
            'currency_code' => $paymentData['currency'] ?? 'INR',
            'description' => $isFirstPayment ? 'First-time Subscription' : 'Subscription Renewal',
            'razorpay_subscription_id' => $subscriptionId,
            'payment_id' => $paymentId,
            'expiry_date' => $newExpiryDate,
            'transaction_count' => 1,
            'status' => self::STATUS_ACTIVE,
        ]);

        // Update user with new expiry
        $user->update([
            'subscription' => 'Yes',
            'razorpay_subscription_id' => $subscriptionId,
            'subscription_end_date' => $newExpiryDate,
        ]);

        // Ensure user subscription is active and ends_at is fresh on renewal
        $userSubscription->update([
            'status' => self::STATUS_ACTIVE,
            'ends_at' => $newExpiryDate,
        ]);

        SubscriptionNotificationHelper::sendNotification(
            'ACTIVATED',
            $user,
            $userSubscription->package_id,
            $subscriptionId,
            $paymentId,
            $isFirstPayment ? 'First-time Subscription Payment Charged' : 'Monthly Recurring Renewal Payment Charged',
            [
                'transaction_email' => $paymentData['email'] ?? null,
                'transaction_mobile' => $paymentData['contact'] ?? null,
                'expiry_date' => $newExpiryDate,
            ]
        );

        Log::info('── handleSubscriptionCharged END — success', [
            'user_id' => $user->id,
            'transaction_id' => $transaction->id,
            'expiry_date' => $newExpiryDate,
            'payment_type' => $isFirstPayment ? 'first_time' : 'renewal'
        ]);
    }

    /**
     * Handle subscription.pending event
     * Payment failed, retry attempts will be made
     */
    private function handleSubscriptionPending(Request $request)
    {
        Log::info('── handleSubscriptionPending START ──');

        $payload = $request->all();
        $subscriptionId = $payload['payload']['subscription']['entity']['id'] ?? null;
        $paymentEntity = $payload['payload']['payment']['entity'] ?? null;

        if (!$subscriptionId) {
            Log::error('Missing subscription_id', ['subscription_id' => $subscriptionId]);
            return;
        }

        // Find user subscription
        $userSubscription = UserSubscription::where('razorpay_subscription_id', $subscriptionId)
            ->latest()
            ->first();

        if (!$userSubscription) {
            Log::error('UserSubscription not found', ['subscription_id' => $subscriptionId]);
            return;
        }

        // Update subscription status to pending (we'll keep as active but mark for retry)
        $subPendingData = ['status' => self::STATUS_ACTIVE];
        if (\Illuminate\Support\Facades\Schema::hasColumn('user_subscriptions', 'retry_count')) {
            $subPendingData['retry_count'] = DB::raw('retry_count + 1');
        }
        $userSubscription->update($subPendingData);

        $user = User::find($userSubscription->user_id);

        SubscriptionNotificationHelper::sendNotification(
            'FAILED',
            $user,
            $userSubscription->package_id,
            $subscriptionId,
            $paymentEntity['id'] ?? null,
            'Monthly Recurring Payment Failed / Refused - Retry in progress',
            [
                'transaction_email' => $paymentEntity['email'] ?? null,
                'transaction_mobile' => $paymentEntity['contact'] ?? null,
            ]
        );

        // Log failure but don't change user access yet
        Log::warning('Subscription payment pending - retry in progress', [
            'subscription_id' => $subscriptionId,
            'user_id' => $userSubscription->user_id,
        ]);
    }

    /**
     * Handle subscription.halted event
     * All retry attempts exhausted, subscription is halted
     */
    private function handleSubscriptionHalted(Request $request)
    {
        Log::info('── handleSubscriptionHalted START ──');

        $payload = $request->all();
        $subscriptionId = $payload['payload']['subscription']['entity']['id'] ?? null;
        $paymentEntity = $payload['payload']['payment']['entity'] ?? null;

        if (!$subscriptionId) {
            Log::error('Missing subscription_id', ['subscription_id' => $subscriptionId]);
            return;
        }

        // Find user
        $user = User::where('razorpay_subscription_id', $subscriptionId)->first();
        if (!$user) {
            Log::warning('User not found for halted subscription', ['subscription_id' => $subscriptionId]);
            return;
        }

        // Update active transaction to failed
        Transction::where('user_id', $user->id)
            ->where('razorpay_subscription_id', $subscriptionId)
            ->where('status', self::STATUS_ACTIVE)
            ->update([
                'description' => 'Subscription Halted - Payment failed after retries',
                'status' => self::STATUS_HALTED,
                'failure_reason' => $paymentEntity['error_reason'] ?? 'Payment failed after retries',
            ]);

        // Update user subscription to No
        $user->update([
            'subscription' => 'No',
            'subscription_end_date' => now(),
            'razorpay_subscription_id' => null,
        ]);

        // Update user_subscription to halted
        UserSubscription::where('razorpay_subscription_id', $subscriptionId)
            ->update([
                'status' => self::STATUS_HALTED,
                'ends_at' => now(),
            ]);

        SubscriptionNotificationHelper::sendNotification(
            'PAUSED',
            $user,
            null,
            $subscriptionId,
            $paymentEntity['id'] ?? null,
            'Subscription Halted / Autopay Mandate Declined or Revoked',
            [
                'transaction_email' => $paymentEntity['email'] ?? null,
                'transaction_mobile' => $paymentEntity['contact'] ?? null,
            ]
        );

        Log::info('── handleSubscriptionHalted END — success', [
            'user_id' => $user->id,
            'subscription_id' => $subscriptionId
        ]);
    }

    /**
     * Handle subscription.paused event
     */
    private function handleSubscriptionPaused(Request $request)
    {
        Log::info('── handleSubscriptionPaused START ──');

        $payload = $request->all();
        $subscriptionId = $payload['payload']['subscription']['entity']['id'] ?? null;

        if (!$subscriptionId) {
            Log::error('Missing subscription_id', ['subscription_id' => $subscriptionId]);
            return;
        }

        // Find user
        $user = User::where('razorpay_subscription_id', $subscriptionId)->first();
        if (!$user) {
            Log::warning('User not found for paused subscription', ['subscription_id' => $subscriptionId]);
            return;
        }

        // Update user subscription status to paused
        $user->update([
            'subscription' => 'No', // Temporarily no access
            'subscription_end_date' => now(), // Set to now, as paused
            // Keep razorpay_subscription_id so handleSubscriptionResumed can identify user!
        ]);

        // Update user_subscription to paused
        UserSubscription::where('razorpay_subscription_id', $subscriptionId)
            ->update(['status' => self::STATUS_PAUSED]);
        
        Transction::where('user_id', $user->id)->where('razorpay_subscription_id', $subscriptionId)->update([
                'description' => 'Subscription Paused',
                'status' => self::STATUS_PAUSED,
        ]);
        // Clear device sessions
        DeviceSession::where('user_id', $user->id)->delete();

        SubscriptionNotificationHelper::sendNotification(
            'PAUSED',
            $user,
            null,
            $subscriptionId,
            null,
            'Subscription Paused via Webhook'
        );

        Log::info('── handleSubscriptionPaused END — success', [
            'user_id' => $user->id,
            'subscription_id' => $subscriptionId
        ]);
    }

    /**
     * Handle subscription.resumed event
     */
    private function handleSubscriptionResumed(Request $request)
    {
        Log::info('── handleSubscriptionResumed START ──');

        $payload = $request->all();
        $subscriptionId = $payload['payload']['subscription']['entity']['id'] ?? null;

        if (!$subscriptionId) {
            Log::error('Missing subscription_id', ['subscription_id' => $subscriptionId]);
            return;
        }

        // Find user
        $user = User::where('razorpay_subscription_id', $subscriptionId)->first();
        if (!$user) {
            $userSub = UserSubscription::where('razorpay_subscription_id', $subscriptionId)->latest()->first();
            if ($userSub) {
                $user = User::find($userSub->user_id);
            }
        }
        if (!$user) {
            Log::warning('User not found for resumed subscription', ['subscription_id' => $subscriptionId]);
            return;
        }

        // Get expiry date: prefer payload's current_end, fallback to API, fallback to +30 days
        $subEntity = $payload['payload']['subscription']['entity'] ?? [];
        $currentEndUnix = $subEntity['current_end'] ?? null;
        if (!$currentEndUnix) {
            $api = $this->getRazorpayApi();
            if ($api) {
                try {
                    $subscription = $api->subscription->fetch($subscriptionId);
                    $currentEndUnix = $subscription['current_end'] ?? null;
                } catch (\Throwable $e) {
                    Log::warning('handleSubscriptionResumed: Could not fetch subscription from API: ' . $e->getMessage());
                }
            }
        }
        $newExpiryDate = !empty($currentEndUnix)
            ? Carbon::createFromTimestamp($currentEndUnix)->format('Y-m-d H:i:s')
            : Carbon::now()->addDays(30)->format('Y-m-d H:i:s');

        // Update user subscription to Yes
        $user->update([
            'subscription' => 'Yes',
            'razorpay_subscription_id' => $subscriptionId,
            'subscription_end_date' => $newExpiryDate,
        ]);

        // Update user_subscription to active
        UserSubscription::where('razorpay_subscription_id', $subscriptionId)
            ->update([
                'status' => self::STATUS_ACTIVE,
                'ends_at' => $newExpiryDate,
            ]);

        SubscriptionNotificationHelper::sendNotification(
            'ACTIVATED',
            $user,
            null,
            $subscriptionId,
            null,
            'Subscription Resumed via Webhook',
            ['expiry_date' => $newExpiryDate]
        );

        Log::info('── handleSubscriptionResumed END — success', [
            'user_id' => $user->id,
            'subscription_id' => $subscriptionId
        ]);
    }

    /**
     * Handle subscription.cancelled event
     */
    private function handleSubscriptionCancelled(Request $request)
    {
        Log::info('── handleSubscriptionCancelled START ──');

        $payload = $request->all();
        $subEntity = $payload['payload']['subscription']['entity'] ?? [];
        $subscriptionId = $subEntity['id'] ?? null;

        if (!$subscriptionId) {
            Log::error('Missing subscription_id in handleSubscriptionCancelled');
            return;
        }

        Log::info('Processing subscription.cancelled webhook', ['subscription_id' => $subscriptionId]);

        // 1. ALWAYS update UserSubscription record status to STATUS_CANCELLED
        $userSubCount = UserSubscription::where('razorpay_subscription_id', $subscriptionId)
            ->update([
                'status' => self::STATUS_CANCELLED,
                'ends_at' => now(),
            ]);

        Log::info('Updated UserSubscription records status to STATUS_CANCELLED (2)', [
            'subscription_id' => $subscriptionId,
            'updated_count' => $userSubCount
        ]);

        // 2. Find User by UserSubscription, or by users.razorpay_subscription_id, or by notes.user_id
        $user = null;
        $userSub = UserSubscription::where('razorpay_subscription_id', $subscriptionId)->latest()->first();
        if ($userSub) {
            $user = User::find($userSub->user_id);
        }
        if (!$user) {
            $user = User::where('razorpay_subscription_id', $subscriptionId)->first();
        }
        if (!$user && isset($subEntity['notes']['user_id'])) {
            $user = User::find($subEntity['notes']['user_id']);
        }

        if ($user) {
            // Update active transaction to cancelled
            Transction::where('user_id', $user->id)
                ->where('razorpay_subscription_id', $subscriptionId)
                ->update([
                    'description' => 'Subscription Cancelled',
                    'status' => self::STATUS_CANCELLED,
                    'cancelled_at' => now(),
                ]);

            // Update user profile if this was their active subscription
            if ($user->razorpay_subscription_id === $subscriptionId) {
                $user->update([
                    'subscription' => 'No',
                    'subscription_end_date' => now(),
                    'razorpay_subscription_id' => null,
                    'cancelled_at' => now(),
                ]);
            }
            DeviceSession::where('user_id', $user->id)->delete();

            SubscriptionNotificationHelper::sendNotification(
                'CANCELLED',
                $user,
                $userSub?->package_id,
                $subscriptionId,
                null,
                'Subscription Cancelled via Webhook'
            );
            Log::info('── handleSubscriptionCancelled END — user updated', ['user_id' => $user->id]);
        } else {
            Log::warning('User object not found for cancelled subscription ID: ' . $subscriptionId . ' (UserSubscription table updated to status 2)');
        }
    }

    /**
     * Handle subscription.completed event (plan completed all cycles)
     */
    private function handleSubscriptionCompleted(Request $request)
    {
        Log::info('── handleSubscriptionCompleted START ──');

        $payload = $request->all();
        $subEntity = $payload['payload']['subscription']['entity'] ?? [];
        $subscriptionId = $subEntity['id'] ?? null;

        if (!$subscriptionId) {
            Log::error('Missing subscription_id in handleSubscriptionCompleted');
            return;
        }

        // 1. Update UserSubscription record to COMPLETED
        UserSubscription::where('razorpay_subscription_id', $subscriptionId)
            ->update(['status' => self::STATUS_COMPLETED]);

        // 2. Find User
        $user = null;
        $userSub = UserSubscription::where('razorpay_subscription_id', $subscriptionId)->latest()->first();
        if ($userSub) {
            $user = User::find($userSub->user_id);
        }
        if (!$user) {
            $user = User::where('razorpay_subscription_id', $subscriptionId)->first();
        }
        if (!$user && isset($subEntity['notes']['user_id'])) {
            $user = User::find($subEntity['notes']['user_id']);
        }

        if ($user) {
            Transction::where('user_id', $user->id)
                ->where('razorpay_subscription_id', $subscriptionId)
                ->update([
                    'description' => 'Subscription Completed - All cycles finished',
                    'status' => self::STATUS_COMPLETED,
                ]);

            if ($user->razorpay_subscription_id === $subscriptionId) {
                $user->update([
                    'subscription' => 'No',
                    'subscription_end_date' => now(),
                    'razorpay_subscription_id' => null,
                ]);
            }
            DeviceSession::where('user_id', $user->id)->delete();
            Log::info('── handleSubscriptionCompleted END — success', ['user_id' => $user->id]);
        }
    }

    /**
     * Handle subscription.updated event
     */
    private function handleSubscriptionUpdated(Request $request)
    {
        Log::info('── handleSubscriptionUpdated START ──');

        $payload = $request->all();
        $subscriptionData = $payload['payload']['subscription']['entity'] ?? null;
        $subscriptionId = $subscriptionData['id'] ?? null;

        if (!$subscriptionId) {
            Log::error('Missing subscription_id', ['subscription_id' => $subscriptionId]);
            return;
        }

        // Find user subscription
        $userSubscription = UserSubscription::where('razorpay_subscription_id', $subscriptionId)
            ->latest()
            ->first();

        if (!$userSubscription) {
            Log::error('UserSubscription not found', ['subscription_id' => $subscriptionId]);
            return;
        }

        // Update subscription details if needed
        // For example, if plan changed, update package_id
        // You can fetch plan details and update accordingly

        Log::info('── handleSubscriptionUpdated END — success', [
            'user_subscription_id' => $userSubscription->id,
            'subscription_id' => $subscriptionId
        ]);
    }

    /**
     * Handle invoice.paid event (alternative renewal webhook)
     */
    private function handleInvoicePaid(Request $request)
    {
        Log::info('── handleInvoicePaid START ──');

        $payload = $request->all();
        $subscriptionData = $payload['payload']['subscription']['entity'] ?? null;
        $paymentData = $payload['payload']['payment']['entity'] ?? null;

        $subscriptionId = $subscriptionData['id'] ?? null;
        $paymentId = $paymentData['id'] ?? null;

        if (!$subscriptionId || !$paymentId) {
            Log::warning('Missing subscription_id or payment_id', ['subscription_id' => $subscriptionId, 'payment_id' => $paymentId]);
            return;
        }

        // Check for duplicate
        if (Transction::where('payment_id', $paymentId)->exists()) {
            Log::info('Duplicate invoice.paid skipped', ['payment_id' => $paymentId]);
            return;
        }

        // Find user
        $user = User::where('razorpay_subscription_id', $subscriptionId)->first();
        if (!$user) {
            Log::error('User not found for subscription', ['subscription_id' => $subscriptionId]);
            return;
        }

        // Find active user subscription
        $userSubscription = UserSubscription::where('razorpay_subscription_id', $subscriptionId)
            ->where('status', self::STATUS_ACTIVE)
            ->first();

        if (!$userSubscription) {
            Log::error('Active UserSubscription not found', ['subscription_id' => $subscriptionId]);
            return;
        }

        // Get expiry date: prefer payload's current_end, fallback to API, fallback to +30 days
        $currentEndUnix = $subscriptionData['current_end'] ?? null;
        if (!$currentEndUnix) {
            $api = $this->getRazorpayApi();
            if ($api) {
                try {
                    $subscription = $api->subscription->fetch($subscriptionId);
                    $currentEndUnix = $subscription['current_end'] ?? null;
                } catch (\Throwable $e) {
                    Log::warning('handleInvoicePaid: Could not fetch subscription from API: ' . $e->getMessage());
                }
            }
        }
        $newExpiryDate = !empty($currentEndUnix)
            ? Carbon::createFromTimestamp($currentEndUnix)->format('Y-m-d H:i:s')
            : Carbon::now()->addDays(30)->format('Y-m-d H:i:s');

        // Archive current active transaction
        Transction::where('user_id', $user->id)
            ->where('razorpay_subscription_id', $subscriptionId)
            ->where('status', self::STATUS_ACTIVE)
            ->update(['status' => self::STATUS_COMPLETED]);

        // Create new transaction
        $transaction = Transction::create([
            'user_id' => $user->id,
            'package_id' => $userSubscription->package_id,
            'amount' => ($paymentData['amount'] ?? 0) / 100,
            'currency_code' => $paymentData['currency'] ?? 'INR',
            'description' => 'Subscription',
            'razorpay_subscription_id' => $subscriptionId,
            'payment_id' => $paymentId,
            'expiry_date' => $newExpiryDate,
            'transaction_count' => 1,
            'status' => self::STATUS_ACTIVE,
        ]);

        // Extend user subscription
        $user->update([
            'subscription' => 'Yes',
            'razorpay_subscription_id' => $subscriptionId,
            'subscription_end_date' => $newExpiryDate,
        ]);

        Log::info('── handleInvoicePaid END — success', [
            'user_id' => $user->id,
            'transaction_id' => $transaction->id,
            'expiry_date' => $newExpiryDate
        ]);
    }

    /**
     * Handle payment.failed event
     */
    private function handlePaymentFailed(Request $request)
    {
        Log::info('── handlePaymentFailed START ──');

        $payload = $request->all();
        $paymentEntity = $payload['payload']['payment']['entity'] ?? null;
        $paymentId = $paymentEntity['id'] ?? null;

        $failureReason = $paymentEntity['error_reason'] ?? 'Payment failed';
        $errorCode = $paymentEntity['error_code'] ?? '';
        $errorDescription = $paymentEntity['error_description'] ?? '';
        $errorSource = $paymentEntity['error_source'] ?? '';
        $errorStep = $paymentEntity['error_step'] ?? '';
        $method = $paymentEntity['method'] ?? '';

        // Log the raw decline details up front, before any lookup can fail
        // and bail out. This is the actual root-cause info from Razorpay/the
        // bank — payment.failed always carries this, regardless of whether
        // we can resolve which subscription it belongs to.
        Log::warning('payment.failed decline details', [
            'payment_id' => $paymentId,
            'method' => $method,
            'error_code' => $errorCode,
            'error_description' => $errorDescription,
            'error_reason' => $failureReason,
            'error_source' => $errorSource,
            'error_step' => $errorStep,
        ]);

        // IMPORTANT: unlike subscription.charged / subscription.authenticated,
        // Razorpay's payment.failed event does NOT include a top-level
        // subscription entity — only payload.payment.entity is guaranteed.
        // Try it anyway (harmless if absent), then fall back to resolving
        // the subscription via the payment's invoice.
        $subscriptionId = $payload['payload']['subscription']['entity']['id'] ?? null;

        if (!$subscriptionId && !empty($paymentEntity['invoice_id'])) {
            try {
                $api = $this->getRazorpayApi();
                $invoice = $api->invoice->fetch($paymentEntity['invoice_id']);
                $subscriptionId = $invoice['subscription_id'] ?? null;
            } catch (\Exception $e) {
                Log::warning('Could not resolve subscription from invoice', [
                    'invoice_id' => $paymentEntity['invoice_id'],
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if (!$subscriptionId) {
            // We still couldn't tie this to a subscription (e.g. it may be a
            // one-off/order payment, not a subscription authorization). The
            // decline details above are already logged, so nothing further
            // to update — just stop here instead of throwing.
            Log::info('payment.failed had no resolvable subscription_id — logged decline details only', [
                'payment_id' => $paymentId,
            ]);
            return;
        }

        // ──────────────────────────────────────────────────────────────────
        // CRITICAL FIX: Before taking ANY destructive action, verify the
        // subscription's ACTUAL status on Razorpay. A payment.failed event
        // may arrive for a timed-out initial UPI attempt AFTER a subsequent
        // successful payment has already activated the subscription.
        // ──────────────────────────────────────────────────────────────────
        $rzpSubscriptionStatus = null;
        try {
            $api = $this->getRazorpayApi();
            $rzpSubscription = $api->subscription->fetch($subscriptionId);
            $rzpSubscriptionStatus = strtolower($rzpSubscription->status ?? '');
            Log::info('payment.failed — verified Razorpay subscription status', [
                'subscription_id' => $subscriptionId,
                'rzp_status' => $rzpSubscriptionStatus,
            ]);
        } catch (\Exception $e) {
            Log::warning('payment.failed — could not verify Razorpay subscription status', [
                'subscription_id' => $subscriptionId,
                'error' => $e->getMessage(),
            ]);
        }

        // If Razorpay confirms the subscription is active/authenticated,
        // the failed payment was just a transient attempt (e.g. UPI timeout
        // on first try, before user retried successfully). Do NOT cancel.
        if (in_array($rzpSubscriptionStatus, ['active', 'authenticated'])) {
            Log::info('payment.failed IGNORED — subscription is still active on Razorpay, not deactivating', [
                'subscription_id' => $subscriptionId,
                'rzp_status' => $rzpSubscriptionStatus,
                'payment_id' => $paymentId,
                'failure_reason' => $failureReason,
            ]);
            return;
        }

        // Find user — also look via UserSubscription in case users.razorpay_subscription_id was already cleared
        $user = User::where('razorpay_subscription_id', $subscriptionId)->first();
        if (!$user) {
            $userSub = UserSubscription::where('razorpay_subscription_id', $subscriptionId)->latest()->first();
            if ($userSub) {
                $user = User::find($userSub->user_id);
            }
        }
        if (!$user) {
            Log::warning('User not found for failed payment', ['subscription_id' => $subscriptionId]);
            return;
        }

        // At this point Razorpay says the subscription is NOT active
        // (halted/cancelled/expired/pending). Update local records accordingly.
        $transaction = Transction::where('user_id', $user->id)
            ->where('razorpay_subscription_id', $subscriptionId)
            ->where('status', self::STATUS_ACTIVE)
            ->first();

        if ($transaction) {
            $txUpdate = [
                'description' => 'Payment Failed - ' . ($rzpSubscriptionStatus === 'halted' ? 'All retries exhausted' : 'Retry in progress'),
                'status' => self::STATUS_ACTIVE, // Keep active during retries
                'failure_reason' => $failureReason . ' (Code: ' . $errorCode . ')',
            ];
            if (\Illuminate\Support\Facades\Schema::hasColumn('transaction', 'retry_count')) {
                $txUpdate['retry_count'] = DB::raw('retry_count + 1');
            }
            $transaction->update($txUpdate);
        }

        $userSubscription = UserSubscription::where('razorpay_subscription_id', $subscriptionId)->first();
        if ($userSubscription) {
            $subUpdate = [
                'status' => self::STATUS_ACTIVE, // Keep active during retries
            ];
            if (\Illuminate\Support\Facades\Schema::hasColumn('user_subscriptions', 'retry_count')) {
                $subUpdate['retry_count'] = DB::raw('retry_count + 1');
            }
            $userSubscription->update($subUpdate);
        }

        // Only fully deactivate if Razorpay confirms halted/cancelled/expired
        if (in_array($rzpSubscriptionStatus, ['halted', 'cancelled', 'expired'])) {
            Log::warning('payment.failed — Razorpay subscription is ' . $rzpSubscriptionStatus . ', deactivating user', [
                'user_id' => $user->id,
                'subscription_id' => $subscriptionId,
            ]);

            $user->update([
                'subscription' => 'No',
                'subscription_end_date' => now(),
                'razorpay_subscription_id' => null,
                'cancelled_at' => now(),
            ]);

            if ($transaction) {
                $transaction->update(['status' => self::STATUS_HALTED]);
            }
            if ($userSubscription) {
                $userSubscription->update(['status' => self::STATUS_HALTED]);
            }

            DeviceSession::where('user_id', $user->id)->delete();
        }

        // Send FAILED notification (informational — retry in progress or halted)
        SubscriptionNotificationHelper::sendNotification(
            'FAILED',
            $user,
            $userSubscription?->package_id,
            $subscriptionId,
            $paymentId,
            "Payment Failed: {$errorDescription} ({$failureReason})",
            [
                'transaction_email' => $paymentEntity['email'] ?? null,
                'transaction_mobile' => $paymentEntity['contact'] ?? null,
            ]
        );

        Log::warning('Payment failed webhook processed', [
            'user_id' => $user->id,
            'subscription_id' => $subscriptionId,
            'rzp_status' => $rzpSubscriptionStatus,
            'failure_reason' => $failureReason
        ]);
    }

    /**
     * Helper method to get subscription details from Razorpay
     */
    private function getSubscriptionDetails($subscriptionId)
    {
        try {
            $api = $this->getRazorpayApi();
            if (!$api) {
                return null;
            }
            return $api->subscription->fetch($subscriptionId);
        } catch (\Throwable $e) {
            Log::error('Failed to fetch subscription from Razorpay', [
                'subscription_id' => $subscriptionId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}