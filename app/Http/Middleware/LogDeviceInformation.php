<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Device;
use App\Services\DeviceService;

class LogDeviceInformation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
      protected $deviceService;

    public function __construct(DeviceService $deviceService)
    {
        $this->deviceService = $deviceService;
    }
    
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = app('logged-in-user');
            $deviceName = $request->header('User-Agent');
            // $deviceIp = $request->ip();
             Device::create([
                    'user_id' => $user->id,
                    'device_id' => $deviceName,
                    // 'device_ip' => $deviceIp,
                ]);

            if ($this->deviceService->canLogin($user->id, $deviceName)) {
                Device::create([
                    'user_id' => $user->id,
                    'device_id' => $deviceName,
                    // 'device_ip' => $deviceIp,
                ]);
            } else {
                // Optionally, handle the situation when the device limit is reached
                // For example, log out the user or show an error message
                Auth::logout();
                return redirect('/login')->withErrors(['You have reached your device limit.']);
            }
        }

      
        $response = $next($request);
        return $response;
    }
}
