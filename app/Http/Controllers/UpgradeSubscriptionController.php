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
Use App\Models\Type;
Use App\Models\Device;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\Log;
use App\Models\User;
class UpgradeSubscriptionController extends Controller
{
    public function storeUpgradeTransaction(Request $request)
    {
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
        
        $attributes = [
            'razorpay_order_id' => $request->razorpay_order_id,
            'razorpay_payment_id' => $request->razorpay_payment_id,
            'razorpay_signature' => $request->razorpay_signature
        ];
        //     echo '<pre>';
        // print_r($attributes);
        // echo '</pre>';
        // die;
        // die;
    
        try {
            $api->utility->verifyPaymentSignature($attributes);
    
            // Payment is successful, save to database
            $expiryDate = now()->addDays(28); // Assuming subscription duration is 28 days
    
            // Check if the transaction already exists
            $existingTransaction = Transction::where('payment_id', $request->razorpay_payment_id)->first();
            
            if ($existingTransaction) {
                return redirect()->route('subscribe.thankyou')->with('message', 'Subscription already processed!');
            }
    
            // Set existing transactions to inactive
            Transction::where('user_id', $request->user_id)
                ->where('status', 1)
                ->where('package_id', 2)
                ->update(['status' => 0]);
    
            // Create a new transaction
            Transction::create([
                'package_id' => $request->package_id,
                'amount' => $request->amount,
                'user_id' => $request->user_id,
                'description' => 'Subscription',
                'payment_id' => $request->razorpay_payment_id,
                'currency_code' => 'INR',
                'expiry_date' => $expiryDate,
                'status' => 1, // Active status
            ]);
    
    
            return redirect()->route('subscribe.thankyou')->with('message', 'Subscription successful!');
        } catch (\Razorpay\Api\Errors\SignatureVerificationError $e) {
            return redirect()->route('errorPage')->with('error', 'Payment verification failed!');
        } catch (\Exception $e) {
            Log::error('Transaction Store Error: ' . $e->getMessage());
            return redirect()->route('errorPage')->with('error', 'An error occurred while processing your transaction.');
        }
    }
    
}
