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
class SubscriptionTestController extends Controller
{
    public function devicelimit(){
        $user = app('logged-in-user');
        
        
        $watchlist = app('wishlist');
        session(['watchlist' => $watchlist]);
    
        $package = Package::all();
        // $package = Package::join('transaction','transaction.package_id', '=', 'package.id')
        // ->where('transaction.user_id', $user->id)
        // ->get();
    
        $typeId =  explode(',', $package->pluck('type_id'));
        $typeData = Type::whereIn('id', $typeId)->get();
        
        // Check if the user has an active subscription
        $mess = null;
        $activeSubscription = false;
        
 
        // Set amount based on active subscription status
        $amount = $activeSubscription ? 1 : 5;
        // Create Razorpay order
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
        $order = $api->order->create([
            'receipt' => 'order_rcptid_11',
            'amount' => $amount * 100, // Razorpay accepts amount in paise
            'currency' => 'INR'
        ]);
        return view('nodevice', compact('user', 'order', 'amount'), [
            
            
            'watchlist' => $watchlist,
            'package' => $package,
            'typeData' => $typeData,
            'activeSubscription' => $activeSubscription,
            
        ]);
    }
    public function renew(){
        $user = app('logged-in-user');
        
        
        $watchlist = app('wishlist');
        session(['watchlist' => $watchlist]);
    
        $package = Package::all();
        // $package = Package::join('transaction','transaction.package_id', '=', 'package.id')
        // ->where('transaction.user_id', $user->id)
        // ->get();
    
        $typeId =  explode(',', $package->pluck('type_id'));
        $typeData = Type::whereIn('id', $typeId)->get();
        
        // Check if the user has an active subscription
        $mess = null;
        $activeSubscription = false;
        
 
        // Set amount based on active subscription status
        $amount = $activeSubscription ? 1 : 5;
        // Create Razorpay order
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
        $order = $api->order->create([
            'receipt' => 'order_rcptid_11',
            'amount' => $amount * 100, // Razorpay accepts amount in paise
            'currency' => 'INR'
        ]);
        return view('renew', compact('user', 'order', 'amount'), [
            
            
            'watchlist' => $watchlist,
            'package' => $package,
            'typeData' => $typeData,
            'activeSubscription' => $activeSubscription,
            
        ]);
    }
    public function index()
    {
        $user = app('logged-in-user');
        
        
        $watchlist = app('wishlist');
        session(['watchlist' => $watchlist]);
    
        $package = Package::all();
        $typeId = $package->pluck('type_id')->toArray();
        $typeData = Type::whereIn('id', $typeId)->get();
        
        $activeSubscription = false;
    
        try {
            if (DB::getSchemaBuilder()->hasColumn('transaction', 'expiry_date')) {
                if ($user) {
                    $activeSubscription = Transction::where('user_id', $user->id)
                        ->where('expiry_date', '>', now())
                        ->exists();
    
                    if ($activeSubscription) {
                        $mess = "User has an active subscription";
                    } else {
                        Transction::where('user_id', $user->id)
                            ->update(['package_id' => 3, 'amount' => 49]);
                        $mess = "User does not have an active subscription. Package ID updated to 2.";
                    }
                } else {
                    $mess = "User not found";
                }
            } else {
                $mess = "Column 'expiry_date' not found in 'transactions' table";
            }
        } catch (\Exception $e) {
            $mess = "An error occurred: " . $e->getMessage();
        }
    
        // Check if the user already has a Razorpay subscription ID in the database
    $razorpaySubscriptionId = $user->razorpay_subscription_id ?? null;
    if (!$razorpaySubscriptionId) {
        // Create a new Razorpay subscription
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
        $createdSubscription = $api->subscription->create([
            'plan_id' => 'plan_Pbixmxa0FAKNBF',
            'customer_notify' => 1,
            'total_count' => 12, // Number of billing cycles
            'quantity' => 1,
        ]);
        $subscription = $createdSubscription;
        // Save the subscription ID to the user's record
        $user->razorpay_subscription_id = $createdSubscription['id'];
        $user->save();
    } else {
        // Retrieve the existing subscription details
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
        $subscription = $api->subscription->fetch($razorpaySubscriptionId);
    }
    $amount = $activeSubscription ? 1 : 5;
    
        return view('subscriptionFormTest', compact('user',   'watchlist', 'package', 'typeData', 'activeSubscription', 'subscription', 'amount'));
    }
    
