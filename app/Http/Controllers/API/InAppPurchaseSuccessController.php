<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Transction;
use App\Models\UserSubscription;
use Log;

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

    public function handleWebhook(Request $request)
    {
        Log::info('Apple IAP Webhook:', $request->all());

        $notificationType = $request->input('notification_type');
        $receiptInfo = $request->input('unified_receipt.latest_receipt_info')[0] ?? null;

        if (!$receiptInfo) {
            return response()->json(['error' => 'No receipt info found'], 400);
        }

        $transactionId = $receiptInfo['transaction_id'];
        $productId = $receiptInfo['product_id'];
        $expiresDate = Carbon::createFromTimestampMs($receiptInfo['expires_date_ms']);

        // Match user from your DB using transaction_id or other method
        $transaction = Transction::where('payment_id', $transactionId)->where('status',1)->first();

        if ($transaction && $transaction->user) {
            $user = $transaction->user;

            $user->subscription = 'Yes';
            $user->subscription_end_date = $expiresDate->toDateString();
            $user->save();

            // Optionally log renewal
            Log::info("Renewed subscription for user ID {$user->id} until {$expiresDate}");
        }

        return response()->json(['success' => true]);
    }
}
