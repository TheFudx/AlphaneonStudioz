<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Video;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Notification;
use App\Models\Watchlists;
use App\Models\Package;
use App\Models\Package_Details;
use App\Models\Transction;
use App\Models\Type;
use App\Models\Device;
use App\Models\DeviceSession;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\UserSubscription;
use App\Models\FestivalCompetition;
use Carbon\Carbon;
use App\Helpers\Helpers;
use App\Services\PayUService;
use App\Helpers\SubscriptionNotificationHelper;
class SubscriptionController extends Controller
{
    protected PayUService $payU;
    public function __construct(PayUService $payU)
    {
        $this->payU = $payU;
    }
    public function index(Request $request)
    {
        $user = auth()->user();
        $userId = $user?->id ?? session('user_id');
        $userIdInt = $userId ? (int) $userId : null;
        $allowedUserIds = [443, 1112, 1406, 680, 545, 1293, 1365, 1664];

        $watchlist = app('wishlist');
        session(['watchlist' => $watchlist]);

        $packageQuery = Package::where('is_delete', 0)->where('status', 1);
        if (!$userIdInt || !in_array($userIdInt, $allowedUserIds)) {
            $packageQuery->where(function ($q) {
                $q->where('price', '>', 2)
                  ->whereNotIn('id', [4, 5]);
            });
        }
        $package = $packageQuery->get();
        $activeSubscription = false;
        $activePackageId    = null;
        // ✅ Only run user-specific queries if logged in
        if ($user) {
            $user_subscription = UserSubscription::with('user')
                ->whereHas('user', function ($q) {
                    $q->where('subscription', 'Yes');
                })
                ->where('user_id', $user->id)
                ->where('status', 1)
                ->first();
            $activePackageId = $user_subscription ? $user_subscription->package_id : null;
            try {
                if (DB::getSchemaBuilder()->hasColumn('transaction', 'expiry_date')) {
                    $activeSubscription = Transction::where('user_id', $user->id)->where('status',1)
                        ->where('expiry_date', '>', now())
                        ->exists();
                }
            } catch (\Exception $e) {
                Log::error('Subscription check error: ' . $e->getMessage());
            }
        }

        // Check if user has already taken / purchased a ₹15 plan
        $hasTaken15Plan = false;
        if ($userIdInt) {
            try {
                $package15Ids = Package::where('price', 15)->pluck('id')->toArray();

                $hasTx15 = Transction::where('user_id', $userIdInt)
                    ->where(function ($q) use ($package15Ids) {
                        if (!empty($package15Ids)) {
                            $q->whereIn('package_id', $package15Ids);
                        }
                        $q->orWhere('amount', 15)
                          ->orWhere('amount', '15')
                          ->orWhere('amount', '15.00')
                          ->orWhere('amount', '15.0');
                    })
                    ->where(function ($q) {
                        $q->whereIn('status', [1, 3, 4, 5, 6])
                          ->orWhereNotNull('payment_id');
                    })
                    ->exists();

                $hasSub15 = false;
                if (!empty($package15Ids)) {
                    $hasSub15 = UserSubscription::where('user_id', $userIdInt)
                        ->whereIn('package_id', $package15Ids)
                        ->where('status', '!=', 2)
                        ->exists();
                }

                $isActive15 = false;
                if ($activePackageId && !empty($package15Ids)) {
                    $isActive15 = in_array($activePackageId, $package15Ids);
                }

                $hasTaken15Plan = $hasTx15 || $hasSub15 || $isActive15;
            } catch (\Exception $e) {
                Log::error('15 plan check error: ' . $e->getMessage());
            }
        }

        if ($hasTaken15Plan) {
            $package = $package->reject(function ($p) {
                return (int) $p->price === 15;
            })->values();
        }

        $typeIds = $package->pluck('type_id')
            ->flatMap(function ($ids) {
                return explode(',', $ids);
            })
            ->unique()
            ->toArray();
        $typeData = Type::whereIn('id', $typeIds)->get();

        $amount = $activeSubscription ? 15 : 149;
        return view('subscription', compact(
            'user', 'activePackageId', 'watchlist',
            'package', 'typeData', 'activeSubscription', 'amount'
        ));
    }
    
    // Status Constants (matching webhook)
    const STATUS_CREATED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_FAILED = 2;
    const STATUS_CANCELLED = 3;
    const STATUS_COMPLETED = 4;
    const STATUS_HALTED = 5;
    const STATUS_PAUSED = 6;

    // How long (in minutes) a "created/pending" subscription may be reused
    // before we treat its authentication attempt (and QR) as stale and
    // cancel + recreate instead. Razorpay's checkout re-serves the same
    // pending authentication transaction for a given subscription_id, so
    // reusing indefinitely means a dead QR never gets refreshed.
    const SUBSCRIPTION_REUSE_WINDOW_MINUTES = 15;

