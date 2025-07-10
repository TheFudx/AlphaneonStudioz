<?php 

namespace App\Services;

use App\Models\Device;
use App\Models\Transction;
 
class DeviceService
{
    public function canLogin($userId, $deviceName)
    {
        // Get the latest active transaction for the user
        $transaction = Transction::where('user_id', $userId)
                                  ->where('status', 1)
                                  ->orderBy('created_at', 'desc')
                                  ->first();

        if (!$transaction) {
            return false; // No active subscription
        } 

        $package = $transaction->package;

        // Count the number of devices the user has logged in on
        $deviceCount = Device::where('user_id', $userId)->count();

        return $deviceCount <= $package->no_of_device;
    }
}
