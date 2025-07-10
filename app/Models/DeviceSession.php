<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Browser;

class DeviceSession extends Model
{
    use HasFactory;
     protected $fillable = [
        'user_id',
        'device_id',
        'device_name',
        'device_ip',
        'browser',
        'browser_version',
        'platform',
        'platform_version',
        'device_type',
        'created_at',
        'updated_at'
    ];

    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    protected static function booted()
    {
        static::creating(function ($deviceSession) {
            if ($deviceSession->device_name) {
                $request = request(); // ✅ grab current request instance

                $parsed = Browser::parse($deviceSession->device_name);

                // ✅ check request input for is_brave (from JS)
                $browser = $request->input('is_brave') == 1 ? 'Brave' : $parsed->browserFamily();

                $deviceSession->browser = $browser;
                $deviceSession->browser_version = $parsed->browserVersion();
                $deviceSession->platform = $parsed->platformFamily();
                $deviceSession->platform_version = $parsed->platformVersion();
                $deviceSession->device_type = $parsed->deviceType();
            }
        });
    }
}
