<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Transction;
use App\Models\UserSubscription;
use Log;
use Http;

class InAppPurchaseSuccessController extends Controller
{
    public function success(Request $request){
        $request->validate([
            'purchaseID' => 'required|string',
            'productID' => 'required|string',
            'transactionDate' => 'required|string',
            'status' => 'required|string',
            'user_id' => 'required|integer',
        ]);
        // return response()->json($request->all());
        $user = User::findOrFail($request->user_id);
        // Determine expiry date based on productID
        if ($request->productID === 'base_mont_01') {
            $expiryDate = Carbon::now()->addMonth();
            $packageId = 1; // Assuming monthly is package ID 1
            $amout = "15.00";
            Log::info('Amount received: ' . $amout);
        } elseif ($request->productID === 'base_year_01') {
            $expiryDate = Carbon::now()->addYear();
            $packageId = 2; // Assuming yearly is package ID 2
            $amout = "149.00";
            Log::info('Amount received: ' . $amout);
        } else {
            return response()->json(['error' => 'Invalid product ID'], 400);
        }
        // Deactivate previous
        Transction::where('user_id', $user->id)
                    ->where('status', 1)
                    ->update(['status' => 0]);

        
        // Record transaction
        Transction::create([
            'user_id' => $user->id,
            'unique_id' => uniqid('txn_'),
            'package_id' => $packageId,
            'description' => 'In-App Purchase via iOS',
            'amount' => $amout, // Since iOS handles payment, set 0 or send actual if passed
            'payment_id' => $request->purchaseID,
            'currency_code' => 'INR', // or from config/request
            'expiry_date' => $expiryDate->toDateString(),
            'status' => 1,
            'transaction_count' => 1,
            'is_delete' => 0,
        ]);
        UserSubscription::create([
            'user_id' => $user->id,
            'payment_id' => $request->purchaseID,
            'package_id' => $packageId,
            'status' => 1,
            'created_at'=>now(),
            'updated_at'=> now()
        ]);
        // Update user
        $user->subscription = 'Yes'; // Assuming 1 means 'yes'
        $user->subscription_end_date = $expiryDate->toDateString();
        $user->payment_id = $request->purchaseID;
        $user->save();

        //Device session store
        registerDeviceSession($request);

        return response()->json([
            'message' => 'Subscription updated successfully.',
        ], 200);
    }

