<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Package;
use App\Models\Package_Details;
use App\Models\Transction;
use App\Models\UserSubscription;
use App\Models\Kluphs;
use Illuminate\Support\Str;
Use App\Models\Type;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use VideoThumbnail;
use Validator;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('mobile_auth');
    }
    public function index()
    {
        
        $user = app('logged-in-user');
        
        
       // Check if user is logged in via web or mobile
        if ($user) {
            $user = app('logged-in-user');
            
            $khlup = Kluphs::where('user_id',$user->id)->orderby('id','desc')->get();
            $package = Package::all();
            $transact = Transction::join('package', 'package.id', '=', 'transaction.package_id')
                ->where('transaction.user_id', $user->id)
                ->select('transaction.*', 'package.type_id') // Select type_id from package table
                ->get();
            
            $activepack = Transction::join('package', 'package.id', '=', 'transaction.package_id')
                ->where('transaction.user_id', $user->id)
                ->where('transaction.status', 1) // Specify the status column from the transaction table
                ->select('transaction.*', 'package.type_id','package.type') // Select type_id from package table
                ->get();
                $currentPackagePrice = 0;
                if (!$activepack->isEmpty()) {
                      $activePackageIds = $activepack->pluck('package_id')->toArray();
                    $currentPackagePrice = $activepack->first()->amount;
                    // Only show packages with a higher price than current
                    $package = Package::where('price', '>=', $currentPackagePrice)->whereNotIn('id', $activePackageIds)->get();
                } else {
                    $package = Package::all(); // Show all if no subscription
                }
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
            $amount = $activeSubscription ? 1 : 49;
             $order = '';
            if(env('APP_ENV') == 'production'){
                // Create Razorpay order
                $api = new Api(config('app.razorpay_key'), config('app.razorpay_secret'));    
                $order = $api->order->create([
                    'receipt' => 'order_rcptid_11',
                    'amount' => $amount * 100, // Razorpay accepts amount in paise
                    'currency' => 'INR'
                ]);
            }
           
            return view('auth.profile-view', compact('user', 'order', 'amount','khlup'), [
                'transact' => $transact,
                'activepack' => $activepack,
                'typeData' => $typeData,
                'package' => $package,
            ]);
        } elseif (session()->has('is_mobile_login')) {
            $user = app('logged-in-user');
            
            
            $package = Package::all();
            $transact = Transction::join('package', 'package.id', '=', 'transaction.package_id')
                ->where('transaction.user_id', $user->id)
                ->select('transaction.*', 'package.type_id') // Select type_id from package table
                ->get();
            
            $activepack = Transction::join('package', 'package.id', '=', 'transaction.package_id')
                ->where('transaction.user_id', $user->id)
                ->where('transaction.status', 1) // Specify the status column from the transaction table
                ->select('transaction.*', 'package.type_id','package.type') // Select type_id from package table
                ->get();
                $currentPackagePrice = 0;
                if (!$activepack->isEmpty()) {
                    $currentPackagePrice = $activepack->first()->amount;
                    // Only show packages with a higher price than current
                    $package = Package::where('price', '>=', $currentPackagePrice)->get();
                } else {
                    $package = Package::all(); // Show all if no subscription
                }
            
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
            $amount = $activeSubscription ? 1 : 49;
    
            // Create Razorpay order
            $api = new Api(config('app.razorpay_key'), config('app.razorpay_secret'));    
            $order = $api->order->create([
                'receipt' => 'order_rcptid_11',
                'amount' => $amount * 100, // Razorpay accepts amount in paise
                'currency' => 'INR'
            ]);
        
            
            return view('auth.profile-view', compact('user', 'order', 'amount','khlup'), [
                'transact' => $transact,
                'activepack' => $activepack,
                'typeData' => $typeData,
                'package' => $package,
            ]);
            
        }
    }
    public function updatePassword(Request $request)
    {
        Log::info('called-update-password');
            $request->validate([
                'current_password' => 'nullable',
                'password' => 'required|confirmed|min:8',
            ]);
            $user = app('logged-in-user');
            
            if ($request->current_password && !Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'The provided password does not match your current password.']);
            }
    
        // Update the user's password
        $user->password = Hash::make($request->password);
        $user->save();
            return redirect('/profile/view')->with('success', 'Password updated successfully.');
    }
    public function deleteAccount(Request $request)
    {
        $user = app('logged-in-user');
        try {
            // Check if user has an active Razorpay subscription
            if (!empty($user->razorpay_subscription_id)) {
                $api = new Api(config('app.razorpay_key'), config('app.razorpay_secret'));    
                $subscription = $api->subscription->fetch($user->razorpay_subscription_id);
                
                if ($subscription) {
                    $subscription->cancel(['cancel_at_cycle_end' => false]);
                    // Update subscription-related tables
                    DB::table('users')
                        ->where('id', $user->id)
                        ->update([
                            'subscription' => 'No',
                            'razorpay_subscription_id' => null,
                            'subscription_end_date' => now(),
                        ]);
                    DB::table('transaction')
                        ->where('user_id', $user->id)
                        ->where('status', 1)
                        ->update([
                            'status' => 0,
                            'expiry_date' => now(),
                        ]);
                    DB::table('user_subscriptions')
                        ->where('user_id', $user->id)
                        ->delete();
                        
                    DB::table('device_sessions')
                        ->where('user_id', $user->id)
                        ->delete();
                }
            }
            // Now delete the user account
            $user->delete();
            return redirect('/')->with('success', 'Your account and subscription have been deleted.');
            
        } catch (\Exception $e) {
            Log::error('Error deleting account: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong while deleting your account. Please try again.');
        }
    }
    public function update(Request $request)
    {
        Log::info('called-update');
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'string|email|max:255',
            'profile_picture' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust validation rules as needed
        ]);
        $user = app('logged-in-user');
        // Update user details
        $user->name = $request->name;
        // $user->email = $request->email;
        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete previous profile picture if exists
            if ($user->profile_picture) {
                Storage::disk('public')->delete('profile_pictures/' . $user->profile_picture);
            }
            $profilePicture = $request->file('profile_picture');
            $fileName = time() . '_' . Str::random(10) . '.' . $profilePicture->getClientOriginalExtension();
            $profilePicture->storeAs('profile_pictures', $fileName, 'public');
            $user->profile_picture = $fileName;
        }
        $user->update();
        return redirect('/profile/view')->with('success', 'Profile updated successfully.');
    }

    
}
