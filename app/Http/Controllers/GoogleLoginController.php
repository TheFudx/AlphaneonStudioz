<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Session; 
use Illuminate\Support\Facades\Route;
use App\Models\Device;
use App\Models\UserRole;
use App\Models\Transction;
use Illuminate\Support\Str;
use App\Models\DeviceSession;

class GoogleLoginController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }
    
    public function handleGoogleCallback(Request $request)
    {
        $deviceName = $request->header('User-Agent');
        $deviceIp = $request->ip();
        $deviceId = hash('sha256', $deviceIp . $deviceName);
        $intendedUrl = session()->get('url.intended', RouteServiceProvider::HOME);

        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            return redirect('/login')->withErrors(['Unable to login using Google. Please try again.']);
        }

        // Check if the email already exists
        $existingUser = User::where('email', $googleUser->email)->first();

        if ($existingUser) {
            // Block if already registered via Apple or another method
            if ($existingUser->type !== 2) {
                $type = match ($existingUser->type) {
                    4 => 'Apple',
                    3 => 'Mobile OTP',
                    5 => 'Email and Password',
                    default => 'another method',
                };

                return redirect('/login')->withErrors([
                    'AppleError' => 'You are already registered with this email (' . $googleUser->email . ').<br>Please login using ' . $type
                ]);
            }

            $user = $existingUser;
        } else {
            // Create new user
            $user = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'type' => 2,
                'subscription' => 'No',
            ]);

            UserRole::firstOrCreate(
                ['user_id' => $user->id],
                ['role_id' => 1]
            );
        }

        Auth::login($user);
        session(['user_id' => $user->id]);
        // user location detect
        // userLocationDetect($request, $user);
        // Subscription check
        $transaction = Transction::where('user_id', $user->id)
            ->where('status', 1)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$transaction) {
            return redirect('/')->withErrors(['You do not have an active subscription.']);
        }

        $package = $transaction->package;

        // Device session check
        $deviceCount = DeviceSession::where('user_id', $user->id)
            ->count();

        $existingSession = DeviceSession::where('user_id', $user->id)
            ->where('device_id', $deviceId)
            ->first();

        if (!$existingSession) {
            if ($deviceCount >= $package->no_of_device) {
                Auth::logout();
                return redirect('/reach-device-limit')->withErrors(['You have reached your device limit.']);
            }

            DeviceSession::create([
                'user_id' => $user->id,
                'device_id' => $deviceId,
                'device_name' => $deviceName,
                'device_ip' => $deviceIp,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return redirect()->route('home');
    }
}