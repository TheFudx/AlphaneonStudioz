<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Device;
use App\Models\Package;
use App\Models\Package_Details;
use App\Models\Transction;
use App\Models\UserRole;
use Illuminate\Support\Facades\Route;
use App\Providers\RouteServiceProvider;
use App\Services\DeviceService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Browser;
use App\Helpers\Helpers;
use App\Models\DeviceSession;
use App\Models\UserLocations;

class LoginController extends Controller
{
    /*
 |--------------------------------------------------------------------------
 | Show Login Form with Auth URL
 |--------------------------------------------------------------------------
 */
    public function showLoginForm()
    {
        $CLIENT_ID = '18549902682527194455';
        $REDIRECT_URL = url('/auth/callback');
        $AUTH_URL = 'https://auth.phone.email/log-in?client_id=' . $CLIENT_ID . '&redirect_url=' . $REDIRECT_URL;
        return view('auth.login', compact('AUTH_URL'));
    }
    // public function login(Request $request){
    //     // dd($request->all());
    //     $request->validate([
    //         'email' => 'required|string',
    //         'password' => 'required|string',
    //     ]);

    //         $credentials = [
    //             'email' => $request->email,
    //             'password' => $request->password,
    //             'type' => 5,
    //         ];

    //         // Attempt login with username, password, and type
    //         if (Auth::attempt($credentials)) {
    //             // Authentication passed...
    //             registerDeviceSession($request);
    //             session(['user_id' => auth()->user()->id]);
    //             return redirect()->intended('home'); // Or wherever you want
    //         }

    //         // Authentication failed...
    //         return back()->withErrors(['login_error' => 'Invalid credentials or user type']);
    // }
    
    use AuthenticatesUsers;

