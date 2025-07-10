<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Device;
use App\Models\Package;
use App\Models\Package_Details;
use App\Models\Transction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckDeviceLimit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    // public function handle(Request $request, Closure $next): Response
    // {
    //     $user = app('logged-in-user');
    //     $deviceId = $request->header('User-Agent'); // Using User-Agent as a simple device identifier
    
    //     // Check if the user has an active subscription
    //     $transaction = Transction::where('user_id', $user->id)
    //                               ->where('status', 1)
    //                               ->latest()
    //                               ->first();
    
    //     if ($transaction) {
    //         $package = Package::find($transaction->package_id);
    //         $deviceLimit = $package->no_of_device;
    
    //         // Check if the user has already logged in from this device
    //         $device = Device::where('user_id', $user->id)
    //                         ->where('device_id', $deviceId)
    //                         ->first();
    
    //         if (!$device) {
    //             // User is logging in from a new device
    //             $deviceCount = Device::where('user_id', $user->id)->count();
    
    //             if ($deviceCount >= $deviceLimit) {
    //                 return response()->json(['message' => 'You have reached your device limit.'], 403);
    //             }
    
    //             // Add the new device to the database
    //             Device::create([
    //                 'user_id' => $user->id,
    //                 'device_id' => $deviceId,
    //             ]);
    //         } elseif ($user->logged_out) {
    //             // User logged out from this device, remove the device entry
    //           Device::where('user_id', $user->id)
    //               ->where('device_id', $deviceId)
    //               ->delete();
            
    //             // Add the new device to the database
    //             Device::create([
    //                 'user_id' => $user->id,
    //                 'device_id' => $deviceId,
    //             ]);
    //         } else {
    //             // User previously logged in from this device and has not logged out
    //             // Do nothing, allow login
    //         }
    //     }
    
    //     return $next($request);
    // }
    
    //   public function handle($request, Closure $next) {
    //     $user = app('logged-in-user');
        
    //     if ($user) {
    //         $package = DB::table('packages')->where('razorpay_plan_id', $user->razorpay_subscription_id)->first();
            
    //         if ($package) {
    //             $deviceLimit = $package->no_of_device;
    //             $activeDevices = DB::table('device_sessions')
    //                 ->where('user_id', $user->id)
    //                 ->count();
                
    //             if ($activeDevices >= $deviceLimit) {
    //                 return response()->json(['error' => 'Device limit reached. Please logout from another device.'], 403);
    //             }
    //         }
    //     }
        
    //     return $next($request);
    // }
    
    public function handle($request, Closure $next) {
        $user = app('logged-in-user');
        if ($user) {
            // Fetch the latest active transaction to get the package ID
            $transaction = DB::table('transaction')
                ->where('user_id', $user->id)
                ->where('status', 1)
                ->orderBy('created_at', 'desc')
                ->first();
            if ($transaction) {
                // Get the package details from the package table
                $package = DB::table('package')->where('id', $transaction->package_id)->first();
                
                if ($package) {
                    $deviceLimit = $package->no_of_device;
                    
                    // Count the number of active devices
                    $activeDevices = DB::table('device_sessions')
                        ->where('user_id', $user->id)
                        ->count();
                    
                        // For debugging or logging, you can inspect the current browser's User-Agent
                    $currentUserAgent = $request->header('User-Agent');
                    Log::info("User {$user->id} current User-Agent: {$currentUserAgent}");
                    Log::info("User {$user->id} device limit: {$deviceLimit}");

                    // If the active device count exceeds the limit, block access
                    if ($activeDevices > $deviceLimit) {
                        return response()->json(['error' => 'Device limit reached. Please logout from another device.'], 403);
                    }
                }
            }
        }
        return $next($request);
    }
}
