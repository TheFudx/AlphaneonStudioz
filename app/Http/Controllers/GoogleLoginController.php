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
        // Find or create the user
        $user = User::firstOrCreate(
            ['email' => $googleUser->email],
            [
                'name' => $googleUser->name,
                'type' => 2,
                'subscription' => 'No',
            ]
        );
        // Ensure user role exists
        UserRole::firstOrCreate(
            ['user_id' => $user->id],
            ['role_id' => 1]
        );
        // Log in the user
        Auth::login($user);
        session(['user_id' => $user->id]);
        // Check active subscription
        $transaction = Transction::where('user_id', $user->id)
            ->where('status', 1)
            ->orderBy('created_at', 'desc')
            ->first();
        if (!$transaction) {
            // Auth::logout();
            return redirect('/')->withErrors(['You do not have an active subscription.']);
        }
        $package = $transaction->package;
        $deviceCount = \DB::table('device_sessions')
            ->where('user_id', $user->id)
            ->count();
        // Check if current device already exists
        $existingSession = \DB::table('device_sessions')
            ->where('user_id', $user->id)
            ->where('device_id', $deviceId)
            ->first();
        if (!$existingSession) {
            if ($deviceCount >= $package->no_of_device) {
                Auth::logout();
                return redirect('/reach-device-limit')->withErrors(['You have reached your device limit.']);
            }
            // Register new device session
            // \DB::table('device_sessions')->insert([
            //     'user_id' => $user->id,
            //     'device_id' => $deviceId,
            //     'device_name' => $deviceName,
            //     'device_ip' => $deviceIp,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ]);
            DeviceSession::create([
            'user_id' => $user->id,
            'device_id' => $deviceId,
            'device_name' => $deviceName,
            'device_ip' => $deviceIp,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        }
        // Redirect to intended URL
        // return redirect($intendedUrl);
        return redirect()->route('home');
    }
}