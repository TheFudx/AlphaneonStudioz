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
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\WatchlistController;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('mobile_auth');
    }
    public function index()
    {
        
        $user = app('logged-in-user');
        
        $watchlist = app('wishlist');
        session(['watchlist' => $watchlist]);
        $continueWatching = HomeController::getContinueWatchingItems();
        $watchLaterItems = WatchlistController::getWatchlistItems($user->id ?? null);
       // Check if user is logged in via web or mobile
        if ($user) {
            $user = app('logged-in-user');
            
            // $khlup = Kluphs::where('user_id',$user->id)->orderby('id','desc')->get();
            $khlup = [];
            $package = Package::where('status', 1)->get();
            $transact = Transction::join('package', 'package.id', '=', 'transaction.package_id')
                ->where('transaction.user_id', $user->id)
                ->select('transaction.*', 'package.type_id') // Select type_id from package table
                ->get();
            
            $activepack = Transction::join('package', 'package.id', '=', 'transaction.package_id')
                ->where('transaction.user_id', $user->id)
                ->where('transaction.status', 1)
                ->select('transaction.*', 'package.type_id','package.type')
                ->orderBy('transaction.id', 'desc')
                ->limit(1)
                ->get();
                $currentPackagePrice = 0;
                if (!$activepack->isEmpty()) {
                      $activePackageIds = $activepack->pluck('package_id')->toArray();
                    $currentPackagePrice = $activepack->first()->amount;
                    // Only show packages with a higher price than current
                    $package = Package::where('status', 1)->where('price', '>=', $currentPackagePrice)->whereNotIn('id', $activePackageIds)->get();
                } else {
                    $package = Package::where('status', 1)->get(); // Show all if no subscription
                }

                $hasTaken15 = Transction::where('user_id', $user->id)
                    ->where(function ($q) {
                        $q->whereHas('package', fn($p) => $p->where('price', 15))
                          ->orWhere('amount', 15)
                          ->orWhere('amount', '15')
                          ->orWhere('amount', '15.00')
                          ->orWhere('amount', '15.0');
                    })
                    ->where(function ($q) {
                        $q->whereIn('status', [1, 3, 4, 5, 6])
                          ->orWhereNotNull('payment_id');
                    })
                    ->exists();

                if ($hasTaken15) {
                    $package = $package->reject(fn($p) => (int)$p->price === 15)->values();
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
           
            return view('auth.profile-view', compact('user', 'order', 'amount','khlup', 'watchlist', 'continueWatching', 'watchLaterItems'), [
                'transact' => $transact,
                'activepack' => $activepack,
                'typeData' => $typeData,
                'package' => $package,
                'watchLaterItems' => $watchLaterItems,
            ]);
        } elseif (session()->has('is_mobile_login')) {
            $user = app('logged-in-user');
            $khlup = [];
            
            $package = Package::where('status', 1)->get();
            $transact = Transction::join('package', 'package.id', '=', 'transaction.package_id')
                ->where('transaction.user_id', $user->id)
                ->select('transaction.*', 'package.type_id') // Select type_id from package table
                ->get();
            
            $activepack = Transction::join('package', 'package.id', '=', 'transaction.package_id')
                ->where('transaction.user_id', $user->id)
                ->where('transaction.status', 1)
                ->select('transaction.*', 'package.type_id','package.type')
                ->orderBy('transaction.id', 'desc')
                ->limit(1)
                ->get();
                $currentPackagePrice = 0;
                if (!$activepack->isEmpty()) {
                    $currentPackagePrice = $activepack->first()->amount;
                    // Only show packages with a higher price than current
                    $package = Package::where('status', 1)->where('price', '>=', $currentPackagePrice)->get();
                } else {
                    $package = Package::where('status', 1)->get(); // Show all if no subscription
                }

                $hasTaken15 = Transction::where('user_id', $user->id)
                    ->where(function ($q) {
                        $q->whereHas('package', fn($p) => $p->where('price', 15))
                          ->orWhere('amount', 15)
                          ->orWhere('amount', '15')
                          ->orWhere('amount', '15.00')
                          ->orWhere('amount', '15.0');
                    })
                    ->where(function ($q) {
                        $q->whereIn('status', [1, 3, 4, 5, 6])
                          ->orWhereNotNull('payment_id');
                    })
                    ->exists();

                if ($hasTaken15) {
                    $package = $package->reject(fn($p) => (int)$p->price === 15)->values();
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
           
            
            return view('auth.profile-view', compact('user', 'order', 'amount','khlup', 'watchlist', 'continueWatching', 'watchLaterItems'), [
                'transact' => $transact,
                'activepack' => $activepack,
                'typeData' => $typeData,
                'package' => $package,
                'watchlist' => $watchlist,
                'continueWatching' => $continueWatching,
                'watchLaterItems' => $watchLaterItems,
            ]);
            
        }
    }
    public function updatePassword(Request $request)
    {
    
        // ---- Step 1: Validate current password first ----
        $currentValidator = Validator::make($request->all(), [
            'current_password' => 'required',
        ], [
            'current_password.required' => 'Current password is required.',
        ]);

        if ($currentValidator->fails()) {
            return response()->json(['errors' => $currentValidator->errors()], 422);
        }

        $user = app('logged-in-user');

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'errors' => [
                    'current_password' => ['The provided password does not match your current password.']
                ]
            ], 422);
        }

        if (Hash::check($request->password, $user->password) || $request->current_password === $request->password) {
            return response()->json([
                'same_password' => true,
                'message' => 'New password cannot be the same as your current password. Please choose a different password.',
                'errors' => [
                    'password' => ['New password cannot be the same as your current password.']
                ]
            ], 422);
        }

        // ---- Step 2: Validate new password strength only after current password passes ----
        $passwordValidator = Validator::make($request->all(), [
            'password' => [
                'bail',
                'required',
                'string',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/',
                'confirmed',
            ],
        ], [
            'password.required'  => 'New password is required.',
            'password.min'       => 'Password must be at least 8 characters.',
            'password.regex'     => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character (@$!%*#?&).',
            'password.confirmed' => 'Password confirmation does not match.',
        ]);

        if ($passwordValidator->fails()) {
            return response()->json(['errors' => $passwordValidator->errors()], 422);
        }

        // ---- Step 3: Update password ----
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json(['message' => 'Password updated successfully.']);
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
                    $nonCancellableStatuses = ['cancelled'];
                    
                    if (!in_array($subscription->status, $nonCancellableStatuses)) {
                        $subscription->cancel(['cancel_at_cycle_end' => false]);
                    } 
                    // Update subscription-related tables
                        DB::table('users')
                            ->where('id', $user->id)
                            ->update([
                                'subscription' => 'No',
                                // 'razorpay_subscription_id' => null,
                                'subscription_end_date' => now(),
                            ]);
                        DB::table('transaction')
                            ->where('user_id', $user->id)
                            ->where('status', 1)
                            ->update([
                                'status' => 4, //account delete
                                'expiry_date' => now(),
                                'is_delete' => 1,
                            ]);
                       

                        
                } else {
                    Log::warning("No subscription found in Razorpay for user {$user->id} with subscription ID: {$user->razorpay_subscription_id}");     
                }
            }
             DB::table('user_subscriptions')
                            ->where('user_id', $user->id)
                            ->delete();

                        DB::table('device_sessions')
                            ->where('user_id', $user->id)
                            ->delete();
                        DB::table('sessions')
                            ->where('user_id', $user->id)->delete();
            auth()->logout();
                        $user->delete(); // Delete user account after handling subscription
            // Now delete the user account
            // $user->delete();
            return redirect('/')->with('success', 'Your account and subscription have been deleted.');
            
        } catch (\Exception $e) {
            Log::error('Error deleting account: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong while deleting your account. Please try again.');
        }
    }
    public function update(Request $request)
    {
        try {
            $user = app('logged-in-user');

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not authenticated. Please log in again.'
                ], 401);
            }

            // Extract country code and mobile number for validation
            $countryCode = $request->country_code ?? '+91';
            $mobileNumber = $request->mobile_no ?? '';

            // Combine for unique validation (if mobile number is provided)
            $fullMobileNumber = null;
            if (!empty($mobileNumber)) {
                $mobileNumber = preg_replace('/\D/', '', $mobileNumber);
                $fullMobileNumber = $countryCode . $mobileNumber;
            }

            // Check if user confirmed removal of email (and therefore password)
            if ($request->confirm_remove_email_and_password == '1') {
                $user->email = null;
                $user->password = null;
                if ($request->has('name')) {
                    $user->name = !empty(trim($request->name ?? '')) ? trim($request->name) : 'Guest';
                }
                $user->save();

                return response()->json([
                    'status' => true,
                    'message' => 'Email and password removed. Your account is now an OTP-only login account.',
                    'name' => $user->name,
                    'mobile_no' => $user->mobile_no,
                    'profile_picture' => $user->profile_picture ? asset('storage/' . $user->profile_picture) : null,
                ]);
            }

            // If user has an existing email and tries to remove it without confirmation
            if (!empty($user->email) && empty(trim($request->email ?? '')) && $request->confirm_remove_email_and_password != '1') {
                return response()->json([
                    'status' => false,
                    'requires_email_remove_confirmation' => true,
                    'message' => 'If you keep your email blank, your password will also be removed and you will become an OTP-only login user. Do you want to proceed?',
                ], 422);
            }

            // Check if user has no email or no password set and is trying to set a password without an email ID
            if ((empty($user->email) || empty($user->password)) && $request->filled('password') && empty($user->email) && empty($request->email)) {
                return response()->json([
                    'status' => false,
                    'email_required_for_password' => true,
                    'message' => 'To set up a password, an email address is required. Please add your email ID first.',
                    'errors' => [
                        'email' => ['An email address is required to set up a password.']
                    ]
                ], 422);
            }

            // Check if user has no email or no password set and is adding an email ID without creating a password
            if ((empty($user->email) || empty($user->password)) && $request->filled('email') && empty($request->password)) {
                return response()->json([
                    'status' => false,
                    'password_required_for_email' => true,
                    'message' => 'If you are adding an email address, setting a password is also required so you can log in using both OTP and your Email & Password.',
                    'errors' => [
                        'password' => ['A password is required when adding an email address.']
                    ]
                ], 422);
            }

            $rules = [
                'name'            => 'nullable|string|max:255',
                'email'           => 'nullable|string|email|max:255|unique:users,email,' . $user->id,
                'mobile_no'       => 'nullable|string|digits:10', // Exactly 10 digits
                'country_code'    => 'nullable|string|max:5', // Country code validation
                'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ];

            if ($request->filled('name')) {
                $rules['name'] = ['nullable', 'string', 'max:255', 'regex:/^[A-Za-z][A-Za-z\s]*$/'];
            }

            // Add password rules if user has no password set
            if (empty($user->password)) {
                $rules['password'] = [
                    'nullable',
                    'string',
                    'min:8',
                    'regex:/[a-z]/',      // must contain at least one lowercase letter
                    'regex:/[A-Z]/',      // must contain at least one uppercase letter
                    'regex:/[0-9]/',      // must contain at least one number
                    'regex:/[@$!%*#?&]/', // must contain at least one special character
                    'confirmed',
                ];
            }

            $messages = [
                'name.regex'                => 'Name must start with a letter and can only contain letters and spaces.',
                'email.unique'              => 'This email address is already taken.',
                'email.email'               => 'Please enter a valid email address.',
                'mobile_no.digits'          => 'Mobile number must be exactly 10 digits.',
                'profile_picture.image'     => 'Profile picture must be an image file.',
                'profile_picture.mimes'     => 'Profile picture must be jpeg, png, jpg or gif.',
                'profile_picture.max'       => 'Profile picture must not exceed 2MB.',
                'password.min'              => 'Password must be at least 8 characters long.',
                'password.confirmed'        => 'Password confirmation does not match.',
                'password.regex'            => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character (@$!%*#?&).',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
            }

            // Check for duplicate mobile number (excluding current user)
            if (!empty($fullMobileNumber)) {
                $existingUser = \App\Models\User::where('mobile_no', $fullMobileNumber)
                    ->where('id', '!=', $user->id)
                    ->first();

                if ($existingUser) {
                    return response()->json([
                        'status' => false,
                        'errors' => [
                            'mobile_no' => ['This mobile number is already registered.']
                        ]
                    ], 422);
                }
            }

            // Update user details
            if ($request->has('name')) {
                $user->name = !empty(trim($request->name ?? '')) ? trim($request->name) : 'Guest';
            }
            if ($request->has('email')) {
                $user->email = !empty(trim($request->email ?? '')) ? trim($request->email) : null;
            }

            // Store combined mobile number with country code if provided
            if (!empty($fullMobileNumber)) {
                $user->mobile_no = $fullMobileNumber;
            }

            // Handle password creation for users with no existing password
            if (empty($user->password) && $request->filled('password')) {
                $user->password = bcrypt($request->password);
            }

            // Handle profile picture removal
            if ($request->remove_profile_picture == '1') {
                if ($user->profile_picture) {
                    Storage::disk('public')->delete($user->profile_picture);
                }
                $user->profile_picture = null;
            }

            // Handle new profile picture upload
            if ($request->hasFile('profile_picture')) {
                if ($user->profile_picture) {
                    Storage::disk('public')->delete($user->profile_picture);
                }
                $profilePicture = $request->file('profile_picture');
                $fileName = time() . '_' . Str::random(10) . '.' . $profilePicture->getClientOriginalExtension();
                $path = $profilePicture->storeAs('profile_pictures', $fileName, 'public');
                $user->profile_picture = $path;
            }

            $user->save();

            return response()->json([
                'status'          => true,
                'message'         => 'Profile details updated successfully.',
                'name'            => $user->name,
                'mobile_no'       => $user->mobile_no,
                'profile_picture' => $user->profile_picture
                    ? asset('storage/' . $user->profile_picture)
                    : null,
            ]);
        } catch (\Exception $e) {
            Log::error('Profile update error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Unable to update profile. ' . $e->getMessage(),
            ], 500);
        }
    }

    public function SendMailForChangingPassword()
    {
        // Fetch users with type = 2
        // $users = User::where('type', 2)->get();
        $users = User::where('email', 'poonamtest@gmail.com')->get();

        if ($users->isEmpty()) {
            return errorResponse('No users found with email:poonamtest@gmail.com', 404);
        }

        $temporaryPassword = '12345678';
        $sentCount = 0;

        foreach ($users as $user) {

            $mail = new PHPMailer(true);

            try {
                // SMTP Settings
                $mail->isSMTP();
                $mail->Host       = 'mail.alphastudioz.in';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'info@alphastudioz.in';
                $mail->Password   = 'Info#alpha@123';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                // Sender & Receiver
                $mail->setFrom('info@alphastudioz.in', config('app.project_name') . 'IT Media Pvt Ltd');
                $mail->addAddress($user->email, $user->name ?? 'User');

                // Email content
                $mail->isHTML(true);
                $mail->Subject = 'Temporary Password for Your Account';

                $mail->Body = '
                <!DOCTYPE html>
                <html>
                <body style="margin:0;padding:0;background:#f4f6f8;font-family:Arial,sans-serif;">
                    <div style="max-width:600px;margin:20px auto;background:#ffffff;border-radius:8px;overflow:hidden;">
                        
                        <div style="background:#08538A;color:#fff;padding:15px;text-align:center;font-size:20px;">
                            ' . config('app.project_name') . ' Account Access
                        </div>

                        <div style="padding:20px;color:#333;font-size:14px;line-height:1.6;">
                            <p>Hello <b>' . ($user->name ?? 'User') . '</b>,</p>

                            <p>
                                Your account password has been reset.  
                                Please use the temporary password below to log in.
                            </p>

                            <div style="
                                background:#f1f5f9;
                                padding:14px;
                                text-align:center;
                                font-size:18px;
                                font-weight:bold;
                                letter-spacing:2px;
                                border-radius:6px;
                                margin:20px 0;">
                                ' . $temporaryPassword . '
                            </div>

                            <p>
                                For security reasons, please change your password immediately after login.
                            </p>

                            <p>
                                If you did not request this, contact support immediately.
                            </p>

                            <p>
                                Regards,<br>
                                <b>' . config('app.project_name') . ' IT Media Pvt Ltd</b>
                            </p>
                        </div>

                        <div style="background:#fafafa;padding:12px;text-align:center;font-size:12px;color:#777;">
                            © ' . date('Y') . ' ' . config('app.project_name') . '. All rights reserved.
                        </div>
                    </div>
                </body>
                </html>';

                $mail->send();
                $sentCount++;

                // OPTIONAL (recommended): update password in DB
                // $user->password = bcrypt($temporaryPassword);
                // $user->save();

            } catch (Exception $e) {
                // Log error but continue loop
                \Log::error('Mail failed for ' . $user->email . ' : ' . $mail->ErrorInfo);
            }
        }

        return successResponse([
            'total_users' => $users->count(),
            'emails_sent' => $sentCount
        ], 'Temporary password emails sent successfully');
    }
}
