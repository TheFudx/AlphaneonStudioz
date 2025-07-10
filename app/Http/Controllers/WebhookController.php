<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transction;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class WebhookController extends Controller
{
  public function handleWebhook(Request $request)
{
    try {
        $this->verifySignature($request);

        $event = $request->event;

        switch ($event) {
            case 'subscription.charged':
                $this->handleSubscriptionCharged($request);
                break;

            case 'subscription.cancelled':
                $this->handleSubscriptionCancelled($request);
                break;

            default:
                Log::warning("Unhandled webhook event: $event");
        }

        return response()->json(['status' => 'success']);
    } catch (\Exception $e) {
        Log::error('Razorpay Webhook Error: ' . $e->getMessage());
        return response()->json(['error' => 'Webhook processing failed'], 500);
    }
}

private function verifySignature(Request $request)
{
    $webhookSecret = 'SKna@@6T69tk4rd';
    $payload = $request->getContent();
    $signature = $request->header('X-Razorpay-Signature');
    $generatedSignature = hash_hmac('sha256', $payload, $webhookSecret);

    if ($signature !== $generatedSignature) {
        Log::error('Invalid Razorpay webhook signature.');
        throw new \Exception('Invalid signature');
    }
}

private function handleSubscriptionCharged(Request $request)
{
    $subscription = $request->payload['subscription']['entity'];
    $user = User::where('razorpay_subscription_id', $subscription['id'])->first();

    if ($user) {
         $user_subscription = UserSubscription::where('razorpay_subscription_id', $subscription['id'])->where('status',1)->first();

        if ($user_subscription->package_id) {
            $newExpiryDate = Carbon::now()->addDays(28)->format('Y-m-d H:i:s');

            // Fetch the active transaction
            $transaction = Transction::where('user_id', $user->id)
                ->where('status', 1)
                ->where('package_id', $user_subscription->package_id)
                ->where('payment_id', $subscription['latest_invoice_id'] ?? null)
                ->first();

            if ($transaction) {
                $transaction->transaction_count += 1;
                $transaction->expiry_date = $newExpiryDate;
                $transaction->save();

                Log::info("Transaction updated: count incremented for User ID: {$user->id}, Count: {$transaction->transaction_count}");
            } else {
                Log::warning("Active transaction not found for User ID: {$user->id} and Package ID: {$user_subscription->package_id}");
            }

            // Update user subscription expiry
            $user->subscription_end_date = $newExpiryDate;
            $user->save();

            Log::info("Subscription renewed for User ID: {$user->id}, Package ID: $user_subscription->package_id");
        } else {
            Log::warning("No package ID found in user_subscription table for Subscription ID: {$subscription['id']}");
        }
    } else {
        Log::error("User not found for Subscription ID: {$subscription['id']}");
    }
}



private function handleSubscriptionCancelled(Request $request)
    {
        $subscription = $request->payload['subscription']['entity'];
        $user = User::where('razorpay_subscription_id', $subscription['id'])->first();

        if ($user) {
            $user_subscription = UserSubscription::where('razorpay_subscription_id', $subscription['id'])->value('package_id');

            $updatedRows = Transction::where('user_id', $user->id)
                ->where('status', 1)
                ->where('package_id', $user_subscription->package_id)
                ->update([
                    'razorpay_subscription_id' => $subscription['id'],
                    'status' => 0,
                    'expiry_date' => Carbon::now()->format('Y-m-d H:i:s')
                ]);

            $user->subscription = 'No';
            $user->subscription_end_date = Carbon::now()->format('Y-m-d H:i:s');
            $user->razorpay_subscription_id = null;
            $user->save();
                
            $user_subscription->status = 3;
            $user_subscription->update();
            
            DB::table('device_sessions')
                        ->where('user_id', $user->id)
                        ->delete();
            

            Log::info("Subscription cancelled for User ID: {$user->id}, Package ID: $user_subscription->package_id, Rows Updated: $updatedRows");
        } else {
            Log::error("User not found for Subscription ID: {$subscription['id']}");
        }
    }




}

