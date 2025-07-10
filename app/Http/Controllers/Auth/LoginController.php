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
    
    public function authenticated(Request $request, $user)
    {
        // Register current device session
     
        // Subscription check
        $transaction = Transction::where('user_id', $user->id)
            ->where('status', 1)
            ->orderBy('created_at', 'desc')
            ->first();
        if ($transaction) {
            registerDeviceSession($request);
            $package = $transaction->package;
            $deviceCount = DB::table('device_sessions')
                ->where('user_id', $user->id)
                ->count();
            if ($deviceCount > $package->no_of_device) {
                removeDeviceSession($request);
                session(['user_id' => $user->id]);
                Auth::logout();
                return redirect('/reach-device-limit')->withErrors(['You have reached your device limit.']);
            }
        } else {
            // Auth::logout();
            return redirect('/')->withErrors(['You do not have an active subscription.']);
        }
        // Proceed to intended URL or /home
        return redirect()->intended('/home');
    }
    /*
    |--------------------------------------------------------------------------
    | Handle Callback and Login Logic
    |--------------------------------------------------------------------------
    */
    public function handleCallback(Request $request)
    {
        $intendedUrl = session()->get('url.intended', '/home');
        if (!$request->has('access_token')) {
            return redirect()->route('login')->with('error', 'Access token not provided.');
        }
        // Call Phone Email API to get user details
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
        if (curl_errno($curl)) {
            return redirect()->route('login')->with('error', 'Error fetching user details.');
        }
        curl_close($curl);
        $response_data = json_decode($response, true);
        if (!isset($response_data['status']) || $response_data['status'] !== 200) {
            return redirect()->route('login')->with('error', 'Error fetching user details.');
        }
        $country_code = $response_data['country_code'];
        $phone_no = $response_data['phone_no'];
        $user = User::firstOrCreate(
            ['mobile_no' => $country_code . $phone_no],
            [
                'name' => 'Guest',
                'subscription' => 'No',
                'type' => '3',
            ]
        );
        UserRole::firstOrCreate(
            ['user_id' => $user->id],
            ['role_id' => 1]
        );
        Auth::login($user);
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
                session(['user_id' => $user->id]);
                Auth::logout();
                return redirect('/reach-device-limit')->withErrors(['You have reached your device limit.']);
            }
        } else {
            return redirect('/')->withErrors(['You do not have an active subscription.']);
        }
        return redirect($intendedUrl);
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
        Log::info("Starting logoutDevice for ID: {$id}");
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
        Log::info("Device with ID {$id} removed from device_sessions.");
        // Invalidate the session for that device
        $sessionId = DB::table('sessions')
            ->where('user_id', $userId)
            ->where('ip_address', $device->device_ip)
            ->first();
        if ($sessionId) {
            DB::table('sessions')->where('id', $sessionId->id)->delete();
            Log::info("Session invalidated for IP: {$device->device_ip}");
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
        Log::info("New device session created for Device ID: {$newDeviceId}");
        //Re-login the user and refresh the session
        Auth::loginUsingId($userId);
        Log::info("User re-logged in with User ID: {$userId}");
        $request->session()->regenerate();
        session(['device_id' => $newDeviceId]);
        session()->save();
        Log::info("Session regenerated and saved.");
        // Redirect back to home
        Log::info("Redirecting to home...");
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
