<?php
namespace App\Http\Controllers;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
class RazorpayController extends Controller
{
    public function createOrder(Request $request)
    {
        try {
            $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
            $order = $api->order->create([
                'receipt' => 'order_rcptid_11',
                'amount' => $request->amount, // amount in the smallest currency unit
                'currency' => 'INR'
            ]);
            return response()->json([
                'order_id' => $order['id'],
                'razorpay_key' => env('RAZORPAY_KEY'),
                'amount' => $request->amount,
                'name' => app('logged-in-user')->name,
                'currency' => 'INR',
                'email' => app('logged-in-user')->email,
                'contact' => app('logged-in-user')->contact,
            ]);
        } catch (\Exception $e) {
            Log::error('Razorpay order creation failed: ' . $e->getMessage());
            return response()->json(['error' => 'Order creation failed: ' . $e->getMessage()], 500);
        }
    }
    public function paymentCallback(Request $request)
    {
        $signatureStatus = $this->verifySignature($request->all());
        
        if ($signatureStatus) {
            // Save the transaction details
            Transaction::create([
                'user_id' => Auth::id(),
                'order_id' => $request->order_id,
                'payment_id' => $request->razorpay_payment_id,
                'amount' => $request->amount,
                'status' => 'completed'
            ]);
            return redirect()->route('subscription.success');
        } else {
            return redirect()->route('subscription.failed');
        }
    }
    private function verifySignature($attributes)
    {
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
        try {
            $api->utility->verifyPaymentSignature($attributes);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