    /**
     * Create a new subscription
     * Frontend: User clicks subscribe button
     */
    public function createSubscription(Request $request)
    {
        $user = auth()->user();
        $planId = $request->plan_id;
        $packageId = $request->package_id;
        Log::info('── createSubscription START ──', ['user_id' => $user?->id]);

        // 1. Fetch credentials with fallback to both config and .env
        $razorpayKey = config('app.razorpay_key') ?: env('RAZORPAY_KEY');
        $razorpaySecret = config('app.razorpay_secret') ?: env('RAZORPAY_SECRET');

        if (empty($razorpayKey) || empty($razorpaySecret)) {
            Log::error('createSubscription — Razorpay credentials missing. Check .env and config/app.php', [
                'has_key' => !empty($razorpayKey),
                'has_secret' => !empty($razorpaySecret),
            ]);
            return response()->json([
                'error' => true,
                'message' => 'Payment gateway credentials are not configured properly.',
                'details' => 'Razorpay Key or Secret is missing.',
            ], 500);
        }

        try {
            $totalCount = match ((int) $packageId) {
                2 => 120,
                3 => 20,
                default => 12,
            };
            if ($user) {
                // Check for existing pending subscription
                $existing = UserSubscription::where('user_id', $user->id)
                    ->where('package_id', $packageId)
                    ->where('status', self::STATUS_CREATED)
                    ->latest()
                    ->first();
                if ($existing) {
                    $api = new Api($razorpayKey, $razorpaySecret);
                    $subscription = $api->subscription->fetch($existing->razorpay_subscription_id);

                    $minutesOld = $existing->created_at->diffInMinutes(now());
                    $isReusable = in_array($subscription->status, ['created', 'pending'])
                        && $minutesOld < self::SUBSCRIPTION_REUSE_WINDOW_MINUTES;

                    if ($isReusable) {
                        Log::info('── createSubscription END — reused existing Subscription: ' . $subscription->id . '──', [
                            'minutes_old' => $minutesOld,
                        ]);
                        return response()->json([
                            'subscription_id' => $subscription->id,
                            'status' => 'existing',
                            'message' => 'Using existing pending subscription.',
                        ]);
                    }

                    if (in_array($subscription->status, ['created', 'pending'])) {
                        try {
                            $subscription->cancel();
                            Log::info('Cancelled stale pending subscription before recreating', [
                                'subscription_id' => $subscription->id,
                                'minutes_old' => $minutesOld,
                            ]);
                        } catch (\Exception $e) {
                            Log::warning('Failed to cancel stale subscription (continuing): ' . $e->getMessage());
                        }
                    }
                    $existing->update(['status' => self::STATUS_FAILED]);
                }
            }

            // Create new subscription using verified credentials
            $api = new Api($razorpayKey, $razorpaySecret);
            $newSubscription = $api->subscription->create([
                'plan_id' => $planId,
                'customer_notify' => 1,
                'total_count' => $totalCount,
                'quantity' => 1,
            ]);

            if ($user) {
                UserSubscription::create([
                    'user_id' => $user->id,
                    'razorpay_subscription_id' => $newSubscription['id'],
                    'package_id' => $packageId,
                    'status' => self::STATUS_CREATED,
                    'starts_at' => null,
                    'ends_at' => null,
                ]);
            }

            Log::info('── createSubscription END — success ──');
            return response()->json([
                'subscription_id' => $newSubscription['id'],
                'status' => 'created',
                'message' => 'New subscription created.',
            ]);
        } catch (\Exception $e) {
            Log::error('createSubscription failed: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Failed to create subscription.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store transaction after payment (web callback from frontend)
     * This is called when Razorpay redirects back after successful payment
     * Note: Webhook will also process this, so we check for duplicates
     */
    public function storeTransaction(Request $request)
    {
        Log::info('── storeTransaction START ──');

        $razorpayKey = config('app.razorpay_key') ?: env('RAZORPAY_KEY');
        $razorpaySecret = config('app.razorpay_secret') ?: env('RAZORPAY_SECRET');

        if (empty($razorpayKey) || empty($razorpaySecret)) {
            Log::error('storeTransaction — RAZORPAY_KEY or RAZORPAY_SECRET is empty/null. Check .env and config/app.php');
        }

        $api = new Api($razorpayKey, $razorpaySecret);

        $user = auth()->user();
        $rawSubId = $request->razorpay_subscription_id;
        $subscriptionId = (!empty($rawSubId) && !in_array(strtolower(trim($rawSubId)), ['undefined', 'null', ''])) 
            ? trim($rawSubId) 
            : null;
        $paymentId = $request->razorpay_payment_id ? trim($request->razorpay_payment_id) : null;
        $signature = $request->razorpay_signature ? trim($request->razorpay_signature) : null;

        // Early user recovery if session is lost
        if (!$user && $request->filled('user_id')) {
            $user = User::find($request->user_id);
        }
        if (!$user && $subscriptionId) {
            $existingSub = UserSubscription::where('razorpay_subscription_id', $subscriptionId)->latest()->first();
            if ($existingSub && $existingSub->user_id) {
                $user = User::find($existingSub->user_id);
            }
        }
        if (!$user && $subscriptionId) {
            $user = User::where('razorpay_subscription_id', $subscriptionId)->first();
        }
        if (!$user && $paymentId) {
            $existingTxn = Transction::where('payment_id', $paymentId)->first();
            if ($existingTxn && $existingTxn->user_id) {
                $user = User::find($existingTxn->user_id);
            }
        }

        // Restore auth session if user was recovered
        if ($user && !auth()->check()) {
            auth()->login($user);
            Log::info('storeTransaction — restored auth session for user_id: ' . $user->id);
        }

        Log::info('storeTransaction — received data', [
            'user_id' => $user?->id,
            'raw_subscription_id' => $rawSubId,
            'subscription_id' => $subscriptionId,
            'payment_id' => $paymentId,
            'signature_length' => strlen($signature ?? ''),
            'signature_present' => !empty($signature),
            'package_id' => $request->package_id,
        ]);

        // 1. FAST PATH A: Check if webhook already recorded this payment
        if ($paymentId && Transction::where('payment_id', $paymentId)->exists()) {
            Log::info('storeTransaction — payment already recorded in transaction table. Redirecting to thank-you.', [
                'payment_id' => $paymentId,
            ]);
            return redirect()->route('subscribe.thankyou')
                ->with('message', 'Subscription activated successfully!');
        }

        // 1. FAST PATH B: Check if user subscription is already active with future expiry
        if ($user && $user->subscription === 'Yes' && !empty($user->subscription_end_date) && Carbon::parse($user->subscription_end_date)->isFuture()) {
            Log::info('storeTransaction — user subscription already active in users table. Redirecting to thank-you.', [
                'user_id' => $user->id,
                'subscription_end_date' => $user->subscription_end_date,
            ]);
            return redirect()->route('subscribe.thankyou')
                ->with('message', 'Subscription activated successfully!');
        }

        // Fetch payment details early if available
        $payment = null;
        if ($paymentId) {
            try {
                $payment = $api->payment->fetch($paymentId);
                if (!$subscriptionId && !empty($payment->invoice_id)) {
                    $invoice = $api->invoice->fetch($payment->invoice_id);
                    if (!empty($invoice->subscription_id)) {
                        $subscriptionId = $invoice->subscription_id;
                        Log::info('storeTransaction — recovered subscription_id from invoice: ' . $subscriptionId);
                    }
                }
            } catch (\Exception $fetchPaymentEx) {
                Log::warning('storeTransaction — payment fetch error: ' . $fetchPaymentEx->getMessage());
            }
        }

        // Verify signature
        $signatureVerified = false;
        if ($subscriptionId && $paymentId && $signature) {
            try {
                $api->utility->verifyPaymentSignature([
                    'razorpay_subscription_id' => $subscriptionId,
                    'razorpay_payment_id' => $paymentId,
                    'razorpay_signature' => $signature,
                ]);
                $signatureVerified = true;
                Log::info('storeTransaction — signature verified successfully');
            } catch (\Razorpay\Api\Errors\SignatureVerificationError $e) {
                // Secondary check with alternate attribute naming if standard fails
                try {
                    $expectedSignature = hash_hmac('sha256', $paymentId . '|' . $subscriptionId, $razorpaySecret);
                    if (hash_equals($expectedSignature, $signature)) {
                        $signatureVerified = true;
                        Log::info('storeTransaction — signature verified via direct hmac check');
                    } else {
                        Log::warning('storeTransaction — signature verification failed: ' . $e->getMessage());
                    }
                } catch (\Exception $sigEx) {
                    Log::warning('storeTransaction — signature hash check error: ' . $sigEx->getMessage());
                }
            }
        }

        // Verify validity via payment or subscription status
        $isValid = $signatureVerified;

        if (!$isValid && $paymentId) {
            if (!$payment) {
                try {
                    $payment = $api->payment->fetch($paymentId);
                } catch (\Exception $apiFetchEx) {
                    Log::error('storeTransaction — API fetch failed: ' . $apiFetchEx->getMessage());
                }
            }

            if ($payment && in_array($payment->status, ['authorized', 'captured'])) {
                $isValid = true;
                Log::info('storeTransaction — payment confirmed valid via Razorpay API status', [
                    'payment_id' => $paymentId,
                    'status' => $payment->status,
                ]);
            }
        }

        // Subscription-level verification directly from Razorpay API
        $rzpSubscription = null;
        if (!$isValid && $subscriptionId) {
            try {
                $rzpSubscription = $api->subscription->fetch($subscriptionId);
                $rzpSubStatus = strtolower($rzpSubscription->status ?? '');
                if (in_array($rzpSubStatus, ['active', 'authenticated'])) {
                    $isValid = true;
                    Log::info('storeTransaction — subscription confirmed active/authenticated via Razorpay API', [
                        'subscription_id' => $subscriptionId,
                        'rzp_status' => $rzpSubStatus,
                    ]);
                }
            } catch (\Exception $subFetchEx) {
                Log::warning('storeTransaction — subscription fetch error: ' . $subFetchEx->getMessage());
            }
        }

        // Database status check
        if (!$isValid) {
            $isAlreadyActive = false;
            if ($paymentId && Transction::where('payment_id', $paymentId)->exists()) {
                $isAlreadyActive = true;
            } elseif ($subscriptionId && Transction::where('razorpay_subscription_id', $subscriptionId)->where('status', self::STATUS_ACTIVE)->exists()) {
                $isAlreadyActive = true;
            } elseif ($user && $user->subscription === 'Yes') {
                $isAlreadyActive = true;
            } elseif ($subscriptionId && UserSubscription::where('razorpay_subscription_id', $subscriptionId)->where('status', self::STATUS_ACTIVE)->exists()) {
                $isAlreadyActive = true;
            }

            if ($isAlreadyActive) {
                Log::info('storeTransaction — already recorded as active in database. Redirecting to thank-you.');
                return redirect()->route('subscribe.thankyou')
                    ->with('message', 'Subscription activated successfully!');
            }

            Log::error('storeTransaction — payment could not be verified via signature, payment, or subscription API', [
                'subscription_id' => $subscriptionId,
                'payment_id' => $paymentId,
            ]);
            return redirect()->route('errorPage')
                ->with('error', 'Payment verification failed. Please contact support.');
        }

        try {
            // Check duplicate again
            if ($payment && Transction::where('payment_id', $payment->id)->exists()) {
                Log::info('Duplicate payment detected, already processed', ['payment_id' => $payment->id]);
                return redirect()->route('subscribe.thankyou')
                    ->with('message', 'Subscription activated successfully!');
            }

            if (!$user && $payment) {
                $rzpEmail = strtolower(trim($payment->email ?? ''));
                $rzpContact = trim($payment->contact ?? '');
                if ($rzpEmail || $rzpContact) {
                    $user = User::where('email', $rzpEmail)->orWhere('mobile_no', $rzpContact)->first();
                }
            }

            if (!$user && $subscriptionId) {
                $user = User::where('razorpay_subscription_id', $subscriptionId)->first();
            }

            if (!$user) {
                throw new \Exception('User session missing and could not identify user from payment details.');
            }

            if (!auth()->check()) {
                auth()->login($user);
            }

            // Clean up any corrupt "undefined" records for this user
            UserSubscription::where('user_id', $user->id)
                ->whereIn('razorpay_subscription_id', ['undefined', 'null', ''])
                ->delete();

            // Find or create user subscription record
            $userSubscription = null;
            if ($subscriptionId) {
                $userSubscription = UserSubscription::where('razorpay_subscription_id', $subscriptionId)
                    ->where('user_id', $user->id)
                    ->first();
            }

            $packageId = $request->package_id;
            if (!$packageId && $userSubscription) {
                $packageId = $userSubscription->package_id;
            }
            if (!$packageId && $subscriptionId) {
                try {
                    $subData = $rzpSubscription ?: $api->subscription->fetch($subscriptionId);
                    $packageId = $subData['notes']['package_id'] ?? null;
                } catch (\Exception $e) {}
            }
            $packageId = $packageId ?? 1;

            if (!$userSubscription) {
                $userSubscription = UserSubscription::where('user_id', $user->id)
                    ->where('package_id', $packageId)
                    ->latest()
                    ->first();
            }

            if (!$userSubscription) {
                $userSubscription = UserSubscription::create([
                    'user_id' => $user->id,
                    'package_id' => $packageId,
                    'razorpay_subscription_id' => $subscriptionId,
                    'status' => self::STATUS_CREATED,
                ]);
            } elseif ($subscriptionId && (empty($userSubscription->razorpay_subscription_id) || in_array($userSubscription->razorpay_subscription_id, ['undefined', 'null']))) {
                $userSubscription->update(['razorpay_subscription_id' => $subscriptionId]);
            }

            $package = Package::find($userSubscription->package_id) ?? Package::find($request->package_id) ?? Package::find($packageId) ?? Package::first();
            if (!$package) {
                throw new \Exception('Package not found for subscription.');
            }

            // Safely fetch subscription expiry date from Razorpay API
            $expiryDate = null;
            if ($subscriptionId) {
                try {
                    $subData = $rzpSubscription ?: $api->subscription->fetch($subscriptionId);
                    $currentEndUnix = $subData['current_end'] ?? null;
                    if ($currentEndUnix) {
                        $expiryDate = Carbon::createFromTimestamp($currentEndUnix)->format('Y-m-d H:i:s');
                    }
                } catch (\Exception $subFetchEx) {
                    Log::warning('storeTransaction — subscription fetch for expiry date failed: ' . $subFetchEx->getMessage());
                }
            }

            if (!$expiryDate) {
                $expiryDate = strtolower($package->type ?? '') === 'year'
                    ? now()->addYear()->format('Y-m-d H:i:s')
                    : now()->addDays(28)->format('Y-m-d H:i:s');
            }

            DB::beginTransaction();

            // Archive any existing active transaction for this subscription
            if ($subscriptionId) {
                Transction::where('user_id', $user->id)
                    ->where('razorpay_subscription_id', $subscriptionId)
                    ->where('status', self::STATUS_ACTIVE)
                    ->update(['status' => self::STATUS_COMPLETED]);
            }

            // Create new transaction with status active
            $amount = $payment ? ($payment->amount / 100) : ($package->price ?? 0);
            $currency = $payment ? $payment->currency : 'INR';
            $finalPaymentId = $payment ? $payment->id : ($paymentId ?? ('manual_' . Str::random(10)));

            $transaction = Transction::create([
                'user_id' => $user->id,
                'package_id' => $package->id,
                'amount' => $amount,
                'currency_code' => $currency,
                'description' => 'Subscription - First Payment',
                'razorpay_subscription_id' => $subscriptionId,
                'payment_id' => $finalPaymentId,
                'expiry_date' => $expiryDate,
                'transaction_count' => 1,
                'status' => self::STATUS_ACTIVE,
            ]);

            $userUpdateData = [
                'subscription' => 'Yes',
                'razorpay_subscription_id' => $subscriptionId ?: $user->razorpay_subscription_id,
                'subscription_end_date' => $expiryDate,
                'cancelled_at' => null,
            ];

            if ($payment) {
                $rzpContact = trim($payment->contact ?? '');
                if (!empty($rzpContact) && empty($user->mobile_no)) {
                    $userUpdateData['mobile_no'] = $rzpContact;
                }
            }

            // Update user
            try {
                $user->update($userUpdateData);
            } catch (\Exception $eUserUpdate) {
                Log::warning("storeTransaction: Could not update user profile details: " . $eUserUpdate->getMessage());
            }

            // Update user_subscription to active
            $userSubscription->update([
                'package_id' => $package->id,
                'status' => self::STATUS_ACTIVE,
                'starts_at' => now(),
                'ends_at' => $expiryDate,
            ]);

            DB::commit();

            // Upgradable plan handling: Cancel any previous active subscriptions for this user
            if ($subscriptionId) {
                self::cancelPreviousSubscriptionsForUser($user, $subscriptionId);
            }

            // Send notification email
            SubscriptionNotificationHelper::sendNotification(
                'ACTIVATED',
                $user,
                $package,
                $subscriptionId,
                $finalPaymentId,
                'Subscription Activated via Web Checkout',
                [
                    'transaction_mobile' => $payment?->contact ?? null,
                    'transaction_email' => $payment?->email ?? null,
                    'expiry_date' => $expiryDate,
                ]
            );

            Log::info('── storeTransaction END — success ──', [
                'transaction_id' => $transaction->id,
                'user_id' => $user->id,
                'expiry_date' => $transaction->expiry_date,
                'signature_verified' => $signatureVerified,
            ]);

            return redirect()->route('subscribe.thankyou')
                ->with('message', 'Subscription activated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('storeTransaction failed: ' . $e->getMessage());

            // ULTIMATE SAFEGUARD: If payment was recorded or user is active, redirect to thank-you!
            if ($paymentId && Transction::where('payment_id', $paymentId)->exists()) {
                return redirect()->route('subscribe.thankyou')
                    ->with('message', 'Subscription activated successfully!');
            }
            if ($subscriptionId && Transction::where('razorpay_subscription_id', $subscriptionId)->where('status', self::STATUS_ACTIVE)->exists()) {
                return redirect()->route('subscribe.thankyou')
                    ->with('message', 'Subscription activated successfully!');
            }
            if ($user && $user->subscription === 'Yes') {
                return redirect()->route('subscribe.thankyou')
                    ->with('message', 'Subscription activated successfully!');
            }
            if (auth()->check() && auth()->user()->subscription === 'Yes') {
                return redirect()->route('subscribe.thankyou')
                    ->with('message', 'Subscription activated successfully!');
            }
            if ($subscriptionId && UserSubscription::where('razorpay_subscription_id', $subscriptionId)->where('status', self::STATUS_ACTIVE)->exists()) {
                return redirect()->route('subscribe.thankyou')
                    ->with('message', 'Subscription activated successfully!');
            }

            if ($subscriptionId) {
                try {
                    $chkSub = $api->subscription->fetch($subscriptionId);
                    if (in_array(strtolower($chkSub->status ?? ''), ['active', 'authenticated'])) {
                        return redirect()->route('subscribe.thankyou')
                            ->with('message', 'Subscription activated successfully!');
                    }
                } catch (\Exception $chkEx) {}
            }

            return redirect()->route('errorPage')
                ->with('error', 'Payment processing failed: ' . $e->getMessage());
        }
    }
    /**
     * Cancel subscription (frontend action)
     * User clicks cancel subscription button
     */
    public function cancelSubscription(Request $request)
    {
        $user = auth()->user();
        Log::info('── cancelSubscription START ──', ['user_id' => $user?->id]);
        if (!$user || !$user->razorpay_subscription_id) {
            return redirect('/')->with('error', 'No active subscription found.');
        }
        $subscriptionId = $user->razorpay_subscription_id;
        try {
            DB::beginTransaction();
            $api = new Api(config('app.razorpay_key'), config('app.razorpay_secret'));
            $subscription = $api->subscription->fetch($subscriptionId);
            // Only cancel if not already cancelled/completed/expired
            if (!in_array($subscription->status, ['cancelled', 'completed', 'expired'])) {
                $subscription->cancel();
                Log::info('Subscription cancelled in Razorpay', ['subscription_id' => $subscriptionId]);
            } else {
                Log::info('Subscription already cancelled in Razorpay', [
                    'subscription_id' => $subscriptionId,
                    'status' => $subscription->status
                ]);
            }
            // Update active transaction to cancelled (only if it's still active)
            $transaction = Transction::where('user_id', $user->id)
                ->where('razorpay_subscription_id', $subscriptionId)
                ->where('status', self::STATUS_ACTIVE)
                ->first();
            if ($transaction) {
                $transaction->update([
                    'description' => 'Subscription Cancelled by User',
                    'status' => self::STATUS_CANCELLED,
                    'cancelled_at' => now(),
                ]);
            }
            // Update user_subscription to cancelled
            UserSubscription::where('razorpay_subscription_id', $subscriptionId)
                ->where('user_id', $user->id)
                ->update([
                    'status' => self::STATUS_CANCELLED,
                    'ends_at' => now(),
                ]);
            // Update user
            $user->update([
                'razorpay_subscription_id' => null,
                'subscription' => 'No',
                'subscription_end_date' => now(),
                'cancelled_at' => now(),
            ]);
            // Clear device sessions
            DeviceSession::where('user_id', $user->id)->delete();
            DB::commit();

            // Send Cancellation Notification Email
            SubscriptionNotificationHelper::sendNotification(
                'CANCELLED',
                $user,
                $transaction?->package_id,
                $subscriptionId,
                $transaction?->payment_id,
                'Subscription Cancelled by User via Web Profile',
                [
                    'transaction_email' => $user->email,
                    'transaction_mobile' => $user->mobile_no,
                ]
            );

            Log::info('── cancelSubscription END — success ──', ['user_id' => $user->id]);
            return redirect('/')->with('success', 'Subscription cancelled successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('cancelSubscription failed: ' . $e->getMessage());
            return redirect('/')->with('error', 'Failed to cancel subscription. Please try again.');
        }
    }
    /**
     * Get subscription status (for frontend display)
     */
    public function getSubscriptionStatus(Request $request)
    {
        $user = auth()->user();
        
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
        $subscriptionId = $user->razorpay_subscription_id;
        
        if (!$subscriptionId) {
            return response()->json([
                'subscription' => false,
                'status' => 'no_active_subscription',
                'message' => 'No active subscription found'
            ]);
        }
        try {
            $api = new Api(config('app.razorpay_key'), config('app.razorpay_secret'));
            $subscription = $api->subscription->fetch($subscriptionId);
            $userSubscription = UserSubscription::where('razorpay_subscription_id', $subscriptionId)
                ->where('user_id', $user->id)
                ->first();
            $transaction = Transction::where('razorpay_subscription_id', $subscriptionId)
                ->where('user_id', $user->id)
                ->where('status', self::STATUS_ACTIVE)
                ->first();
            return response()->json([
                'subscription' => $user->subscription === 'Yes',
                'razorpay_status' => $subscription->status,
                'local_status' => $userSubscription?->status ?? 'unknown',
                'current_end' => $subscription->current_end 
                    ? Carbon::createFromTimestamp($subscription->current_end)->toDateTimeString() 
                    : null,
                'expiry_date' => $user->subscription_end_date,
                'package_id' => $userSubscription?->package_id,
                'transaction_id' => $transaction?->id,
                'payment_id' => $transaction?->payment_id,
            ]);
        } catch (\Exception $e) {
            Log::error('getSubscriptionStatus failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch subscription status',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Get transaction history for user
     */
    public function getTransactionHistory(Request $request)
    {
        $user = auth()->user();
        
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
        try {
            $transactions = Transction::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($transaction) {
                    return [
                        'id' => $transaction->id,
                        'amount' => $transaction->amount,
                        'currency' => $transaction->currency_code,
                        'description' => $transaction->description,
                        'payment_id' => $transaction->payment_id,
                        'expiry_date' => $transaction->expiry_date,
                        'status' => $transaction->status,
                        'status_label' => $this->getStatusLabel($transaction->status),
                        'created_at' => $transaction->created_at->toDateTimeString(),
                    ];
                });
            return response()->json([
                'success' => true,
                'transactions' => $transactions,
                'total' => count($transactions)
            ]);
        } catch (\Exception $e) {
            Log::error('getTransactionHistory failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch transaction history'], 500);
        }
    }
    /**
     * Get status label for display
     */
    private function getStatusLabel($status)
    {
        return match ($status) {
            self::STATUS_CREATED => 'Pending',
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_FAILED => 'Failed',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_HALTED => 'Halted',
            self::STATUS_PAUSED => 'Paused',
            default => 'Unknown',
        };
    }
    public function devicelimit()
    {
        $user = session('user_id');
        $watchlist = app('wishlist');
        session(['watchlist' => $watchlist]);
        $package = Package::where('status', 1)->get();
        // $package = Package::join('transaction','transaction.package_id', '=', 'package.id')
        // ->where('transaction.user_id', $user->id)
        // ->get();
        $activepack = Transction::join('package', 'package.id', '=', 'transaction.package_id')
            ->where('transaction.user_id', $user)
            ->where('transaction.status', 1) // Specify the status column from the transaction table
            ->select('transaction.*', 'package.type_id') // Select type_id from package table
            ->get();
        $currentPackagePrice = 0;
        if (!$activepack->isEmpty()) {
            $currentPackagePrice = $activepack->first()->amount;
            // Only show packages with a higher price than current
            $package = Package::where('status', 1)->where('price', '>=', $currentPackagePrice)->get();
        } else {
            $package = Package::where('status', 1)->get(); // Show all if no subscription
        }
        $typeId = explode(',', $package->pluck('type_id'));
        $typeData = Type::whereIn('id', $typeId)->get();
        // Check if the user has an active subscription
        $mess = null;
        $activeSubscription = false;
        $logedInDevice = DeviceSession::where('user_id', $user)->get();
        // Set amount based on active subscription status
        $amount = $activeSubscription ? 15 : 149;
        return view('nodevice', compact('user', 'logedInDevice', 'amount'), [
            'watchlist' => $watchlist,
            'package' => $package,
            'typeData' => $typeData,
            'activeSubscription' => $activeSubscription,
        ]);
    }
    public function storeUgradeTransaction(Request $request)
    {
        // $api = new Api(config('app.razorpay_key'), config('app.razorpay_secret'));
        $api = new Api(config('app.razorpay_key'), config('app.razorpay_secret'));
        $attributes = [
            'razorpay_order_id' => $request->razorpay_order_id,
            'razorpay_payment_id' => $request->razorpay_payment_id,
            'razorpay_signature' => $request->razorpay_signature
        ];
        // echo '<pre>';
        // print_r($attributes);
        // echo '</pre>';
        // die;
        // die;
        try {
            $api->utility->verifyPaymentSignature($attributes);
            // Payment is successful, save to database
            $expiryDate = now()->addDays(28);// Assuming subscription duration is 1 month
            // $expiryDate = now()->addMonth(); // Assuming subscription duration is 1 month
            // Set existing transactions to inactive
            $transactionExists = Transction::where('user_id', $request->user_id)
                ->where('status', 1)
                ->where('amount', 1)
                ->where('package_id', 2)
                ->exists();
            if ($transactionExists) {
                Transction::where('user_id', $request->user_id)
                    ->where('status', 1)
                    ->where('amount', 1)
                    ->where('package_id', 2)
                    ->update(['status' => 1]);
            } else {
                Transction::create([
                    'package_id' => $request->package_id,
                    'amount' => $request->amount,
                    'user_id' => $request->user_id,
                    'description' => 'Subscription',
                    'payment_id' => $request->razorpay_payment_id,
                    'currency_code' => 'INR',
                    'expiry_date' => $expiryDate,
                    'status' => 1, // Active status
                    'transaction_count' => 1, // Active status
                ]);
            }
            // Store the new transaction details in the database
            // Update user subscription status
            $user = User::find($request->user_id);
            $user->subscription = 'Yes';
            $user->subscription_end_date = $expiryDate;
            $user->save();
            $deviceName = $request->header('User-Agent');
            $deviceIp = $request->ip();
            Device::create([
                'user_id' => $user->id,
                'device_id' => $deviceName,
                'device_ip' => $deviceIp,
            ]);
            return redirect()->route('subscribe.thankyou')->with('message', 'Subscription successful!');
        } catch (\Razorpay\Api\Errors\SignatureVerificationError $e) {
            if (auth()->check() && auth()->user()->subscription === 'Yes') {
                return redirect()->route('subscribe.thankyou')->with('message', 'Subscription successful!');
            }
            return redirect()->route('errorPage')->with('error', 'Payment verification failed!');
        } catch (\Exception $e) {
            Log::error('Transaction Store Error: ' . $e->getMessage());
            if (auth()->check() && auth()->user()->subscription === 'Yes') {
                return redirect()->route('subscribe.thankyou')->with('message', 'Subscription successful!');
            }
            return redirect()->route('errorPage')->with('error', 'An error occurred while processing your transaction.');
        }
    }
    public function initiate(Request $request)
    {
        $request->validate(['package_id' => 'required|exists:package,id']);
        $package = \App\Models\Package::findOrFail($request->package_id);
        $user    = auth()->user();
        if($user->subscription === 'Yes') {
            return redirect()->route('subscribe')
                ->with('message', 'You already have an active subscription.');
        }

        if ((int) $package->price === 15) {
            $package15Ids = Package::where('price', 15)->pluck('id')->toArray();
            $alreadyHas15 = Transction::where('user_id', $user->id)
                ->where(function ($q) use ($package15Ids) {
                    if (!empty($package15Ids)) {
                        $q->whereIn('package_id', $package15Ids);
                    }
                    $q->orWhere('amount', 15)
                      ->orWhere('amount', '15')
                      ->orWhere('amount', '15.00')
                      ->orWhere('amount', '15.0');
                })
                ->where(function ($q) {
                    $q->whereIn('status', [1, 3, 4, 5, 6])
                      ->orWhereNotNull('payment_id');
                })
                ->exists()
                || (!empty($package15Ids) && UserSubscription::where('user_id', $user->id)
                    ->whereIn('package_id', $package15Ids)
                    ->where('status', '!=', 2)
                    ->exists());

            if ($alreadyHas15) {
                return redirect()->back()
                    ->with('error', 'You have already availed the ₹15 plan. Please choose another plan.');
            }
        }
        $txnid   = 'OTT_' . strtoupper(\Str::random(10)) . '_' . time();
        // Build productinfo with fallbacks — must never be empty
        $productinfo = $package->title 
                    ?? $package->name 
                    ?? 'Subscription Plan';
        $params = [
            'txnid'       => $txnid,
            'amount'      => number_format($package->price, 2, '.', ''),
            'productinfo' => $productinfo,   // ← fixed
            'firstname'   => $user->name,
            'email'       => $user->email,
            'phone'       => $user->mobile_no ?? $user->phone ?? '9999999999',
        ];
        // Save pending transaction AFTER params are built
        \DB::table('transaction')->insert([
            'user_id'       => $user->id,
            'unique_id'     => $txnid,
            'package_id'    => $package->id,
            'amount'        => $package->price,
            'payment_id'    => '',
            'currency_code' => 'INR',
            'expiry_date'   => now()->addDays($package->no_of_days ?? 30),
            'status'        => 2,
            'gateway'       => 'payu',
            'payu_txnid'    => $txnid,
            'description'   => $productinfo . ' Plan',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
        $params['hash'] = $this->payU->generateHash($params);
        return view('payu.redirect', [
            'paymentUrl' => $this->payU->getPaymentUrl(),
            'key'        => $this->payU->getKey(),
            'params'     => $params,
            'successUrl' => $this->payU->getSuccessUrl(),
            'failureUrl' => $this->payU->getFailureUrl(),
        ]);
    }
    public function success(Request $request)
    {
        $data = $request->all();
        if (! $this->payU->verifyHash($data)) {
            return redirect('/')->with('error', 'Payment verification failed.');
        }
        $txn = \DB::table('transaction')
            ->where('payu_txnid', $data['txnid'])
            ->where('status', 2)
            ->first();
        if (! $txn) {
            return redirect('/')->with('error', 'Transaction not found.');
        }
        if (!auth()->check()) {
        $user = User::find($txn->user_id);
        if ($user) {
            Auth::login($user);
            session(['user_id' => $user->id]);
        }
    }
        $package = Package::find($txn->package_id);
        $days    = $package?->no_of_days ?? 30;
        // Determine expiry same way as Razorpay
        $expiryDate = strtolower($package?->type ?? '') === 'year'
            ? now()->addYear()
            : now()->addDays($days);
        DB::transaction(function () use ($txn, $data, $expiryDate) {
            // 1. Update transaction record
            DB::table('transaction')->where('id', $txn->id)->update([
                'payment_id'       => $data['mihpayid'],
                'status'           => 1,
                'payment_mode'     => $data['mode'] ?? null,
                'gateway_response' => json_encode($data),
                'expiry_date'      => $expiryDate,
                'updated_at'       => now(),
            ]);
            // 2. Update user (same as Razorpay storeTransaction)
            \App\Models\User::where('id', $txn->user_id)->update([
                'subscription'          => 'Yes',
                'subscription_end_date' => $expiryDate,
            ]);
            // 3. Update user_subscription
            DB::table('user_subscriptions')->updateOrInsert(
                ['user_id' => $txn->user_id],
                [
                    'payment_id'               => (string) $txn->id,
                    'razorpay_subscription_id' => null,
                    'package_id'               => $txn->package_id,
                    'status'                   => 1,
                    'gateway'                  => 'payu',
                    'starts_at'                => now(),
                    'ends_at'                  => $expiryDate,
                    'updated_at'               => now(),
                    'created_at'               => now(),
                ]
            );
        });
        return redirect()->route('subscribe.thankyou')
            ->with('message', 'Subscription activated successfully!');
    }
    public function failure(Request $request)
    {
        $data = $request->all();
        \Log::warning('PayU payment failed', $data);
        // Update transaction status to failed
        if (!empty($data['txnid'])) {
            \DB::table('transaction')
                ->where('payu_txnid', $data['txnid'])
                ->where('status', 2) // only update if still pending
                ->update([
                    'status'           => 0, // failed
                    'payment_id'       => $data['mihpayid'] ?? '',
                    'gateway_response' => json_encode($data),
                    'updated_at'       => now(),
                ]);
        }
        $reason = $data['error_Message'] 
            ?? $data['field9'] 
            ?? 'Payment was not completed. Please try again.';
        return redirect()->route('subscription.index')
            ->with('payment_error', $reason);
    }
    // public function storeTransaction(Request $request)
    // {
    //     Log::info('store transaction called from web');
    //     $api = new Api(config('app.razorpay_key'), config('app.razorpay_secret'));
    //     $attributes = [
    //         'razorpay_subscription_id' => $request->razorpay_subscription_id,
    //         'razorpay_payment_id' => $request->razorpay_payment_id,
    //         'razorpay_signature' => $request->razorpay_signature
    //     ];
    //     try {
    //         $api->utility->verifyPaymentSignature($attributes);
    //         $payment = $api->payment->fetch($request->razorpay_payment_id);
    //         $user_subscription = UserSubscription::where('razorpay_subscription_id', $request->razorpay_subscription_id)
    //             ->where('package_id', $request->package_id)->where('status', 0)
    //             ->first();
    //         $user = User::find($request->user_id);
    //         if ($payment->status === 'authorised') {
    //             $payment = $payment->capture(['amount' => $payment->amount]);
    //         }
    //         // if ($payment->status === 'captured') {
    //         //     $amountInRupees = $request->amount / 100;
    //         //     $package = Package::find($request->package_id);
    //         //     if (!$package) {
    //         //         return redirect()->route('subscribe')->with('message', 'Invalid package selected.');
    //         //     }
    //         //     $type = strtolower(trim($package->type)); // normalize to lowercase
    //         //     if ($type === 'month') {
    //         //         $expiryDate = Carbon::now()->addDays(28)->format('Y-m-d H:i:s');
    //         //     } elseif ($type === 'year') {
    //         //         $expiryDate = Carbon::now()->addYear()->format('Y-m-d H:i:s');
    //         //     } else {
    //         //         $expiryDate = Carbon::now()->addDays(28)->format('Y-m-d H:i:s'); // fallback
    //         //     }
    //         //     //upgrading
    //         //     $existingTransaction =  Transction::where('user_id', $request->user_id)
    //         //                                             ->where('status', 1)
    //         //                                             ->first();
    //         //     if($existingTransaction){
    //         //         try {
    //         //             $existingUsreSubscription = UserSubscription::where('user_id', $request->user_id)
    //         //                                                 ->where('razorpay_subscription_id',$user->razorpay_subscription_id)
    //         //                                                 ->where('status',1)
    //         //                                                 ->first();
    //         //             Log::warning('Existing User subscription: ' . $existingUsreSubscription);
    //         //             $oldSubscription = $api->subscription->fetch($user->razorpay_subscription_id);
    //         //             if ($oldSubscription && $oldSubscription->status !== 'cancelled') {
    //         //                 $oldSubscription->cancel(['cancel_at_cycle_end' => false]);
    //         //             }
    //         //             $existingTransaction->status = 3; //upgrade,cancel,deleted
    //         //             $existingTransaction->cancelled_at = Carbon::now()->format('Y-m-d H:i:s');
    //         //             $existingTransaction->update();
    //         //             $existingUsreSubscription->status = 3; //upgrade,cancel,deleted
    //         //             $existingUsreSubscription->update();
    //         //         } catch (\Exception $ex) {
    //         //              Log::warning('Existing subscription cancellation failed: ' . $ex->getMessage());
    //         //         }
    //         //     }
    //         //     if($user_subscription){
    //         //         // Save transaction
    //         //         Transction::create([
    //         //             'package_id' => $request->package_id,
    //         //             'amount' => $amountInRupees,
    //         //             'user_id' => $request->user_id,
    //         //             'description' => 'Subscription',
    //         //             'razorpay_subscription_id'=> $request->razorpay_subscription_id,
    //         //             'payment_id' => $request->razorpay_payment_id,
    //         //             'currency_code' => 'INR',
    //         //             'expiry_date' => $expiryDate,
    //         //             'status' => 1,
    //         //             'transaction_count' => 1,
    //         //         ]);
    //         //         $user->subscription = 'Yes';
    //         //         $user->razorpay_subscription_id = $request->razorpay_subscription_id;
    //         //         $user->subscription_end_date = $expiryDate;
    //         //         $user->update();
    //         //         $user_subscription->status = 1;
    //         //         $user_subscription->update();
    //         //     }
    //         //     registerDeviceSession($request);
    //         //     $checkfestivalcompetition = FestivalCompetition::where('user_id',$user->id)->where('status',1)->first();
    //         //     if($checkfestivalcompetition)
    //         //     {
    //         //         $checkfestivalcompetition->status = 1;
    //         //         $checkfestivalcompetition->update();
    //         //     }
    //         //     return redirect()->route('subscribe.thankyou')->with('message', 'Subscription successful!');
    //         // } else {
    //         //     Log::error('Transaction Store Error: Payment not captured.');
    //         //     return redirect()->route('subscribe')->with('message', 'Payment not captured.');
    //         // }
    //         return redirect()->route('subscribe.thankyou')->with('message', 'Subscription successful!');
    //     } catch (\Razorpay\Api\Errors\SignatureVerificationError $e) {
    //         Log::error('Transaction Store Error: ' . $e->getMessage());
    //         return redirect()->route('errorPage')->with('error', 'Payment verification failed!');
    //     } catch (\Exception $e) {
    //         Log::error('Transaction Store Error: ' . $e->getMessage());
    //         return redirect()->route('errorPage')->with('error', 'An error occurred while processing your transaction.');
    //     }
    // }
    // if (auth()->check()) {
            // } else {
            //     $rzpEmail   = strtolower(trim($payment->email   ?? ''));
            //     $rzpContact = trim($payment->contact ?? '');
            //     if (!$rzpEmail) {
            //         throw new \Exception('Razorpay did not return a valid email for this payment.');
            //     }
            //     $userByEmail  = User::where('email', $rzpEmail)->first();
            //     $userByMobile = $rzpContact ? User::where('mobile_no', $rzpContact)->first() : null;
            //     if ($userByEmail && $userByMobile) {
            //         $user = $userByEmail;
            //         if ($userByEmail->id !== $userByMobile->id && empty($user->mobile_no)) {
            //             $user->update(['mobile_no' => $rzpContact]);
            //         }
            //     } elseif ($userByEmail) {
            //         $user = $userByEmail;
            //         if (empty($user->mobile_no) && $rzpContact) {
            //             $user->update(['mobile_no' => $rzpContact]);
            //         }
            //     } elseif ($userByMobile) {
            //         $user = $userByMobile;
            //     } else {
            //         $user = User::create([
            //             'name'      => 'Guest User',
            //             'email'     => $rzpEmail,
            //             'mobile_no' => $rzpContact,
            //             'type'      => 3,
            //         ]);
    /**
     * Upgradable plan helper: Cancel any previous active or pending subscriptions on Razorpay API and DB when a new plan is activated.
     */
    public static function cancelPreviousSubscriptionsForUser($user, $currentSubscriptionId)
    {
        if (!$user || !$currentSubscriptionId) {
            return;
        }

        try {
            $oldSubs = UserSubscription::where('user_id', $user->id)
                ->where('razorpay_subscription_id', '!=', $currentSubscriptionId)
                ->whereIn('status', [self::STATUS_CREATED, self::STATUS_ACTIVE])
                ->get();

            if ($oldSubs->isEmpty()) {
                return;
            }

            $api = new Api(config('app.razorpay_key'), config('app.razorpay_secret'));

            foreach ($oldSubs as $oldSub) {
                $oldId = $oldSub->razorpay_subscription_id;
                if (empty($oldId)) continue;

                Log::info('Upgrading user subscription — cancelling old subscription: ' . $oldId);

                try {
                    $rzpSub = $api->subscription->fetch($oldId);
                    if (isset($rzpSub->status) && !in_array(strtolower($rzpSub->status), ['cancelled', 'completed', 'expired'])) {
                        $rzpSub->cancel();
                        Log::info('Cancelled old subscription on Razorpay API: ' . $oldId);
                    }
                } catch (\Exception $e) {
                    Log::warning('Could not cancel old subscription on Razorpay: ' . $e->getMessage());
                }

                $oldSub->update([
                    'status' => self::STATUS_CANCELLED,
                    'ends_at' => now(),
                ]);

                Transction::where('user_id', $user->id)
                    ->where('razorpay_subscription_id', $oldId)
                    ->where('status', self::STATUS_ACTIVE)
                    ->update([
                        'status' => self::STATUS_CANCELLED,
                        'cancelled_at' => now(),
                        'description' => 'Cancelled due to plan upgrade',
                    ]);
            }

            // Ensure any dangling active transactions not tied to current subscription are also deactivated
            Transction::where('user_id', $user->id)
                ->where('status', self::STATUS_ACTIVE)
                ->where(function ($q) use ($currentSubscriptionId) {
                    $q->where('razorpay_subscription_id', '!=', $currentSubscriptionId)
                      ->orWhereNull('razorpay_subscription_id');
                })
                ->update([
                    'status' => self::STATUS_CANCELLED,
                    'cancelled_at' => now(),
                    'description' => 'Cancelled due to plan upgrade',
                ]);
        } catch (\Exception $e) {
            Log::error('cancelPreviousSubscriptionsForUser error: ' . $e->getMessage());
        }
    }
}