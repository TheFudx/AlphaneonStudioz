<?php
use App\Models\DeviceSession;
use App\Models\Transction;
use Illuminate\Http\Request;
use App\Models\UserLocations;
use Illuminate\Support\Facades\Http;
function successResponse($data, $message = 'Success',$status=200)
{
        return response()->json([
            'status'=>true,
            'status_code' => $status,
            'message' => $message,
            'data' => $data
        ],$status);
} 
function errorResponse($message,$status=422,$data=null)
{
        return response()->json([
            'status'=> false,
            'status_code' => $status,
            'message' => $message,
            'data'=>$data
        ],$status);
}
if (!function_exists('MillisecondsToTime')) {
    function MillisecondsToTime($milliseconds) {
        $seconds = intdiv($milliseconds, 1000);
        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds, 60) % 60;
        $remainingSeconds = $seconds % 60;

        $format = [];

        if ($hours > 0) {
            $format[] = $hours . ' hr';
        }

        if ($minutes > 0) {
            $format[] = $minutes . ' min';
        }

        if ($remainingSeconds > 0 || empty($format)) {
            $format[] = $remainingSeconds . ' sec';
        }

        return implode(' ', $format);
    }
}
function MillisecondsToTimeForMusic($milliseconds){
    $seconds = floor($milliseconds / 1000); // Convert to full seconds
    $minutes = floor($seconds / 60);
    $remainingSeconds = $seconds % 60;
    return sprintf('%02d:%02d', $minutes, $remainingSeconds);
}
// function getOSAndBrowser($userAgent) {
//     $os = 'Unknown OS';
//     $browser = 'Unknown Browser';

//     // OS detection
//     if (preg_match('/linux/i', $userAgent)) {
//         $os = 'Linux';
//     } elseif (preg_match('/macintosh|mac os x/i', $userAgent)) {
//         $os = 'Mac OS';
//     } elseif (preg_match('/windows|win32/i', $userAgent)) {
//         $os = 'Windows';
//     }

//     // Browser detection
//     if (preg_match('/MSIE/i', $userAgent) && !preg_match('/Opera/i', $userAgent)) {
//         $browser = 'Internet Explorer';
//     } elseif (preg_match('/Firefox/i', $userAgent)) {
//         $browser = 'Firefox';
//     } elseif (preg_match('/Chrome/i', $userAgent)) {
//         $browser = 'Chrome';
//     } elseif (preg_match('/Safari/i', $userAgent)) {
//         $browser = 'Safari';
//     } elseif (preg_match('/Opera/i', $userAgent)) {
//         $browser = 'Opera';
//     } elseif (preg_match('/Netscape/i', $userAgent)) {
//         $browser = 'Netscape';
//     }

//     return "$os | $browser";
// }
// function getActualDeviceNameAndBrowserFromUserAgent($userAgent) {
//     if (empty($userAgent)) {
//         return 'Unknown Device | Unknown Browser';
//     }

//     $parser = new WhichBrowser\Parser($userAgent);

//     $deviceName = 'Unknown Device';
//     $browserName = 'Unknown Browser';

//     // Get Browser Information
//     if (isset($parser->browser) && !empty($parser->browser->name)) {
//         $browserName = $parser->browser->name;
//         if (isset($parser->browser->version) && !empty($parser->browser->version->value)) {
//             $browserName .= ' ' . $parser->browser->version->value;
//         } elseif (isset($parser->browser->version) && !empty($parser->browser->version->alias)) {
//             $browserName .= ' ' . $parser->browser->version->alias;
//         }
//     }

//     // Get Device Information
//     if (isset($parser->device) && !empty($parser->device->manufacturer) && !empty($parser->device->model)) {
//         $deviceName = $parser->device->manufacturer . ' ' . $parser->device->model;
//     } elseif (isset($parser->device) && !empty($parser->device->model)) {
//         $deviceName = $parser->device->model;
//     } elseif (isset($parser->os) && !empty($parser->os->name)) {
//         // Fallback to Operating System if no specific device model
//         $deviceName = $parser->os->name;
//         if (isset($parser->os->version) && !empty($parser->os->version->alias)) {
//             $deviceName .= ' ' . $parser->os->version->alias;
//         } elseif (isset($parser->os->version) && !empty($parser->os->version->value)) {
//              $deviceName .= ' ' . $parser->os->version->value;
//         }
//     }

//     // Add device type if available and useful (e.g., mobile, tablet)
//     if (isset($parser->device->type) && $parser->device->type !== 'desktop' && $deviceName !== 'Unknown Device') {
//         // Avoid redundant type if OS already implies it (e.g., "iPhone (Mobile)")
//         if (!str_contains(strtolower($deviceName), strtolower($parser->device->type))) {
//              $deviceName .= ' (' . ucfirst($parser->device->type) . ')';
//         }
//     }


//     return "$deviceName | $browserName";
// }
function get_device_id_web($request)
{
    $agent = $request->userAgent() ?? 'unknown';
    return hash('sha256', $request->ip() . $agent);
}
function generateDeviceIdForAPI(Request $request): string
{
    $userAgent = $request->userAgent();
    $acceptLanguage = $request->header('Accept-Language');
    $secChUa = $request->header('sec-ch-ua');
    $secChUaPlatform = $request->header('sec-ch-ua-platform');
    $secChUaMobile = $request->header('sec-ch-ua-mobile');
    $deviceIp = $request->ip();

    $raw = $userAgent . $acceptLanguage . $secChUa . $secChUaPlatform . $secChUaMobile . $deviceIp;

    return hash('sha256', $raw);
}
function getDeviceTypeFromUserAgent(string $userAgent): string
{
    $userAgent = strtolower($userAgent);

    if (preg_match('/tablet|ipad|playbook|silk|(android(?!.*mobile))/i', $userAgent)) {
        return 'Tablet';
    }

    if (preg_match('/mobile|iphone|ipod|android|blackberry|opera mini|iemobile|wpdesktop/i', $userAgent)) {
        return 'Mobile';
    }

    return 'Desktop';
}

