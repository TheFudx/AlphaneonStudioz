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
use App\Models\DeviceSession;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\UserSubscription;
use Carbon\Carbon;
use App\Helpers\Helpers;
class SubscriptionController extends Controller
{
   
    public function devicelimit(){
        $user = session('user_id');
       
        
        $watchlist = app('wishlist');
        session(['watchlist' => $watchlist]);
    
        $package = Package::all();
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
            $package = Package::where('price', '>=', $currentPackagePrice)->get();
        } else {
            $package = Package::all(); // Show all if no subscription
        }
    
        $typeId =  explode(',', $package->pluck('type_id'));
        $typeData = Type::whereIn('id', $typeId)->get();
        
        // Check if the user has an active subscription
        $mess = null;
        $activeSubscription = false;
        
        $logedInDevice = DeviceSession::where('user_id', $user)->get();
        // Set amount based on active subscription status
        $amount = $activeSubscription ? 15 : 149;
        
        return view('nodevice', compact('user', 'logedInDevice','amount'), [
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
    
        $typeIds = $package->pluck('type_id') // this gives all type_id values
        ->flatMap(function($ids) {
            return explode(',', $ids); // assuming each type_id is comma-separated
        })
        ->unique() // optional: remove duplicates
        ->toArray();
        $typeData = Type::whereIn('id', $typeIds)->get();
        
        // Check if the user has an active subscription
        $mess = null;
        $activeSubscription = false;
        
 
        // Set amount based on active subscription status
        $amount = $activeSubscription ? 1 : 5;
        // Create Razorpay order
        // $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
        $api = new Api (config('app.razorpay_key'), config('app.razorpay_secret'));
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
    // public function createSubscription(Request $request)
    // {
    //     $user = app('logged-in-user');
    //     $planId = $request->plan_id;
    
    //     $api = new Api(config('app.razorpay_key'), config('app.razorpay_secret'));
    
    //     $subscription = $api->subscription->create([
    //         'plan_id' => $planId,
    //         'customer_notify' => 1,
    //         'total_count' => 12,
    //     ]);
    
    //     // Optional: save to user or log
    
    //     return response()->json(['subscription_id' => $subscription['id']]);
    // }
    public function createSubscription(Request $request)
    {
       
        try {
            $user = app('logged-in-user');
            $planId = $request->plan_id;
            $packageId = $request->package_id;
    
            // Check if an active or pending subscription exists
            $existing = UserSubscription::where('user_id', $user->id)
                ->where('package_id', $packageId)->where('status',1)
                ->latest()
                ->first();
    
            if ($existing) {
                // Fetch subscription from Razorpay
                $api = new Api(config('app.razorpay_key'), config('app.razorpay_secret'));
                $subscription = $api->subscription->fetch($existing->razorpay_subscription_id);
    
                // If still created (not completed or canceled), return it
                if (in_array($subscription->status, ['created', 'pending'])) {
                    return response()->json([
                        'subscription_id' => $subscription->id,
                        'status' => 'existing',
                        'message' => 'Using existing pending subscription.',
                    ]);
                }
            }
    
            // If no usable subscription found, create a new one
            $api = new Api(config('app.razorpay_key'), config('app.razorpay_secret'));
            $newSubscription = $api->subscription->create([
                'plan_id' => $planId,
                'customer_notify' => 1,
                'total_count' => 12,
                'quantity' => 1,
            ]);
            // Save to DB
            UserSubscription::create([
                'user_id' => $user->id,
                'razorpay_subscription_id' => $newSubscription['id'],
                'package_id' => $packageId,
                 'status'=> 0
            ]);
    
            return response()->json([
                'subscription_id' => $newSubscription['id'],
                'status' => 'created',
                'message' => 'New subscription created.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Failed to create or reuse subscription.',
                'details' => $e->getMessage()
            ], 500);
        }
    }
    
    public function index(Request $request)
    {
        $user = app('logged-in-user');
        
        
        $watchlist = app('wishlist');
        session(['watchlist' => $watchlist]);
    
        $package = Package::all();
        $typeIds = $package->pluck('type_id') // this gives all type_id values
                    ->flatMap(function($ids) {
                        return explode(',', $ids); // assuming each type_id is comma-separated
                    })
                    ->unique() // optional: remove duplicates
                    ->toArray();
        $typeData = Type::whereIn('id', $typeIds)->get();
        $activeSubscription = false;
        $user_subscription = UserSubscription::with('user')->whereHas('user', function ($q)  {
                        $q->where('subscription', 'Yes'); // 'type' column in users table
                    })
                                ->where('user_id',$user->id)->where('status',1)->first();
        $activePackageId = $user_subscription ? $user_subscription->package_id : null ;
        
        try {
            if (DB::getSchemaBuilder()->hasColumn('transaction', 'expiry_date')) {
                if ($user) {
                    $activeSubscription = Transction::where('user_id', $user->id)
                        ->where('expiry_date', '>', now())
                        ->exists();
    
                    if ($activeSubscription) {
                        
                        $mess = "User has an active subscription";
                    } else {
                       
                        $mess = "User does not have an active subscription";
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
    
         $amount = $activeSubscription ? 15 : 149;
    
        return view('subscription', compact('user',  'activePackageId', 'watchlist', 'package', 'typeData', 'activeSubscription', 'amount'));
    }
    
    public function storeTransaction(Request $request)
    {
        $api = new Api(config('app.razorpay_key'), config('app.razorpay_secret'));        
        $attributes = [
            'razorpay_subscription_id' => $request->razorpay_subscription_id,
            'razorpay_payment_id' => $request->razorpay_payment_id,
            'razorpay_signature' => $request->razorpay_signature
        ];
        try {
            $api->utility->verifyPaymentSignature($attributes);
            $payment = $api->payment->fetch($request->razorpay_payment_id);
            $user_subscription = UserSubscription::where('razorpay_subscription_id', $request->razorpay_subscription_id)->first();
             

            if ($payment->status === 'captured') {
                Log::info('Amount received: ' . $request->amount);
                $amountInRupees = $request->amount / 100;
                $package = Package::find($request->package_id);
                if (!$package) {
                    return redirect()->route('subscribe')->with('message', 'Invalid package selected.');
                }
                $type = strtolower(trim($package->type)); // normalize to lowercase
                if ($type === 'month') {
                    $expiryDate = Carbon::now()->addDays(28)->format('Y-m-d H:i:s');
                } elseif ($type === 'year') {
                    $expiryDate = Carbon::now()->addYear()->format('Y-m-d H:i:s');
                } else {
                    $expiryDate = Carbon::now()->addDays(28)->format('Y-m-d H:i:s'); // fallback
                }
                $existingTransaction = Transction::where('payment_id', $request->razorpay_payment_id)->first();
                if ($existingTransaction) {
                    return redirect()->route('subscribe.thankyou')->with('message', 'Subscription already processed!');
                }
                $user = User::find($request->user_id);
          
            if (!empty($user->razorpay_subscription_id)) {
                try {
                    $oldSubscription = $api->subscription->fetch($user->razorpay_subscription_id);
                    if ($oldSubscription && $oldSubscription->status !== 'cancelled') {
                        $oldSubscription->cancel(['cancel_at_cycle_end' => false]);
                    }
                    // Deactivate old transaction
                    Transction::where('user_id', $user->id)
                        ->where('status', 1)
                        ->update(['status' => 0, 'expiry_date' => Carbon::now()->format('Y-m-d H:i:s')]);
                    // Update user data
                    $user->razorpay_subscription_id = null;
                    $user->subscription = 'No';
                    $user->subscription_end_date = Carbon::now()->format('Y-m-d H:i:s');
                    $user->save();
                    // Optional: Delete or update user_subscription table if used
                    // DB::table('user_subscription')->where('user_id', $user->id)->delete();
                    $user_subscription->status = 3;
                    $user_subscription->update();

                } catch (\Exception $ex) {
                    Log::warning('Old subscription cancellation failed: ' . $ex->getMessage());
                }
            }
                // Deactivate previous
                Transction::where('user_id', $request->user_id)
                    ->where('status', 1)
                    ->update(['status' => 0]);
                // Save transaction
                Transction::create([
                    'package_id' => $request->package_id,
                    'amount' => $amountInRupees,
                    'user_id' => $request->user_id,
                    'description' => 'Subscription',
                    'payment_id' => $request->razorpay_payment_id,
                    'currency_code' => 'INR',
                    'expiry_date' => $expiryDate,
                    'status' => 1,
                    'transaction_count' => 1,
                ]);
                $user->subscription = 'Yes';
                $user->razorpay_subscription_id = $request->razorpay_subscription_id;
                $user->subscription_end_date = $expiryDate;
                $user->save();

                $user_subscription->status = 1;
                $user_subscription->update();

                registerDeviceSession($request);
                return redirect()->route('subscribe.thankyou')->with('message', 'Subscription successful!');
            } else {
                return redirect()->route('subscribe')->with('message', 'Payment not captured.');
            }
            } catch (\Razorpay\Api\Errors\SignatureVerificationError $e) {
                return redirect()->route('errorPage')->with('error', 'Payment verification failed!');
            } catch (\Exception $e) {
                Log::error('Transaction Store Error: ' . $e->getMessage());
                return redirect()->route('errorPage')->with('error', 'An error occurred while processing your transaction.');
            }
        }
        
        public function cancelSubscription(Request $request)
        {
            
        $user = app('logged-in-user');
        
            if (!$user->razorpay_subscription_id) {
                Log::warning("No subscription found for User ID: {$user->id}");
                return redirect('/')->with('error', 'No active subscription found.');
            }
            $subscriptionId = $user->razorpay_subscription_id;
            try {
                // Cancel Razorpay subscription
                $api = new Api(config('app.razorpay_key'), config('app.razorpay_secret'));  
                $subscription = $api->subscription->fetch($subscriptionId);
                $subscription->cancel();
                Log::info("Razorpay subscription cancelled for ID: $subscriptionId");
                // Cleanup local data
                 $user_subscription = UserSubscription::where('razorpay_subscription_id', $subscriptionId)->first();

                Transction::where('user_id', $user->id)
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
                // UserSubscription::where('razorpay_subscription_id', $subscriptionId)->delete();
                $user_subscription->status = 3;
                $user_subscription->update();
                
                // Remove current device from device_session
                // $deviceId = hash('sha256', $request->ip() . $request->userAgent());
                DeviceSession::where('user_id', $user->id)
                    // ->where('device_id', $deviceId)
                    ->delete();
                Log::info("Device session removed for User ID: {$user->id}");
                return redirect('/')->with('success', 'Subscription cancelled and device removed successfully.');
            } catch (\Exception $e) {
                Log::error("Error cancelling subscription for User ID: {$user->id}, Error: " . $e->getMessage());
                return redirect('/')->with('error', 'Failed to cancel subscription. Please try again.');
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
        // $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
        $api = new Api (config('app.razorpay_key'), config('app.razorpay_secret'));
    
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
            return redirect()->route('errorPage')->with('error', 'Payment verification failed!');
        } catch (\Exception $e) {
            Log::error('Transaction Store Error: ' . $e->getMessage());
            return redirect()->route('errorPage')->with('error', 'An error occurred while processing your transaction.');
        }
    }
    
}