    public function storeTransaction(Request $request)
    {
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
        
        $attributes = [
            'razorpay_subscription_id' => $request->razorpay_subscription_id,
            'razorpay_payment_id' => $request->razorpay_payment_id,
            'razorpay_signature' => $request->razorpay_signature
        ];
    
        try {
            $api->utility->verifyPaymentSignature($attributes);
    
            // Payment is successful, save to database
            $payment = $api->payment->fetch($request->razorpay_payment_id);
            if ($payment->status === 'captured') {
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
    
            // Update user subscription status
            $user = User::find($request->user_id);
            $user->subscription = 'Yes';
            $user->razorpay_subscription_id = $request->razorpay_subscription_id;
            $user->subscription_end_date = $expiryDate;
            $user->save();
            $deviceName = $request->header('User-Agent');
            $deviceIp = $request->ip();
            $sessionIdentifier = Str::uuid()->toString(); 
            Device::create([
                'user_id' => $user->id,
                'device_id' => $deviceName,
                'device_ip' => $deviceIp,
                'session_identifier' => $sessionIdentifier, 
            ]);
    
            return redirect()->route('subscribe.thankyou')->with('message', 'Subscription successful!');
        }
        else{
                return redirect()->route('subscribe')->with('message', 'errrrr');
            }
            
        } catch (\Razorpay\Api\Errors\SignatureVerificationError $e) {
            return redirect()->route('errorPage')->with('error', 'Payment verification failed!');
        } catch (\Exception $e) {
            Log::error('Transaction Store Error: ' . $e->getMessage());
            return redirect()->route('errorPage')->with('error', 'An error occurred while processing your transaction.');
        }
    }
    
    // public function storeTransaction(Request $request)
    // {
    //     $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
    
    //     $attributes = [
    //         'razorpay_order_id' => $request->razorpay_order_id,
    //         'razorpay_payment_id' => $request->razorpay_payment_id,
    //         'razorpay_signature' => $request->razorpay_signature
    //     ];
    //     // echo '<pre>';
    //     // print_r($attributes);
    //     // echo '</pre>';
    //     // die;
    //     // die;
  
    
    //     try {
    //         $api->utility->verifyPaymentSignature($attributes);
    
    //         // Payment is successful, save to database
    //         $expiryDate = now()->addDays(28);// Assuming subscription duration is 1 month
    //         // $expiryDate = now()->addMonth(); // Assuming subscription duration is 1 month
    
    //         // Set existing transactions to inactive
    //         $transactionExists =  Transction::where('user_id', $request->user_id)
    //             ->where('status', 1)
    //             ->where('amount', 1)
    //             ->where('package_id', $request->package_id,)
    //             ->exists();
    //         if($transactionExists){
    //             Transction::where('user_id', $request->user_id)
    //             ->where('status', 1)
    //             ->where('amount', 1)
    //             ->where('package_id', $request->package_id,)
    //             ->update(['status' => 1]);
    //         }
    //         else{
    //             Transction::create([
    //                 'package_id' => $request->package_id,
    //                 'amount' => $request->amount,
    //                 'user_id' => $request->user_id,
    //                 'description' => 'Subscription',
    //                 'payment_id' => $request->razorpay_payment_id,
    //                 'currency_code' => 'INR',
    //                 'expiry_date' => $expiryDate,
    //                 'status' => 1, // Active status
    //             ]);
    //         }
    //         // Store the new transaction details in the database
          
    
    //         // Update user subscription status
    //         $user = User::find($request->user_id);
    //         $user->subscription = 'Yes';
    //         $user->save();
    
    //         return redirect()->route('subscribe.thankyou')->with('message', 'Subscription successful!');
    //     } catch (\Razorpay\Api\Errors\SignatureVerificationError $e) {
    //         return redirect()->route('errorPage')->with('error', 'Payment verification failed!');
    //     } catch (\Exception $e) {
    //         Log::error('Transaction Store Error: ' . $e->getMessage());
    //         return redirect()->route('errorPage')->with('error', 'An error occurred while processing your transaction.');
    //     }
    // }
    public function storeUgradeTransaction(Request $request)
    {
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
    
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
            $transactionExists =  Transction::where('user_id', $request->user_id)
                ->where('status', 1)
                ->where('amount', 1)
                ->where('package_id', 2)
                ->exists();
            if($transactionExists){
                Transction::where('user_id', $request->user_id)
                ->where('status', 1)
                ->where('amount', 1)
                ->where('package_id', 2)
                ->update(['status' => 1]);
            }
            else{
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
            return redirect()->route('errorPage')->with('error', 'Payment verification failed!');
        } catch (\Exception $e) {
            Log::error('Transaction Store Error: ' . $e->getMessage());
            return redirect()->route('errorPage')->with('error', 'An error occurred while processing your transaction.');
        }
    }
    
}