    public function verifyReceiptAndSubscribe(Request $request)
    {
        $request->validate([
            'receipt' => 'required|string',
            'user_id' => 'required|integer',
        ]);
        // Apple shared secret from App Store Connect
        $sharedSecret = config('services.apple.shared_secret'); // store this in .env
        $endpoint = 'https://buy.itunes.apple.com/verifyReceipt';
        $payload = json_encode([
            'receipt-data' => $request->receipt,
            'password' => $sharedSecret,
            'exclude-old-transactions' => true
        ]);
        // Verify with Apple
        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpCode != 200) {
            return response()->json(['error' => 'Apple verification failed'], 500);
        }
        $decoded = json_decode($response, true);
        if ($decoded['status'] !== 0) {
            return response()->json([
                'error' => 'Invalid receipt from Apple',
                'status' => $decoded['status']
            ], 400);
        }
        $latestReceiptInfo = $decoded['latest_receipt_info'] ?? [];
        $activeSubscription = collect($latestReceiptInfo)->last(); // latest transaction
        if (!$activeSubscription) {
            return response()->json(['error' => 'No active subscription found'], 404);
        }
        $productId = $activeSubscription['product_id'];
        $expiresMs = intval($activeSubscription['expires_date_ms']);
        $expiryDate = Carbon::createFromTimestampMs($expiresMs);
        if ($expiryDate->isPast()) {
            return response()->json(['error' => 'Subscription expired'], 400);
        }
        // Determine package_id
        $packageId = match ($productId) {
            'xyz' => 1,    // Monthly
            'abcde' => 2,  // Yearly
            default => null
        };
        if (!$packageId) {
            return response()->json(['error' => 'Unknown product ID'], 400);
        }
        // Update user subscription
        $user = User::findOrFail($request->user_id);
        $user->status = 1;
        $user->subscription = $expiryDate->toDateString();
        $user->save();
        // Save transaction
        Transction::create([
            'user_id' => $user->id,
            'unique_id' => uniqid('txn_'),
            'package_id' => $packageId,
            'description' => 'iOS In-App Purchase',
            'amount' => '0.00',
            'payment_id' => $activeSubscription['transaction_id'],
            'currency_code' => 'INR',
            'status' => 1,
            'transaction_count' => 1,
            'is_delete' => 0,
        ]);
        return response()->json([
            'success' => true,
            'product_id' => $productId,
            'expires_at' => $expiryDate->toDateTimeString(),
        ]);
    }

    public function handleIOSWebhook(Request $request)
    {   
        Log::info('------------------------------Apple webhook started------------------------------');
        Log::info('Apple IAP Webhook Received:', $request->all());

        $notificationType = $request->input('notification_type');
        $latestInfo = $request->input('unified_receipt.latest_receipt_info')[0] ?? null;

        if (!$latestInfo) {
            return response()->json(['error' => 'No receipt info found'], 400);
        }

        // Extract core fields from Apple payload
        $transactionId         = $latestInfo['transaction_id'] ?? null;
        $originalTransactionId = $latestInfo['original_transaction_id'] ?? null;
        $productId             = $latestInfo['product_id'] ?? null;
        $expiresAt             = isset($latestInfo['expires_date_ms']) 
                                    ? \Carbon\Carbon::createFromTimestampMs($latestInfo['expires_date_ms']) 
                                    : null;
        $purchasedAt           = isset($latestInfo['purchase_date_ms']) 
                                    ? \Carbon\Carbon::createFromTimestampMs($latestInfo['purchase_date_ms']) 
                                    : null;
        $environment           = $request->input('environment', 'Unknown');

        // Find user by previous transaction (optional: add app_account_token mapping later)
        $transaction = Transction::where('payment_id', $originalTransactionId)
            ->orWhere('payment_id', $transactionId)
            ->first();

        $user = $transaction?->user;
        if($latestInfo['product_id'] == 'base_mont_01') {
            $packageId = 2; // Monthly
        } elseif($latestInfo['product_id'] == 'base_year_01') {
            $packageId = 3; // Yearly
        } else {
            return response()->json(['error' => 'Unknown product ID'], 400);
        }
        // Upsert transaction
        $newTransaction = Transction::updateOrCreate(
            ['payment_id' => $transactionId],
            [
                'user_id'                 => optional($user)->id,
                'package_id'              => $packageId,
                'payment_id'              => $originalTransactionId,
                'created_at'              => $purchasedAt,
                'expiry_date'              => $expiresAt,
                'status'                  => in_array($notificationType, ['INITIAL_BUY', 'DID_RENEW']) ? 1 : 0,
            ]
        );
        Log::info('user latest info', $latestInfo);

        // Update user subscription status (if user found)
        if ($user) {
            if (in_array($notificationType, ['INITIAL_BUY', 'DID_RENEW'])) {
                $user->update([
                    'is_subscribed' => true,
                    'subscription_expires_at' => $expiresAt,
                ]);
            } elseif (in_array($notificationType, ['CANCEL', 'EXPIRED', 'DID_FAIL_TO_RENEW'])) {
                $user->update([
                    'is_subscribed' => false,
                    'subscription_expires_at' => $expiresAt,
                ]);
            }

            Log::info("Subscription updated for user ID {$user->id}, notification: $notificationType");
        } else {
            Log::warning("Webhook received but user not found for transaction_id: $transactionId");
        }
        Log::info('------------------------------Apple webhook ended------------------------------');
        return response()->json(['success' => true]);
    }
    public function verifyIOSReceipt(Request $request)
    {
        $receiptData = $request->input('receiptData'); // This must be base64-encoded
        $sharedSecret = env('IOS_SERECT_KEY'); // Set this in .env
        $response = Http::post('https://buy.itunes.apple.com/verifyReceipt', [
            'receipt-data' => $receiptData,
            'password' => $sharedSecret,
            'exclude-old-transactions' => true,
        ]);
        $data = $response->json();
        // Handle common errors
        if ($data['status'] == 21007) {
            // Receipt from sandbox, resend to sandbox URL
            $response = Http::post('https://sandbox.itunes.apple.com/verifyReceipt', [
                'receipt-data' => $receiptData,
                'password' => $sharedSecret,
                'exclude-old-transactions' => true,
            ]);
            $data = $response->json();
        }
        if ($data['status'] !== 0) {
            return response()->json(['error' => 'Invalid receipt', 'code' => $data['status']], 400);
        }
        // Extract subscription info
        $latestReceiptInfo = $data['latest_receipt_info'] ?? [];
        $activeSubscriptions = [];
        foreach ($latestReceiptInfo as $receipt) {
            $expires = intval($receipt['expires_date_ms']);
            if ($expires > now()->timestamp * 1000) {
                $activeSubscriptions[] = [
                    'product_id' => $receipt['product_id'],
                    'transaction_id' => $receipt['original_transaction_id'],
                    'expires_at' => $expires,
                ];
            }
        }
        return response()->json([
            'active_subscriptions' => $activeSubscriptions,
            'raw' => $data // optional: for debugging
        ]);
    }
}