    /**
     * Override failed login response to show polite OTP login guidance.
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $email = $request->input('email');
        $user = User::where('email', $email)->first();

        // If user exists AND has a password set -> show incorrect password error popup
        if ($user && !empty($user->password)) {
            return redirect()->back()
                ->withInput($request->only('email', 'remember'))
                ->with('login_error_alert', [
                    'title' => 'Incorrect Password',
                    'message' => 'The password you entered is incorrect. Please check your password and try again.',
                ])
                ->withErrors([
                    'password' => ['Incorrect password. Please try again.'],
                ]);
        }

        // Otherwise (user does not exist OR user exists but has no password set yet):
        $message = "If you are new or haven't set up a password yet, please log in using the 'Continue With OTP' option. After logging in, you can add your email ID and set a password in your Profile section to enable both OTP and password login.";

        return redirect()->back()
            ->withInput($request->only('email', 'remember'))
            ->with('otp_suggestion_alert', [
                'title' => 'Account Notice',
                'message' => $message,
            ])
            ->withErrors([
                'email' => [$message],
            ]);
    }
    
    
    public function authenticated(Request $request, $user)
    {
        // ✅ No subscription → set flag and go straight to home
        // Popup fires on home page. Skip device/transaction checks entirely.
        if ($user->subscription !== 'Yes') {
            // session(['auto_open_payment' => true]);
            // return redirect('/home');  // ← immediate return, nothing else runs
            return redirect(url('/?open_modal=subscription'));
        }
    
        // From here: user HAS a subscription — run device/transaction checks
        $transaction = Transction::where('user_id', $user->id)
            ->where('status', 1)
            ->orderBy('created_at', 'desc')
            ->first();
    
        if ($transaction) {
            registerDeviceSession($request);
            $package     = $transaction->package;
            $deviceCount = DB::table('device_sessions')
                ->where('user_id', $user->id)
                ->count();
    
            if ($deviceCount > $package->no_of_device) {
                removeDeviceSession($request);
                session(['user_id' => $user->id]);
                Auth::logout();
                return redirect('/reach-device-limit')
                    ->withErrors(['You have reached your device limit.']);
            }
        } else {
            return redirect('/')->withErrors(['You do not have an active subscription.']);
        }
    
        return redirect()->intended('/home');
    }
    /*
    |--------------------------------------------------------------------------
    | Handle Callback and Login Logic
    |--------------------------------------------------------------------------
    */
    //OTP Login Logic
    public function handleCallback(Request $request)
    {
        
        $intendedUrl = session()->get('url.intended', '/home');

        if (!$request->has('access_token')) {
            return redirect()->route('login')->withErrors(['Access token not provided.']);
        }

        // Call Phone Email API
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://eapi.phone.email/getuser',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'client_id' => '18549902682527194455',
                'access_token' => $_GET['access_token'],
            ]),
        ));
        $response = curl_exec($curl);
        Log::info('Phone.email API response', ['response' => $response]);
        if (curl_errno($curl)) {
            return redirect()->route('login')->withErrors(['Error fetching user details.']);
        }
        curl_close($curl);
        $response_data = json_decode($response, true);
        if (!isset($response_data['status']) || $response_data['status'] !== 200) {
            return redirect()->route('login')->withErrors(['Invalid response from authentication server.']);
        }
        $country_code = $response_data['country_code'];
        $phone_no = $response_data['phone_no'];
        $mobile_no = $country_code . $phone_no;
        $fullname = $response_data['first_name'] . ' ' . $response_data['last_name'];

        $existingUser = User::where('mobile_no', $mobile_no)->orWhere('mobile_no',$phone_no)->first();

        if ($existingUser) {
            // Block if user already registered via a different method
            // if ($existingUser->type !== 3) {
            //     $type = match ($existingUser->type) {
            //         2 => 'Google',
            //         4 => 'Apple',
            //         5 => 'Email and Password',
            //         default => 'another method',
            //     };
            //     return redirect('/login')->withErrors([
            //          'AppleError' => 'This mobile number is already registered using ' . $type . '.<br>Please login using ' . $type . '. Mobile No. ' . $existingUser->mobile_no . ' is associated with Email (' . $existingUser->email . ').'
            //     ]);
            // }

            $user = $existingUser;
        } else {
            $user = User::create([
                'name' => $fullname ?? 'Guest',
                'mobile_no' => $mobile_no,
                'subscription' => 'No',
                'type' => '3',
            ]);

            UserRole::firstOrCreate(
                ['user_id' => $user->id],
                ['role_id' => 1]
            );
        }

        Auth::login($user);
        session(['user_id' => $user->id]);

        if ($user->subscription !== 'Yes') {
            // session(['auto_open_payment' => true]);
            // return redirect('/home');
            return redirect(url('/?open_modal=subscription'));
        }

        //User location detect
        // userLocationDetect($request, $user);

        registerDeviceSession($request);

        $transaction = Transction::where('user_id', $user->id)
            ->where('status', 1)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($transaction) {
            $package = $transaction->package;

            $deviceCount = DB::table('device_sessions')
                ->where('user_id', $user->id)
                ->count();

            if ($deviceCount > $package->no_of_device) {
                removeDeviceSession($request);
                Auth::logout();
                return redirect('/reach-device-limit')->withErrors(['You have reached your device limit.']);
            }
        } else {
            return redirect('/')->withErrors(['You do not have an active subscription.']);
        }

        return redirect($intendedUrl);
    }

    public function handleCallbackSubscription(Request $request)
    {
        if (!$request->has('access_token')) {
            return response('<script>window.close();</script>');
        }

        // ── Call Phone Email API ──────────────────────────────────────
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => 'https://eapi.phone.email/getuser',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS     => http_build_query([
                'client_id'    => '18549902682527194455',
                'access_token' => $request->access_token,
            ]),
        ]);
        $response = curl_exec($curl);
        if (curl_errno($curl)) {
            return response('<script>window.close();</script>');
        }
        curl_close($curl);

        $response_data = json_decode($response, true);
        if (!isset($response_data['status']) || $response_data['status'] !== 200) {
            return response('<script>window.close();</script>');
        }

        $mobile_no = $response_data['country_code'] . $response_data['phone_no'];
        $fullname  = $response_data['first_name'] . ' ' . $response_data['last_name'];

        // ── Find or create user ───────────────────────────────────────
        $user = User::where('mobile_no', $mobile_no)
                    ->orWhere('mobile_no', $response_data['phone_no'])
                    ->first();

        if (!$user) {
            $user = User::create([
                'name'         => $fullname ?? 'Mobile Guest',
                'mobile_no'    => $mobile_no,
                'subscription' => 'No',
                'type'         => '3',
            ]);
            UserRole::firstOrCreate(
                ['user_id' => $user->id],
                ['role_id' => 1]
            );
        }

        Auth::login($user);
        session(['user_id' => $user->id]);

        $pendingPackageId = session()->pull('pending_package_id');

        // ── Close popup and notify parent ────────────────────────────
        if ($pendingPackageId) {
            // Tell parent to auto-submit PayU for this package
            return response("
                <script>
                    if (window.opener) {
                        window.opener.handleAfterOtpLogin('{$pendingPackageId}');
                    }
                    window.close();
                </script>
            ");
        }

        // No pending package → just close popup, parent will open modal
        return response("
            <script>
                if (window.opener) {
                    window.opener.handleAfterOtpLogin(null);
                }
                window.close();
            </script>
        ");
    }
    /*
    |--------------------------------------------------------------------------
    | Logout Logic
    |--------------------------------------------------------------------------
    */
    public function logout(Request $request)
    {
        removeDeviceSession($request);
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
    public function logoutDevice(Request $request, $id)
    {
        $userId = session('user_id');
        $newDeviceIp = $request->ip();
        $deviceName = $request->header('User-Agent');
        $newDeviceId = get_device_id_web($request);
        // 🔍 Prevent double delete attempts
        if (!DeviceSession::where('id', $id)->exists()) {
            Log::warning("Attempted to delete a device that does not exist: {$id}");
            return back()->withErrors(['Device not found or it was already logged out.']);
        }
        // 🔍 Fetch the device to be logged out by ID
        $device = DeviceSession::find($id);
        if ($device->user_id != $userId) {
            Log::error("Unauthorized device logout attempt for user ID: {$userId}");
            return back()->withErrors(['Device not found or unauthorized.']);
        }
        //Remove the selected device from `device_sessions`
        DeviceSession::where('id', $id)->delete();
        // Invalidate the session for that device
        $sessionId = DB::table('sessions')
            ->where('user_id', $userId)
            ->where('ip_address', $device->device_ip)
            ->first();
        if ($sessionId) {
            DB::table('sessions')->where('id', $sessionId->id)->delete();
        }
        // If the device being removed matches the current session's device ID:
        if ($device->device_id === $newDeviceId) {
            Log::info("Current device matches the one being logged out. Logging out...");
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('/login')->withErrors(['You were logged out from this device.']);
        }
        // If it's not the current device, create a new session for the current one
        // DeviceSession::insert([
        //     'user_id' => $userId,
        //     'device_id' => $newDeviceId,
        //     'device_name' => $deviceName,
        //     'device_ip' => $newDeviceIp,
        //     'created_at' => now(),
        //     'updated_at' => now()
        // ]);
        DeviceSession::create([
            'user_id' => $userId,
            'device_id' => $newDeviceId,
            'device_name' => $deviceName,
            'device_ip' => $newDeviceIp,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        //Re-login the user and refresh the session
        Auth::loginUsingId($userId);
        $request->session()->regenerate();
        session(['device_id' => $newDeviceId]);
        session()->save();
        // Redirect back to home
        return redirect()->route('home')->with('success', 'Device swapped and logged in successfully.');
    }
    // public function logoutDevice(Request $request, $id)
// {
//     $userId = session('user_id');
//     $newDeviceId = $this->get_device_id_web($request);
//     $newDeviceIp = $request->ip();
//     $deviceName = $request->header('User-Agent');
//     $device = DeviceSession::find($id);
//     if ($device && $device->user_id == $userId) {
//         DeviceSession::where('id', $id)->delete();
//         $sessionId = DB::table('sessions')
//             ->where('user_id', $userId)
//             ->where('ip_address', $device->device_ip)
//             ->first();
    //         if ($sessionId) {
//             DB::table('sessions')->where('id', $sessionId->id)->delete();
//         }
//         if ($device->device_id == $newDeviceId) {
//             Auth::logout();
//             $request->session()->invalidate();
//             $request->session()->regenerateToken();
//             return redirect('/login')->withErrors(['You were logged out from this device.']);
//         }
//         DeviceSession::insert([
//             'user_id' => $userId,
//             'device_id' => $newDeviceId,
//             'device_name' => $deviceName,
//             'device_ip' => $newDeviceIp,
//             'created_at' => now(),
//             'updated_at' => now()
//         ]);
//         Auth::loginUsingId($userId);
//         $request->session()->regenerate();
//         session(['device_id' => $newDeviceId]);
//         // return redirect()->route('home')->with('success', 'Device swapped successfully.');
    //         return redirect('/');
//     }
    //     return back()->withErrors(['Device not found or unauthorized.']);
// }
}
//         // Extract response data
//         $json_data = $response->json();
//         // $country_code = $json_data['country_code'];
//         // $phone_no = $json_data['phone_no'];
//         // $ph_email_jwt = $json_data['ph_email_jwt'];
//     //   $sss=  Session::put('phone_number', $country_code . $phone_no);
//         // print_r($phone_no);
//         Session::put('is_mobile_login', true);
//         return redirect()->route('home');
//        // Set session variable indicating mobile login
//         // // Check if API call was successful
//         // // Extract user details
//         // $country_code = $json_data['country_code'];
//         // $phone_no = $json_data['phone_no'];
//         // $ph_email_jwt = $json_data['ph_email_jwt'];
//         // // Store verified user phone number in session
//         // Session::put('phone_number', $country_code . $phone_no);
//         // Redirect to the home page
//     }