function safeBrowserParse(Request $request)
{
    $agent = $request->header('User-Agent');

    if (empty($agent) || !is_string($agent)) {
        // Always guarantee a string
        $agent = 'Mozilla/5.0 (Compatible; Unknown Device)';
    }

    return \Browser::parse($agent);
}
function registerDeviceSession($request)
{
    $deviceId  = get_device_id_web($request);
    $userAgent = $request->userAgent() ?? 'unknown';

    $parsed = safeBrowserParse($request);

    $browser = $request->input('is_brave') == 1
        ? 'Brave'
        : $parsed->browserFamily();

    $browser_version  = $parsed->browserVersion();
    $platform         = $parsed->platformFamily();
    $platform_version = $parsed->platformVersion();
    $device_type      = $parsed->deviceType();

    $exists = DeviceSession::where('user_id', auth()->id())
        ->where('device_id', $deviceId)
        ->where('browser', $browser)
        ->where('browser_version', $browser_version)
        ->where('platform', $platform)
        ->where('platform_version', $platform_version)
        ->where('device_type', $device_type)
        ->exists();

    if (!$exists) {
        DeviceSession::create([
            'user_id'     => auth()->id(),
            'device_id'   => $deviceId,
            'device_name' => $userAgent,
            'device_ip'   => $request->ip(),
        ]);
    }
}
function removeDeviceSession(Request $request)
{
    $deviceId = get_device_id_web($request);
    $parsed   = safeBrowserParse($request);

    DeviceSession::where('device_id', $deviceId)
        ->where('browser', $parsed->browserFamily())
        ->where('platform', $parsed->platformFamily())
        ->delete();
}
function get_setting(string $key, $default = null)
{
    $row = \DB::table('general_setting')->where('key', $key)->first();
    return $row ? $row->value : $default;
}

function save_setting(string $key, $value): void
{
    \DB::table('general_setting')->updateOrInsert(['key' => $key], ['value' => $value]);
}
function userLocationDetect($request, $user)
{
    $device_ip = $request->ip();
    $userAgent = $request->header('User-Agent') ?: 'Unknown Device';

    $parsed   = safeBrowserParse($request);
    $platform = $parsed->platformFamily();

    $geo = Http::withOptions(['verify' => false])
        ->get("https://ipwho.is/{$device_ip}")
        ->json();

    $check = UserLocations::where('user_id', $user->id)
        ->where('device_ip', $device_ip)
        ->where('plateform', $platform)
        ->first();

    if ($check) {
        if ($user->subscription === 'Yes') {

            $transaction = Transction::where('user_id', $user->id)
                ->where('status', 1)
                ->latest()
                ->first();

            if ($transaction) {

                $count   = UserLocations::where('user_id', $user->id)->count();
                $package = $transaction->package;

                if ($count > $package->no_of_device) {

                    $oldDevice = UserLocations::where('user_id', $user->id)
                        ->oldest()
                        ->first();

                    if ($oldDevice) {
                        $oldDevice->update([
                            'country'      => $geo['country'] ?? null,
                            'state'        => $geo['region'] ?? null,
                            'city'         => $geo['city'] ?? null,
                            'country_code' => $geo['country_code'] ?? null,
                            'region_code'  => $geo['region_code'] ?? null,
                            'latitude'     => $geo['latitude'] ?? null,
                            'longitude'    => $geo['longitude'] ?? null,
                            'device_ip'    => $device_ip,
                            'device_id'    => get_device_id_web($request),
                            'device_name'  => $userAgent,
                            'device_type'  => $parsed->deviceType(),
                            'plateform'    => $platform,
                        ]);
                    }

                } else {
                    $check->update([
                        'country'      => $geo['country'] ?? null,
                        'state'        => $geo['region'] ?? null,
                        'city'         => $geo['city'] ?? null,
                        'country_code' => $geo['country_code'] ?? null,
                        'region_code'  => $geo['region_code'] ?? null,
                        'latitude'     => $geo['latitude'] ?? null,
                        'longitude'    => $geo['longitude'] ?? null,
                        'device_ip'    => $device_ip,
                        'device_id'    => get_device_id_web($request),
                        'device_name'  => $userAgent,
                        'device_type'  => $parsed->deviceType(),
                        'plateform'    => $platform,
                    ]);
                }
            }
        }
    } else {
        UserLocations::create([
            'user_id'      => $user->id,
            'country'      => $geo['country'] ?? null,
            'state'        => $geo['region'] ?? null,
            'city'         => $geo['city'] ?? null,
            'country_code' => $geo['country_code'] ?? null,
            'region_code'  => $geo['region_code'] ?? null,
            'latitude'     => $geo['latitude'] ?? null,
            'longitude'    => $geo['longitude'] ?? null,
            'plateform'    => $platform,
            'device_ip'    => $device_ip,
            'device_id'    => get_device_id_web($request),
            'device_name'  => $userAgent,
            'device_type'  => $parsed->deviceType(),
        ]);
    }
}

?>