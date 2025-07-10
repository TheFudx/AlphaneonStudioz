<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Helpers\Helpers;

class AuthController extends Controller
{
    // public function showLoginForm()
    // {
    //     // Replace XXXXXXXXXX with your client ID
    //     $CLIENT_ID = '18549902682527194455';
        
    //     // Get current URL for redirection
    //     $REDIRECT_URL = url('/auth/callback');
    //     // Construct authentication URL
    //     $AUTH_URL = 'https://auth.phone.email/log-in?client_id='.$CLIENT_ID.'&redirect_url='.$REDIRECT_URL;
    //     return view('auth.loginSampl', compact('AUTH_URL'));
    // }
    // public function handleCallback(Request $request)
    // {
    //     if (!$request->has('access_token')) {
    //         return redirect()->route('login')->with('error', 'Access token not provided.');
    //     }
    //     // Call Phone Email API to get user details
    //     $response = Http::post('https://eapi.phone.email/getuser', [
    //         'access_token' => $request->access_token,
    //         'client_id' => '18549902682527194455', // Replace with your client ID
    //     ]);
    //     $json_data = $response->json();
    //     if ($json_data['status'] != 200) {
    //         return redirect()->route('login')->with('error', 'Error fetching user details.');
    //     }
    //     $country_code = $json_data['country_code'];
    //     $phone_no = $json_data['phone_no'];
    //     $ph_email_jwt = $json_data['ph_email_jwt'];
    //     // Store verified user phone number in session
    //     Session::put('phone_number', $country_code . $phone_no);
    //     // Redirect to home page or any desired page
    //     return redirect()->route('home');
    // }
    // public function logout(Request $request)
    // {
    //     // Clear session and cookie
    //     Session::forget('phone_number');
    //     setcookie('ph_email_jwt', '', time() - 3600);
    //     return redirect()->route('login');
    // }
    
    // ===============================
    // Login Logic
    // ===============================
    public function login(Request $request) {
       
        if (auth()->attempt($request->only('email', 'password'))) {
            registerDeviceSession($request);
            return redirect('/dashboard');
        }
        return back()->withErrors(['error' => 'Invalid Credentials']);
    }
    // ===============================
    // Logout Logic
    // ===============================
    public function logout(Request $request) {
        removeDeviceSession($request);
        auth()->logout();
        return redirect('/login');
    }
    
    public function showCustomLoginForm()
{
    return view('auth.moblogin');
}
public function handleCustomCallback(Request $request)
{
    // Validate the mobile number
    $request->validate([
        'mobile_number' => 'required|numeric',
    ]);
    // Replace XXXXXXXXXX with your client ID
    $CLIENT_ID = '18549902682527194455';
    // Generate the authentication URL with the mobile number
    $AUTH_URL = 'https://auth.phone.email/log-in?client_id='.$CLIENT_ID.'&mobile_number='.$request->mobile_number;
    // Call Phone Email API to send OTP
    $response = Http::post('https://eapi.phone.email/getuser', [
        'client_id' => $CLIENT_ID,
        'mobile_number' => $request->mobile_number,
    ]);
    
    if ($response->status() != 200) {
        return redirect()->route('custom.login.submit')->with('error', 'Error sending OTP: ' . $response->body());
    }
    // Redirect to the OTP verification page
    return redirect()->route('custom.login.verify', ['mobile_number' => $request->mobile_number]);
}
public function showCustomLoginVerifyForm($mobile_number)
{
    return view('auth.custom-login-verify', compact('mobile_number'));
}
public function handleCustomLoginVerifySubmit(Request $request, $mobile_number)
{
    // Validate the OTP
    $request->validate([
        'otp' => 'required|numeric|digits:6',
    ]);
    // Replace XXXXXXXXXX with your client ID
    $CLIENT_ID = '18549902682527194455';
    // Call Phone Email API to verify OTP
    $response = Http::post('https://eapi.phone.email/verify-otp', [
        'client_id' => $CLIENT_ID,
        'mobile_number' => $mobile_number,
        'otp' => $request->otp,
    ]);
    if ($response->status() != 200) {
        return redirect()->route('custom.login.verify', ['mobile_number' => $mobile_number])->with('error', 'Invalid OTP.');
    }
    // Call Phone Email API to get user details
    $response = Http::post('https://eapi.phone.email/getuser', [
        'client_id' => $CLIENT_ID,
        'access_token' => $response->json()['access_token'],
    ]);
    if ($response->status() != 200) {
        return redirect()->route('custom.login.verify', ['mobile_number' => $mobile_number])->with('error', 'Error fetching user details.');
    }
    // Extract response data
    $json_data = $response->json();
    // $country_code = $json_data['country_code'];
    // $phone_no = $json_data['phone_no'];
    // $ph_email_jwt = $json_data['ph_email_jwt'];
    // $sss=  Session::put('phone_number', $country_code . $phone_no);
    Session::put('is_mobile_login', true);
    return redirect()->route('home');
}
}
