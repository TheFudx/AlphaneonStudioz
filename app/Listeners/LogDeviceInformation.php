<?php

namespace App\Listeners;

use App\Models\Device;
use App\Services\DeviceService;
use Illuminate\Auth\Events\Authenticated;
use Illuminate\Support\Facades\Request;

class LogDeviceInformation
{
    protected $deviceService;

    public function __construct(DeviceService $deviceService)
    {
        $this->deviceService = $deviceService;
    }

    /**
     * Handle the event.
     *
     * @param  Authenticated  $event
     * @return void
     */
    public function handle(Authenticated $event)
    {
        // $user = $event->user;
        // $deviceName = Request::header('User-Agent');
        // // $deviceIp = Request::ip();

        // if ($this->deviceService->canLogin($user->id, $deviceName)) {
        //     Device::create([
        //         'user_id' => $user->id,
        //         'device_id' => $deviceName, 
        //         // 'device_ip' => $deviceIp,
        //     ]);
        // } else {
        //     // Auth::logout();
        //     return redirect('/login')->withErrors(['You have reached your device limit.']);
        // }
    }
}
