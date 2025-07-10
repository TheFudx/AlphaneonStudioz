<?php
use App\Models\DeviceSession;

if (!function_exists('MillisecondsToTime')) {
    function MillisecondsToTime($milliseconds) {
        $seconds = intdiv($milliseconds, 1000);
        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds, 60) % 60;
        $remainingSeconds = $seconds % 60;

        $format = '';

        if ($hours > 0) {
            $format .= $hours . ' hr ';
            $format .= $minutes > 0 ? $minutes . ' min' : '';
        } elseif ($minutes > 0) {
            $format .= $minutes . ' min';
        } else {
            $format .= $remainingSeconds . ' sec';
        }

        return trim($format);
        
    }
}
function MillisecondsToTimeForMusic($milliseconds){
    $Seconds = (int) $milliseconds / 1000;
    $Seconds = round($Seconds);

    $Format = sprintf('%02d:%02d', ((int) $Seconds / 60 % 60), ((int) $Seconds) % 60);
    return $Format;
}
function getOSAndBrowser($userAgent) {
    $os = 'Unknown OS';
    $browser = 'Unknown Browser';

    // OS detection
    if (preg_match('/linux/i', $userAgent)) {
        $os = 'Linux';
    } elseif (preg_match('/macintosh|mac os x/i', $userAgent)) {
        $os = 'Mac OS';
    } elseif (preg_match('/windows|win32/i', $userAgent)) {
        $os = 'Windows';
    }

    // Browser detection
    if (preg_match('/MSIE/i', $userAgent) && !preg_match('/Opera/i', $userAgent)) {
        $browser = 'Internet Explorer';
    } elseif (preg_match('/Firefox/i', $userAgent)) {
        $browser = 'Firefox';
    } elseif (preg_match('/Chrome/i', $userAgent)) {
        $browser = 'Chrome';
    } elseif (preg_match('/Safari/i', $userAgent)) {
        $browser = 'Safari';
    } elseif (preg_match('/Opera/i', $userAgent)) {
        $browser = 'Opera';
    } elseif (preg_match('/Netscape/i', $userAgent)) {
        $browser = 'Netscape';
    }

    return "$os | $browser";
}


function getActualDeviceNameAndBrowserFromUserAgent($userAgent) {
    if (empty($userAgent)) {
        return 'Unknown Device | Unknown Browser';
    }

    $parser = new WhichBrowser\Parser($userAgent);

    $deviceName = 'Unknown Device';
    $browserName = 'Unknown Browser';

    // Get Browser Information
    if (isset($parser->browser) && !empty($parser->browser->name)) {
        $browserName = $parser->browser->name;
        if (isset($parser->browser->version) && !empty($parser->browser->version->value)) {
            $browserName .= ' ' . $parser->browser->version->value;
        } elseif (isset($parser->browser->version) && !empty($parser->browser->version->alias)) {
            $browserName .= ' ' . $parser->browser->version->alias;
        }
    }

    // Get Device Information
    if (isset($parser->device) && !empty($parser->device->manufacturer) && !empty($parser->device->model)) {
        $deviceName = $parser->device->manufacturer . ' ' . $parser->device->model;
    } elseif (isset($parser->device) && !empty($parser->device->model)) {
        $deviceName = $parser->device->model;
    } elseif (isset($parser->os) && !empty($parser->os->name)) {
        // Fallback to Operating System if no specific device model
        $deviceName = $parser->os->name;
        if (isset($parser->os->version) && !empty($parser->os->version->alias)) {
            $deviceName .= ' ' . $parser->os->version->alias;
        } elseif (isset($parser->os->version) && !empty($parser->os->version->value)) {
             $deviceName .= ' ' . $parser->os->version->value;
        }
    }

    // Add device type if available and useful (e.g., mobile, tablet)
    if (isset($parser->device->type) && $parser->device->type !== 'desktop' && $deviceName !== 'Unknown Device') {
        // Avoid redundant type if OS already implies it (e.g., "iPhone (Mobile)")
        if (!str_contains(strtolower($deviceName), strtolower($parser->device->type))) {
             $deviceName .= ' (' . ucfirst($parser->device->type) . ')';
        }
    }


    return "$deviceName | $browserName";
}
function get_device_id_web($request)
{
    return hash('sha256', $request->ip() . $request->userAgent());
    
}
function registerDeviceSession($request)
{
    $deviceId = get_device_id_web($request);
    $userAgent = $request->userAgent();
    // Parse the User-Agent using the hisorange/browser-detect package
    $parsed = Browser::parse($userAgent);
    $browser = $request->input('is_brave') == 1 ? 'Brave' : Browser::browserFamily();     // e.g., Edge
    $browser_version = $parsed->browserVersion();     // e.g., Edge
    $platform = $parsed->platformFamily();   // e.g., Windows
    $platform_version = $parsed->platformVersion();   // e.g., Windows
    $device_type = $parsed->deviceType();   // e.g., Windows

    // Check if this device/browser/platform combination already exists
    $exists = DeviceSession::where('user_id', auth()->id())
        ->where('device_id', $deviceId)
        ->where('browser', $browser)->where('browser_version',$browser_version)
        ->where('platform', $platform)->where('platform_version',$platform_version)->where('device_type',$device_type)
        ->exists();

    if (!$exists) {
        DeviceSession::create([
            'user_id' => auth()->id(),
            'device_id' => $deviceId,
            'device_name' => $request->userAgent(),
            'device_ip' => $request->ip()
        ]);
    }
}
function removeDeviceSession($request)
{
   $deviceId = get_device_id_web($request);
    $parsed = Browser::parse($request->userAgent());

    DeviceSession::where('device_id', $deviceId)
        ->where('browser', $parsed->browserFamily())
        ->where('platform', $parsed->platformFamily())
        ->delete();
}

// function get_device_id_web($request)
// {
//     // return hash('sha256', $request->ip() . $request->userAgent());
//     $ip = $request->ip();
//     $userAgent = $request->userAgent();
//     $browser = Browser::browserName(); // e.g., Chrome, Firefox
//     $browserVersion = Browser::browserVersion(); // e.g., 115.0.1

//     return hash('sha256', $ip . $userAgent . $browser . $browserVersion);
// }
?>