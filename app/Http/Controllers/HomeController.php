<?php
namespace App\Http\Controllers;
use App\Models\Episodes;
use App\Models\Transction;
use App\Models\User;
use App\Models\UserLocations;
use Illuminate\Http\Request;
use App\Models\App_Section;
use App\Models\Video;
use App\Models\Banner;
use App\Models\Series;
use App\Models\StaticBanners;
use App\Models\Category;
use App\Models\Kluphs;
use App\Models\Notification;
use App\Models\Watchlists;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use VideoThumbnail;
use Log;
use App\Helpers\FirebaseV1Notification;
use DB;
use App\Models\UserWatchReport;
use App\Helpers\VideoHelper;

class HomeController extends Controller
{
    public function saveUserLocation(Request $request)
    {
        $data = $request->all();
        $user = User::find($data['user_id']);
        $data['city'] = isset($data['city']) ? $data['city'] : null;
        $data['state'] = isset($data['state']) ? $data['state'] : null;
        $check = UserLocations::where('user_id', $data['user_id'])
            ->where('city', $data['city'])
            ->where('state', $data['state'])
            ->first();
        if ($check) {
            if ($user->subscription == 'Yes') {

                $transaction = Transction::where('user_id', $user->id)
                    ->where('status', 1)
                    ->orderBy('created_at', 'desc')
                    ->first();

                if ($transaction) {

                    $count = UserLocations::where('user_id', $user->id)->count();
                    $package = $transaction->package;

                    // If total devices > allowed limit
                    if ($count > $package->no_of_device) {

                        // Update the OLDEST DEVICE record
                        $oldDevice = UserLocations::where('user_id', $user->id)
                            ->orderBy('created_at', 'asc')   // oldest entry
                            ->first();

                        if ($oldDevice) {

                            $oldDevice->update([
                                'country' => $data['country'] ?? null,
                                'state' => $data['state'] ?? null,
                                'city' => $data['city'] ?? null,
                                'country_code' => $data['country_code'] ?? null,
                                // 'region_code'   => $data['region_code'] ?? null,
                                'latitude' => $data['latitude'] ?? null,
                                'longitude' => $data['longitude'] ?? null,

                            ]);
                        }

                    } else {
                        // Inside limit → only update the existing device record
                        $check->update([
                            'country' => $data['country'] ?? null,
                            'state' => $data['state'] ?? null,
                            'city' => $data['city'] ?? null,
                            'country_code' => $data['country_code'] ?? null,
                            // 'region_code'   => $data['region_code'] ?? null,
                            'latitude' => $data['latitude'] ?? null,
                            'longitude' => $data['longitude'] ?? null,
                        ]);
                    }
                }
            }

        } else {
            // New device entry
            UserLocations::create([
                'user_id' => $user->id,
                'country' => $data['country'] ?? null,
                'state' => $data['state'] ?? null,
                'city' => $data['city'] ?? null,
                'country_code' => $data['country_code'] ?? null,
                // 'region_code'   => $data['region_code'] ?? null,
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
            ]);
        }
        return response()->json(['success' => true, 'data' => $data]);
    }
    // public function saveWatchTime(Request $request)
    // {
    //     $request->validate([
    //         'user_id' => 'required|integer',
    //         // 'video_id' => 'required|integer',
    //         // 'watch_time' => 'required|numeric',
    //         // 'type_id' => 'required|integer',
    //         // 'category_id' => 'required|integer'
    //     ]);
    //     if ($request->type == 'video') {

    //         $video = Video::where('category_id', $request->category_id)
    //             ->where('id', $request->video_id)
    //             ->first();

    //         $videoDurationSec = $video->video_duration / 1000;

    //         // Get last watch record
    //         $last = UserWatchReport::where('user_id', $request->user_id)
    //             ->where('video_id', $request->video_id)
    //             ->where('category_id', $request->category_id)
    //             ->where('type_id', $request->type_id)
    //             ->orderBy('id', 'desc')
    //             ->first();

    //         // CASE 1: No previous record → create new
    //         if (!$last) {
    //             UserWatchReport::create([
    //                 'user_id' => $request->user_id,
    //                 'type_id' => $request->type_id,
    //                 'category_id' => $request->category_id,
    //                 'video_id' => $request->video_id,
    //                 'watch_time' => $request->watch_time,
    //                 'date_time' => now(),
    //             ]);

    //             return response()->json(['success' => true]);
    //         }
    //         if ($last->watch_time >= ($videoDurationSec - 1) && $last->status != 1) {
    //             $last->status = 1;
    //             $last->save();
    //         }
    //         // CASE 2: Previous record is completed → create new record for new attempt
    //         if ($last->watch_time >= $videoDurationSec) { // tolerance

    //             UserWatchReport::create([
    //                 'user_id' => $request->user_id,
    //                 'type_id' => $request->type_id,
    //                 'category_id' => $request->category_id,
    //                 'video_id' => $request->video_id,
    //                 'watch_time' => $request->watch_time,
    //                 'date_time' => now(),
    //             ]);

    //             return response()->json(['success' => true, 'event' => 'new_session']);
    //         }

    //         // CASE 3: Not completed → update the same row
    //         $last->watch_time = $request->watch_time;
    //         $last->date_time = now();
    //         $last->save();
    //     } elseif ($request->type == 'episode') {

    //         $episode = Episodes::where('series_id', $request->series_id)
    //             ->where('id', $request->episode_id)
    //             ->first();



    //         $videoDurationSec = $episode->episode_duration / 1000;

    //         $last = UserWatchReport::where('user_id', $request->user_id)
    //             ->where('episode_id', $request->episode_id)
    //             ->where('category_id', $request->category_id)
    //             ->where('type_id', $request->type_id)
    //             ->where('series_id', $request->series_id)
    //             ->orderBy('id', 'desc')
    //             ->first();
    //         //   dd($episode,$last);
    //         if (!$last) {
    //             UserWatchReport::create([
    //                 'user_id' => $request->user_id,
    //                 'type_id' => $request->type_id,
    //                 'category_id' => $request->category_id,
    //                 'episode_id' => $request->episode_id,
    //                 'series_id' => $request->series_id,
    //                 'watch_time' => $request->watch_time,
    //                 'date_time' => now(),
    //             ]);

    //             return response()->json(['success' => true]);
    //         }
    //         if ($last->watch_time >= ($videoDurationSec - 1) && $last->status != 1) {
    //             $last->status = 1;
    //             $last->save();
    //         }
    //         // CASE 2: Previous record is completed → create new record for new attempt
    //         if ($last->watch_time >= $videoDurationSec) { // tolerance

    //             UserWatchReport::create([
    //                 'user_id' => $request->user_id,
    //                 'type_id' => $request->type_id,
    //                 'category_id' => $request->category_id,
    //                 'episode_id' => $request->episode_id,
    //                 'series_id' => $request->series_id,
    //                 'watch_time' => $request->watch_time,
    //                 'date_time' => now(),
    //             ]);

    //             return response()->json(['success' => true, 'event' => 'new_session']);
    //         }

    //         // CASE 3: Not completed → update the same row
    //         $last->watch_time = $request->watch_time;
    //         $last->date_time = now();
    //         $last->save();

    //     }

    //     return response()->json(['success' => true, 'event' => 'update']);
    // }
    public function saveWatchTime(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
        ]);
        $categoryId = is_array($request->category_id)
            ? implode(',', $request->category_id)
            : (string) $request->category_id;
        /** Identify Video or Episode */
        if ($request->type == 'video') {

            $video = Video::find($request->video_id);

            if (!$video) {
                return response()->json(['success' => false, 'message' => 'Video not found']);
            }

            // Do not track watch time for trailers (trailers should not be added to continue watching)
            if ($video->type_id == config('constant.TYPES.TRAILER', 8) || $request->type_id == config('constant.TYPES.TRAILER', 8) || !empty($video->trailer_for)) {
                return response()->json(['success' => true, 'event' => 'ignored_trailer']);
            }

            $durationSec = $video->video_duration / 1000;
            $tolerance = $durationSec - 3;

            $query = UserWatchReport::where('user_id', $request->user_id)
                ->where('type_id', $request->type_id)
              
                ->where('video_id', $request->video_id);

        } elseif ($request->type == 'episode') {

            $episode = Episodes::find($request->episode_id);

            if (!$episode) {
                return response()->json(['success' => false, 'message' => 'Episode not found']);
            }

            $durationSec = $episode->episode_duration / 1000;
            $tolerance = $durationSec - 3;

            $query = UserWatchReport::where('user_id', $request->user_id)
                ->where('type_id', $request->type_id)
                ->whereRaw("FIND_IN_SET(?, category_id)", [$request->category_id])
                ->where('series_id', $request->series_id)
                ->where('episode_id', $request->episode_id);
                    $categoryIds = explode(',', $request->category_id);
                                    $query->where(function ($q) use ($categoryIds) {
                        foreach ($categoryIds as $catId) {
                            $q->orWhereRaw("FIND_IN_SET(?, category_id)", [$catId]);
                        }
                    });
        }

        /** Last Record */
        $last = $query->orderBy('id', 'desc')->first();


        /** CASE 1: No record → insert first  */
        if (!$last) {

            $isCompleted = $request->watch_time >= $tolerance ? 1 : 0;

            UserWatchReport::create([
                'user_id' => $request->user_id,
                'type_id' => $request->type_id,
                'category_id' => $categoryId,
                'video_id' => $request->video_id ?? null,
                'series_id' => $request->series_id ?? null,
                'episode_id' => $request->episode_id ?? null,
                'watch_time' => $request->watch_time,
                'status' => $isCompleted,
                'date_time' => now(),
            ]);

            return response()->json([
                'success' => true,
                'event' => $isCompleted ? 'completed_first' : 'insert_first'
            ]);
        }


        /** CASE 2: Last session completed*/
        if ($last->status == 1) {

            // BLOCK: duplicate rows on refresh or end-time spam
            if (abs($request->watch_time - $last->watch_time) <= 4) {
                return response()->json([
                    'success' => true,
                    'event' => 'duplicate_prevented'
                ]);
            }

            // NEW SESSION: only if user actually restarted
            if ($request->watch_time < 5) {

                $isCompleted = $request->watch_time >= $tolerance ? 1 : 0;

                UserWatchReport::create([
                    'user_id' => $request->user_id,
                    'type_id' => $request->type_id,
                    'category_id' => $categoryId,
                    'video_id' => $request->video_id ?? null,
                    'series_id' => $request->series_id ?? null,
                    'episode_id' => $request->episode_id ?? null,
                    'watch_time' => $request->watch_time,
                    'status' => $isCompleted,
                    'date_time' => now(),
                ]);

                return response()->json([
                    'success' => true,
                    'event' => 'new_session'
                ]);
            }

            // Otherwise ignore
            return response()->json([
                'success' => true,
                'event' => 'completed_ignore'
            ]);
        }


        /**CASE 3: Update same session */
        $last->watch_time = $request->watch_time;

        if ($request->watch_time >= $tolerance) {
            $last->status = 1;
        }

        $last->date_time = now();
        $last->save();

        return response()->json([
            'success' => true,
            'event' => $last->status == 1 ? 'completed' : 'progress_update'
        ]);
    }

    public function saveToken(Request $request)
    {
        $request->validate([
            'device_token' => 'required|string',
            'user_id' =>'nullable',
            
        ]);

        DB::table('fcm_tokens')->updateOrInsert(
            [
                'device_token' => $request->device_token
            ],
            [
                'user_id' => $request->user_id,
                'device_type' => $request->device_type,
                'platform' => $request->platform,
                'browser' => $request->browser,
                'device_name' => $request->device_name,
                'app_version' => $request->app_version,
                'status' => 1,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        return response()->json(['message' => 'Token saved or updated successfully.']);
    }

    public function saveTokenAPI(Request $request)
    {
        $request->validate([
            'device_token' => 'required|string',
            'user_id' => 'nullable|exists:users,id',
            'device_type' => 'nullable|in:web,mobile,tablet,desktop',
            'platform' => 'nullable|string',
            'browser' => 'nullable|string',
            'device_name' => 'nullable|string',
            'app_version' => 'nullable|string',
            'user_agent' => 'nullable|string',
        ]);

        // Auto-detect device type if not provided
        $deviceType = $request->device_type;
        $platform = $request->platform;
        $browser = $request->browser;
        
        if (empty($deviceType) || empty($platform)) {
            $userAgent = $request->user_agent ?? $request->header('User-Agent');
            $detected = $this->detectDeviceInfo($userAgent);
            
            if (empty($deviceType)) {
                $deviceType = $detected['device_type'];
            }
            if (empty($platform)) {
                $platform = $detected['platform'];
            }
            if (empty($browser)) {
                $browser = $detected['browser'];
            }
            if (empty($request->device_name)) {
                $request->merge(['device_name' => $detected['device_name']]);
            }
        }

        // Check if token exists and is from same device type
        $existingToken = DB::table('fcm_tokens')
            ->where('device_token', $request->device_token)
            ->first();

        if ($existingToken) {
            // Update existing token
            DB::table('fcm_tokens')
                ->where('device_token', $request->device_token)
                ->update([
                    'user_id' => $request->user_id,
                    'device_type' => $deviceType,
                    'platform' => $platform,
                    'browser' => $browser,
                    'device_name' => $request->device_name ?? $this->getDeviceName(),
                    'app_version' => $request->app_version,
                    'status' => 1,
                    'updated_at' => now(),
                ]);
        } else {
            // Insert new token
            DB::table('fcm_tokens')->insert([
                'user_id' => $request->user_id,
                'device_token' => $request->device_token,
                'device_type' => $deviceType,
                'platform' => $platform,
                'browser' => $browser,
                'device_name' => $request->device_name ?? $this->getDeviceName(),
                'app_version' => $request->app_version,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Optional: Clean up old tokens for same user/device
        if ($request->user_id) {
            $this->cleanupOldTokens($request->user_id, $request->device_token, $deviceType);
        }

        return response()->json([
            'success' => true,
            'message' => 'Token saved or updated successfully.',
            'data' => [
                'device_type' => $deviceType,
                'platform' => $platform,
                'browser' => $browser,
            ]
        ]);
    }

    /**
     * Detect device information from User-Agent
     */
    private function detectDeviceInfo($userAgent = null)
    {
        if (!$userAgent) {
            return [
                'device_type' => 'unknown',
                'platform' => 'unknown',
                'browser' => 'unknown',
                'device_name' => 'Unknown Device'
            ];
        }

        $deviceType = 'desktop';
        $platform = 'unknown';
        $browser = 'unknown';
        $deviceName = 'Unknown Device';

        // Detect platform
        if (strpos($userAgent, 'Android') !== false) {
            $platform = 'android';
            $deviceType = 'mobile';
        } elseif (strpos($userAgent, 'iPhone') !== false || strpos($userAgent, 'iPad') !== false) {
            $platform = 'ios';
            $deviceType = 'mobile';
        } elseif (strpos($userAgent, 'Windows') !== false) {
            $platform = 'windows';
        } elseif (strpos($userAgent, 'Macintosh') !== false || strpos($userAgent, 'Mac OS') !== false) {
            $platform = 'macos';
        } elseif (strpos($userAgent, 'Linux') !== false) {
            $platform = 'linux';
        }

        // Detect if mobile
        if (preg_match('/(Mobile|Tablet)/i', $userAgent)) {
            $deviceType = 'mobile';
        }

        // Detect browser
        if (strpos($userAgent, 'Chrome') !== false && strpos($userAgent, 'Edg') === false) {
            $browser = 'chrome';
        } elseif (strpos($userAgent, 'Firefox') !== false) {
            $browser = 'firefox';
        } elseif (strpos($userAgent, 'Safari') !== false && strpos($userAgent, 'Chrome') === false) {
            $browser = 'safari';
        } elseif (strpos($userAgent, 'Edg') !== false) {
            $browser = 'edge';
        } elseif (strpos($userAgent, 'Opera') !== false || strpos($userAgent, 'OPR') !== false) {
            $browser = 'opera';
        }

        return [
            'device_type' => $deviceType,
            'platform' => $platform,
            'browser' => $browser,
            'device_name' => $deviceName,
        ];
    }

    /**
     * Get device name from request or user agent
     */
    private function getDeviceName()
    {
        // You can implement device name detection here
        return request()->header('User-Agent') ? substr(request()->header('User-Agent'), 0, 100) : 'Unknown Device';
    }

    /**
     * Clean up old tokens for the same user/device combination
     */
    private function cleanupOldTokens($userId, $currentToken, $deviceType)
    {
        // Keep only the latest 5 tokens per user per device type
        $tokens = DB::table('fcm_tokens')
            ->where('user_id', $userId)
            ->where('device_type', $deviceType)
            ->orderBy('updated_at', 'desc')
            ->pluck('device_token')
            ->toArray();

        if (count($tokens) > 5) {
            $tokensToDelete = array_slice($tokens, 5);
            DB::table('fcm_tokens')
                ->whereIn('device_token', $tokensToDelete)
                ->where('user_id', $userId)
                ->delete();
        }
    }
    public function sendFirebaseNotification(Request $request)
    {
        $notification = Notification::find($request->notification_id);

        if (!$notification) {
            return response()->json(['status' => false, 'message' => 'Notification not found']);
        }

        // Validate image URL
        $image = config('app.website_url') . 'admin_panel/public/images/notification/' . $notification->image;
        if (!empty($image) && !filter_var($image, FILTER_VALIDATE_URL)) {
            Log::warning('Invalid image URL for notification', ['image' => $image]);
            $image = null; // Fallback to no image
        }

        $tokens = DB::table('fcm_tokens')
            ->select('device_token')
            ->distinct()
            ->pluck('device_token')
            ->toArray();

        if (empty($tokens)) {
            return response()->json(['message' => 'No device token found.'], 404);
        }

        $title = $notification->title;
        $body = $notification->message;
        $data = [
            'notification_id' => (string) $notification->id,
            'redirect_link' => $notification->redirect_link ?? '/',
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
        ];

        // Log notification details
        Log::info('Sending FCM notification', [
            'title' => $title,
            'image' => $image,
            'tokens_count' => count($tokens)
        ]);

        $responses = [];

        foreach ($tokens as $token) {
            try {
                $result = FirebaseV1Notification::send($token, $title, $image, $body, $data);
                $responses[] = [
                    'token' => $token,
                    'status' => 'success',
                    'response' => $result,
                ];
                Log::info("FCM sent successfully for token", [
                    'token' => substr($token, 0, 10) . '...',
                    'response' => $result
                ]);
            } catch (\Exception $e) {
                $responses[] = [
                    'token' => $token,
                    'status' => 'error',
                    'error' => $e->getMessage(),
                ];

                Log::error("FCM send failed", [
                    'token' => substr($token, 0, 10) . '...',
                    'error' => $e->getMessage()
                ]);
            }
        }

        return response()->json([
            'message' => 'Notifications processed.',
            'results' => $responses
        ]);
    }

    public function maintainance()
    {
        return view('underMaintainance');
    }

    // public function index()
    // {

    //    $videos = Banner::join('video', 'video.id', '=', 'banner.video_id')
    //     ->join('type', 'type.id', '=', 'banner.type_id')
    //     ->where('banner.type_id', '=', config('constant.TYPES.MOVIES'))
    //     ->where('banner.is_home_screen', '=', 1)
    //     ->orderByDesc('banner.created_at')->limit(12)
    //     ->get(['banner.id as bid','video.id','video.name','release_date','landscape_url','video_duration','type.name as tname','description']);

    //     $commonVideoQuery = Video::active()->select('video.*', 'language.name as language_name')
    //     ->join('language', 'language.id', '=', 'video.language_id')
    //     ->orderBy('created_at', 'desc')->get();

    //     $trailer = $commonVideoQuery->filter(function ($video) {
    //         return $video->type_id == config('constant.TYPES.TRAILER') && $video->status == 1; // Filter by status on the collection
    //     })->take(12);

    //     $upcomming = $commonVideoQuery->filter(function ($video) {
    //         return $video->type_id == config('constant.TYPES.UPCOMING') && $video->status == 1; // Filter by status on the collection
    //     })->take(12);
    //     $movies = $commonVideoQuery->filter(function ($video) {
    //         return $video->type_id == config('constant.TYPES.MOVIES') && $video->status == 1; // Filter by status on the collection
    //     })->take(12);
    //     $shortfilms = $commonVideoQuery->filter(function ($video) {
    //         return $video->type_id == config('constant.TYPES.SHORTFILMS') && $video->status == 1; // Filter by status on the collection
    //     })->take(12);
    //     $banner = StaticBanners::orderBy('id', 'desc')->limit(4)->get();

    //     $webseries = Series::all();

    //     $music = $commonVideoQuery->filter(function ($video) {
    //         return $video->type_id == config('constant.TYPES.MUSIC') && $video->category_id == 20 && $video->status == 1; // Filter by status on the collection
    //     })->take(12);
    //     $romance = $commonVideoQuery->filter(function ($video) {
    //         return $video->type_id == config('constant.TYPES.MOVIES') && $video->category_id == 12 && $video->status == 1; // Filter by status on the collection
    //     })->take(12);
    //     $action = $commonVideoQuery->filter(function ($video) {
    //         return $video->type_id == config('constant.TYPES.MOVIES') && $video->category_id == 13 && $video->status == 1; // Filter by status on the collection
    //     })->take(12);
    //     $thrill = $commonVideoQuery->filter(function ($video) {
    //         $categories = explode(',', $video->category_id);
    //         return $video->type_id == config('constant.TYPES.MOVIES') && in_array(14, $categories) && $video->status == 1; // Filter by status on the collection
    //     })->take(12); 
    //     $poadcastData = $commonVideoQuery->filter(function ($video) {
    //         // Check if category_id (comma-separated string) contains '27'
    //         $categories = explode(',', $video->category_id);
    //         return in_array(27, $categories) &&
    //             $video->type_id == config('constant.TYPES.PODCAST') &&
    //             $video->status == 1; // Filter by status on the collection
    //     })->take(12);
    //     $comdeyData = $commonVideoQuery->filter(function ($video) {
    //         // Check if category_id (comma-separated string) contains '16'
    //         $categories = explode(',', $video->category_id);
    //         return in_array(16, $categories) &&
    //             $video->type_id == config('constant.TYPES.COMEDYSHOW') &&
    //             $video->status == 1; // Filter by status on the collection
    //     })->take(12);
    //     $khlups = Kluphs::where('type_id', config('constant.TYPES.KHLUP'))->where('user_id',1)
    //      ->orderBy('id', 'desc') // Replace 'id' with the column you want to sort by
    //      ->get();


    //     $watchlist = app('wishlist');
    //     session(['watchlist' => $watchlist]);

    //     return view('index', ['videos' => $videos, 'trailer'=>$trailer, 'upcomming' => $upcomming, 
    //     'romance'=>$romance, 'action'=>$action, 'thrill'=>$thrill, 'music'=>$music, 'poadcastData'=>$poadcastData,
    //      'comdeyData' => $comdeyData,  'movies'=> $movies, 
    //      'shortfilms' => $shortfilms, 'series' => $webseries, 'khlup' => $khlups, 'banner' => $banner, 
    //      'watchlist' => $watchlist]);
    // }


    // In HomeController.php, add this method before the index() method

    public static function getContinueWatchingItems()
    {
        $user = auth()->user();
        if (!$user) {
            return collect();
        }

        // Get all incomplete watch records (exclude trailers)
        $watchReports = UserWatchReport::where('user_id', $user->id)
            ->where('status', 0) // Not completed
            ->where('watch_time', '>', 5) // Watched more than 5 seconds
            ->where('type_id', '!=', config('constant.TYPES.TRAILER', 8))
            ->orderBy('updated_at', 'desc') // Most recent first
            ->take(20) // Limit to 20 items
            ->get();

        $continueWatching = [];
      
        foreach ($watchReports as $report) {
                
            // Check if this is an episode (has episode_id)
            if ($report->episode_id && $report->episode_id > 0) {
                // This is an episode from a series (comedy show or web series)
                $episode = Episodes::find($report->episode_id);
                if ($episode && $episode->status == 1) {
                    $series = Series::find($episode->series_id);
                    
                    // Determine the correct type for watchlist button
                    $itemType = 'episode';
                    
                    // Generate the slug-based URL for episodes
                    if ($series) {
                        // Check type_id to determine if it's Comedy Show or Web Series
                        $typeId = $episode->type_id ?? 10; // Default to Series if not set
                        
                        if ($typeId == 12) { // Comedy Show
                            $redirectUrl = route('comedy.show.episode.view.by.slug', [
                                'slug' => $series->slug ?? \Illuminate\Support\Str::slug($series->name),
                                'season' => $episode->season_id,
                                'episode' => $episode->episode_no
                            ]);
                        } else { // Web Series (type_id == 10)
                            $redirectUrl = route('webseries.episode.view.by.slug', [
                                'slug' => $series->slug ?? \Illuminate\Support\Str::slug($series->name),
                                'season' => $episode->season_id,
                                'episode' => $episode->episode_no
                            ]);
                        }
                    } else {
                        // Fallback to encrypted ID route if series not found
                        $redirectUrl = route('webseries.episodes.view', ['id' => VideoHelper::encryptID($episode->id)]);
                    }
                    
                    $website_url = rtrim(config('app.website_url', 'https://alphastudioz.in/'), '/') . '/';
                    $episodeImage = !empty($episode->landscape) ? $episode->landscape : (!empty($episode->thumbnail) ? $episode->thumbnail : null);
                    $episodeImageUrl = null;
                    if (!empty($episode->landscape_url)) {
                        $episodeImageUrl = $episode->landscape_url;
                    } elseif (!empty($episode->thumbnail_url)) {
                        $episodeImageUrl = $episode->thumbnail_url;
                    } elseif (!empty($episodeImage)) {
                        if (str_starts_with($episodeImage, 'http://') || str_starts_with($episodeImage, 'https://') || str_starts_with($episodeImage, '//')) {
                            $episodeImageUrl = $episodeImage;
                        } else {
                            $episodeImageUrl = $website_url . 'admin_panel/public/images/series/' . ltrim($episodeImage, '/');
                        }
                    }

                    $continueWatching[] = [
                        'id' => $episode->id,
                        'type_id' => $episode->type_id,
                        'category_id' => $episode->category_id,
                        'type' => $itemType,
                        'watch_time' => $report->watch_time,
                        'duration' => $episode->episode_duration / 1000,
                        'title' => $episode->title ?? $episode->name ?? 'Episode ' . $episode->episode_no,
                        'series_name' => $series->name ?? 'Series',
                        'season' => $episode->season_id,
                        'episode_num' => $episode->episode_no,
                        'landscape' => $episode->landscape ?: ($episode->thumbnail ?: null),
                        'poster' => $episode->thumbnail ?: ($episode->landscape ?: null),
                        'landscape_url' => $episode->landscape_url ?? null,
                        'thumbnail_url' => $episode->thumbnail_url ?? null,
                        'image_url' => $episodeImageUrl,
                        'progress_percentage' => min(100, round(($report->watch_time / ($episode->episode_duration / 1000)) * 100)),
                        'redirect_url' => $redirectUrl
                    ];
                }
            } 
            // Check if this is a regular video (has video_id and no episode_id)
            elseif ($report->video_id && $report->video_id > 0 && (!$report->episode_id || $report->episode_id == 0)) {
                $video = Video::find($report->video_id);
             
                if ($video && $video->status == 1) {
                    $typeId = $video->type_id ?? 1;

                    // Skip trailers (trailers should not be added to continue watching)
                    if ($typeId == config('constant.TYPES.TRAILER', 8) || !empty($video->trailer_for)) {
                        continue;
                    }

                    $slug = $video->slug ?? \Illuminate\Support\Str::slug($video->name);
                    
                    // Map type_id to content type and route
                    switch ($typeId) {
                        case 1: // Upcoming
                            $redirectUrl = route('view.details.slug', [
                                'content' => 'upcoming',
                                'slug' => $slug
                            ]);
                            break;
                            
                        case 5: // Music
                            $redirectUrl = route('view.details.slug', [
                                'content' => 'musics',
                                'slug' => $slug
                            ]);
                            break;
                            
                        case 6: // Movies
                            $redirectUrl = route('view.details.slug', [
                                'content' => 'movies',
                                'slug' => $slug
                            ]);
                            break;
                            
                        case 9: // Short Films
                            $redirectUrl = route('view.details.slug', [
                                'content' => 'shortfilms',
                                'slug' => $slug
                            ]);
                            break;
                            
                        case 11: // Podcast
                            $redirectUrl = route('podcast.details', [
                                'slug' => $slug
                            ]);
                            break;
                            
                        case 10: 
                              $redirectUrl = route('webseries.episode.view.by.slug', [
                                'slug' => $series->slug ?? \Illuminate\Support\Str::slug($series->name),
                                'season' => $episode->season_id,
                                'episode' => $episode->episode_no
                            ]);
                            // Series (Web Series) - This should typically be episodes, but handle just in case
                        case 12: 
                            $redirectUrl = route('comedy.show.episode.view.by.slug', [
                                'slug' => $slug,
                                'season' => $video->season_no,
                                'episode' => $video->episode_no
                            ]);
                            break;
                       
                            // Comedy Show - This should typically be episodes, but handle just in case
                        default:
                            // Default fallback for any other type
                            $redirectUrl = route('view.details.slug', [
                                'content' => 'video',
                                'slug' => $slug
                            ]);
                            break;
                    }
                    
                    $website_url = rtrim(config('app.website_url', 'https://alphastudioz.in/'), '/') . '/';
                    $videoImage = !empty($video->landscape) ? $video->landscape : (!empty($video->thumbnail) ? $video->thumbnail : null);
                    $videoImageUrl = null;
                    if (!empty($video->landscape_url)) {
                        $videoImageUrl = $video->landscape_url;
                    } elseif (!empty($video->thumbnail_url)) {
                        $videoImageUrl = $video->thumbnail_url;
                    } elseif (!empty($videoImage)) {
                        if (str_starts_with($videoImage, 'http://') || str_starts_with($videoImage, 'https://') || str_starts_with($videoImage, '//')) {
                            $videoImageUrl = $videoImage;
                        } else {
                            $videoImageUrl = $website_url . 'admin_panel/public/images/video/' . ltrim($videoImage, '/');
                        }
                    }

                    $continueWatching[] = [
                        'id' => $video->id,
                        'type_id' => $video->type_id,
                        'category_id' => $video->category_id,
                        'type' => 'video',
                        'watch_time' => $report->watch_time,
                        'duration' => $video->video_duration / 1000,
                        'title' => $video->name,
                        'series_name' => null,
                        'season' => null,
                        'episode_num' => null,
                        'landscape' => $video->landscape ?: ($video->thumbnail ?: null),
                        'poster' => $video->thumbnail ?: ($video->landscape ?: null),
                        'landscape_url' => $video->landscape_url ?? null,
                        'thumbnail_url' => $video->thumbnail_url ?? null,
                        'image_url' => $videoImageUrl,
                        'progress_percentage' => min(100, round(($report->watch_time / ($video->video_duration / 1000)) * 100)),
                        'redirect_url' => $redirectUrl
                    ];
                  
                }
            }
        }

        return collect($continueWatching);
    }
    public function debugWatchReports()
{
    $user = auth()->user();
    if (!$user) {
        return "No user logged in";
    }
    
    $reports = UserWatchReport::where('user_id', $user->id)
        ->orderBy('id', 'desc')
        ->take(10)
        ->get();
    
    $output = [];
    foreach ($reports as $report) {
        $output[] = [
            'id' => $report->id,
            'type_id' => $report->type_id,
            'video_id' => $report->video_id,
            'episode_id' => $report->episode_id,
            'series_id' => $report->series_id,
            'watch_time' => $report->watch_time,
            'status' => $report->status,
            'created_at' => $report->created_at,
        ];
    }
    
    return response()->json($output);
}

    public function index()
    {
        // 2. Static banners
        $banner = StaticBanners::orderBy('id', 'desc')->limit(4)->get();

        // 3. Series
        $webseries = Episodes::with([
            'series:id,slug,name,season,landscape_image,trailer_url,description,total_episode,season_no',
            'type',
            'category',
            'season'
        ])
        ->where('status', 1)
        ->where('episode_no', 1)
        ->orderByDesc('id')
        ->take(10)
        ->get([
            'id',
            'series_id',
            'type_id',
            'category_id',
            'language_id',
            'episode_duration',
            'thumbnail',    
            'landscape',
            'release_date',
            'description',
            'status',
            'created_at',
            'episode_no',
            'slug'
        ]);

        // 4. Load common videos using individual queries
        $trailer = Video::active()
            ->where('type_id', config('constant.TYPES.TRAILER'))
            ->with('language')
            ->orderByDesc('order_by')
            ->take(10)
            ->get(['id', 'type_id', 'category_id', 'language_id', 'trailer_url','thumbnail', 'landscape', 'release_date', 'video_duration', 'description', 'status', 'created_at','slug']);

        $upcomming = Video::active()
            ->where('type_id', config('constant.TYPES.UPCOMING'))
            ->with('language')
            ->orderByDesc('created_at')
            ->take(10)
            ->get(['id', 'type_id', 'category_id', 'language_id', 'trailer_url','thumbnail', 'landscape', 'release_date', 'video_duration', 'description', 'status', 'created_at','slug']);

        $movies = Video::active()
            ->where('type_id', config('constant.TYPES.MOVIES'))
            ->with('language')
            ->orderByDesc('order_by')
            ->take(10)
            ->get(['id', 'type_id', 'category_id', 'language_id', 'trailer_url','thumbnail', 'landscape', 'release_date', 'video_duration', 'description', 'status', 'created_at','slug']);

        $shortfilms = Video::active()
            ->where('type_id', config('constant.TYPES.SHORTFILMS'))
            ->with('language')
            ->orderByDesc('order_by')
            ->take(10)
            ->get(['id', 'type_id', 'category_id', 'language_id', 'trailer_url','thumbnail', 'landscape', 'release_date', 'video_duration', 'description', 'status', 'created_at','slug']);

        $music = Video::active()
            ->where('type_id', config('constant.TYPES.MUSIC'))
            ->where('category_id', 20)
            ->with('language')
            ->orderByDesc('order_by')
            ->take(10)
            ->get(['id', 'type_id', 'category_id', 'language_id', 'trailer_url','thumbnail', 'landscape', 'release_date', 'video_duration', 'description', 'status', 'created_at','slug']);

        $romance = Video::active()
            ->where('type_id', config('constant.TYPES.MOVIES'))
            ->where('category_id', 12)
            ->with('language')
            ->orderByDesc('created_at')
            ->take(10)
            ->get(['id', 'type_id', 'category_id', 'language_id', 'trailer_url','thumbnail', 'landscape', 'release_date', 'video_duration', 'description', 'status', 'created_at','slug']);

        $action = Video::active()
            ->where('type_id', config('constant.TYPES.MOVIES'))
            ->where('category_id', 13)
            ->with('language')
            ->orderByDesc('created_at')
            ->take(10)
            ->get(['id', 'type_id', 'category_id', 'language_id', 'trailer_url','thumbnail', 'landscape', 'release_date', 'video_duration', 'description', 'status', 'created_at','slug']);

        $thrill = Video::active()
            ->where('type_id', config('constant.TYPES.MOVIES'))
            ->whereRaw("FIND_IN_SET(14, category_id)")
            ->with('language')
            ->orderByDesc('created_at')
            ->take(10)
            ->get(['id', 'type_id', 'category_id', 'language_id', 'trailer_url','thumbnail', 'landscape', 'release_date', 'video_duration', 'description', 'status', 'created_at','slug']);

        $poadcastData = Video::active()
            ->where('type_id', config('constant.TYPES.PODCAST'))->where('language_id', 1)
            ->whereRaw("FIND_IN_SET(27, category_id)")
            ->with('language')
            ->orderByDesc('order_by')
            ->take(10)
            ->get(['id', 'type_id', 'category_id', 'language_id', 'trailer_url','thumbnail', 'landscape', 'release_date', 'video_duration', 'description', 'status', 'created_at','slug']);

        $marathipoadcastData = Video::active()
            ->where('type_id', config('constant.TYPES.PODCAST'))->where('language_id', 2)
            ->whereRaw("FIND_IN_SET(27, category_id)")
            ->with('language')
            ->orderByDesc('order_by')
            ->take(10)
            ->get(['id', 'type_id', 'category_id', 'language_id', 'trailer_url','thumbnail', 'landscape', 'release_date', 'video_duration', 'description', 'status', 'created_at','slug']);

        $comdeyData = Video::active()
            ->where('type_id', config('constant.TYPES.COMEDYSHOW'))
            ->where('is_banner',1)
            ->with('language')
            ->orderByDesc('order_by')
            ->take(10)
            ->get(['id', 'type_id', 'category_id', 'language_id', 'trailer_url', 'landscape', 'release_date', 'video_duration', 'description', 'status', 'created_at','slug','season_no','episode_no']);

        $continueWatching = $this->getContinueWatchingItems();

        // TOP 10 DATA - Fetch top 10 for each type
        $top10Movies = Video::active()
            ->where('type_id', config('constant.TYPES.MOVIES'))
            ->with('language')
            ->orderByDesc('view')
            ->take(10)
            ->get();

        $top10Trailers = Video::active()
            ->where('type_id', config('constant.TYPES.TRAILER'))
            ->with('language')
            ->orderByDesc('view')
            ->take(10)
            ->get();

        $top10Shortfilms = Video::active()
            ->where('type_id', config('constant.TYPES.SHORTFILMS'))
            ->with('language')
            ->orderByDesc('view')
            ->take(10)
            ->get();

        $top10Podcasts = Video::active()
            ->where('type_id', config('constant.TYPES.PODCAST'))
            ->with('language')
            ->orderByDesc('view')
            ->take(10)
            ->get();

        // 6. Watchlist (session set from helper)
        $watchlist = app('wishlist');
        session(['watchlist' => $watchlist]);

        // 7. Return view
        return view('index', [
            'trailer' => $trailer,
            'upcomming' => $upcomming,
            'romance' => $romance,
            'action' => $action,
            'thrill' => $thrill,
            'music' => $music,
            'poadcastData' => $poadcastData,
            'marathipoadcastData' => $marathipoadcastData,
            'comdeyData' => $comdeyData,
            'movies' => $movies,
            'shortfilms' => $shortfilms,
            'series' => $webseries,
            'banner' => $banner,
            'watchlist' => $watchlist,
            'continueWatching' => $continueWatching,
            // Top 10 data
            'top10Movies' => $top10Movies,
            'top10Trailers' => $top10Trailers,
            'top10Shortfilms' => $top10Shortfilms,
            'top10Podcasts' => $top10Podcasts,
        ]);
    }

    public function continueWatchingList(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Get filter parameter
        $filter = $request->get('filter', 'all'); // all, videos, series
        
        // Get all incomplete watch records with pagination (exclude trailers)
        $query = UserWatchReport::where('user_id', $user->id)
            ->where('status', 0) // Not completed
            ->where('watch_time', '>', 5) // Watched more than 5 seconds
            ->where('type_id', '!=', config('constant.TYPES.TRAILER', 8));

        // Apply filter
        if ($filter == 'videos') {
            $query->whereNull('episode_id')->orWhere('episode_id', 0);
        } elseif ($filter == 'series') {
            $query->whereNotNull('episode_id')->where('episode_id', '>', 0);
        }

        $watchReports = $query->orderBy('updated_at', 'desc')
            ->paginate(24); // 24 items per page

        $continueWatching = [];

        foreach ($watchReports as $report) {
           
            // Check if this is an episode
            if ($report->episode_id && $report->episode_id > 0) {
                $episode = Episodes::find($report->episode_id);
                if ($episode && $episode->status == 1) {
                    $series = Series::find($episode->series_id);
                    
                    // Generate redirect URL based on type
                    if ($series) {
                        $typeId = $episode->type_id ?? 10;
                        
                        if ($typeId == 12) { // Comedy Show
                            $redirectUrl = route('comedy.show.episode.view.by.slug', [
                                'slug' => $series->slug ?? \Illuminate\Support\Str::slug($series->name),
                                'season' => $episode->season_id,
                                'episode' => $episode->episode_no
                            ]);
                        } else { // Web Series
                            $redirectUrl = route('webseries.episode.view.by.slug', [
                                'slug' => $series->slug ?? \Illuminate\Support\Str::slug($series->name),
                                'season' => $episode->season_id,
                                'episode' => $episode->episode_no
                            ]);
                        }
                    } else {
                        // Fallback
                        $redirectUrl = route('webseries.episodes.view', ['id' => VideoHelper::encryptID($episode->id)]);
                    }
                    
                    $website_url = rtrim(config('app.website_url', 'https://alphastudioz.in/'), '/') . '/';
                    $episodeImage = !empty($episode->landscape) ? $episode->landscape : (!empty($episode->thumbnail) ? $episode->thumbnail : null);
                    $episodeImageUrl = null;
                    if (!empty($episode->landscape_url)) {
                        $episodeImageUrl = $episode->landscape_url;
                    } elseif (!empty($episode->thumbnail_url)) {
                        $episodeImageUrl = $episode->thumbnail_url;
                    } elseif (!empty($episodeImage)) {
                        if (str_starts_with($episodeImage, 'http://') || str_starts_with($episodeImage, 'https://') || str_starts_with($episodeImage, '//')) {
                            $episodeImageUrl = $episodeImage;
                        } else {
                            $episodeImageUrl = $website_url . 'admin_panel/public/images/series/' . ltrim($episodeImage, '/');
                        }
                    }

                    $continueWatching[] = (object)[
                        'id' => $episode->id,
                        'type_id' => $episode->type_id,
                        'category_id' => $episode->category_id,
                        'type' => 'episode',
                        'watch_time' => $report->watch_time,
                        'duration' => $episode->episode_duration / 1000,
                        'title' => $episode->title ?? $episode->name ?? 'Episode ' . $episode->episode_no,
                        'series_name' => $series->name ?? 'Series',
                        'season' => $episode->season_no,
                        'episode_num' => $episode->episode_no,
                        'landscape' => $episode->landscape ?: ($episode->thumbnail ?: null),
                        'poster' => $episode->thumbnail ?: ($episode->landscape ?: null),
                        'landscape_url' => $episode->landscape_url ?? null,
                        'thumbnail_url' => $episode->thumbnail_url ?? null,
                        'image_url' => $episodeImageUrl,
                        'description' => $episode->description,
                        'release_date' => $episode->release_date,
                        'language_name' => $episode->language_name ?? 'English',
                        'progress_percentage' => min(100, round(($report->watch_time / ($episode->episode_duration / 1000)) * 100)),
                        'redirect_url' => $redirectUrl
                    ];
                }
            } 
            // Regular video
            elseif ($report->video_id && $report->video_id > 0) {
                $video = Video::find($report->video_id);
                if ($video && $video->status == 1) {
                    $typeId = $video->type_id ?? 1;

                    // Skip trailers (trailers should not be added to continue watching)
                    if ($typeId == config('constant.TYPES.TRAILER', 8) || !empty($video->trailer_for)) {
                        continue;
                    }

                    $slug = $video->slug ?? \Illuminate\Support\Str::slug($video->name);
                    
                    // Generate redirect URL based on type_id
                    switch ($typeId) {
                        case 1: // Upcoming
                            $redirectUrl = route('view.details.slug', [
                                'content' => 'upcoming',
                                'slug' => $slug
                            ]);
                            break;
                            
                        case 5: // Music
                            $redirectUrl = route('view.details.slug', [
                                'content' => 'musics',
                                'slug' => $slug
                            ]);
                            break;
                            
                        case 6: // Movies
                            $redirectUrl = route('view.details.slug', [
                                'content' => 'movies',
                                'slug' => $slug
                            ]);
                            break;
                            
                        case 9: // Short Films
                            $redirectUrl = route('view.details.slug', [
                                'content' => 'shortfilms',
                                'slug' => $slug
                            ]);
                            break;
                            
                        case 11: // Podcast
                            $redirectUrl = route('podcast.details', [
                                'slug' => $slug
                            ]);
                            break;
                        
                        case 12: // Comedy-Show
                            $redirectUrl = route('comedy.show.episode.view.by.slug', [
                                'slug' => $slug,
                                'season' => $video->season_no,
                                'episode' => $video->episode_no
                            ]);
                            break;
                            
                        // default:
                        //     // Default fallback
                        //     $redirectUrl = route('view.details.slug', [
                        //         'content' => 'video',
                        //         'slug' => $slug
                        //     ]);
                        //     break;
                    }
                    
                    $website_url = rtrim(config('app.website_url', 'https://alphastudioz.in/'), '/') . '/';
                    $videoImage = !empty($video->landscape) ? $video->landscape : (!empty($video->thumbnail) ? $video->thumbnail : null);
                    $videoImageUrl = null;
                    if (!empty($video->landscape_url)) {
                        $videoImageUrl = $video->landscape_url;
                    } elseif (!empty($video->thumbnail_url)) {
                        $videoImageUrl = $video->thumbnail_url;
                    } elseif (!empty($videoImage)) {
                        if (str_starts_with($videoImage, 'http://') || str_starts_with($videoImage, 'https://') || str_starts_with($videoImage, '//')) {
                            $videoImageUrl = $videoImage;
                        } else {
                            $videoImageUrl = $website_url . 'admin_panel/public/images/video/' . ltrim($videoImage, '/');
                        }
                    }

                    $continueWatching[] = (object)[
                        'id' => $video->id,
                        'type_id' => $video->type_id,
                        'category_id' => $video->category_id,
                        'type' => 'video',
                        'watch_time' => $report->watch_time,
                        'duration' => $video->video_duration / 1000,
                        'title' => $video->name,
                        'series_name' => null,
                        'season' => null,
                        'episode_num' => null,
                        'landscape' => $video->landscape ?: ($video->thumbnail ?: null),
                        'poster' => $video->thumbnail ?: ($video->landscape ?: null),
                        'landscape_url' => $video->landscape_url ?? null,
                        'thumbnail_url' => $video->thumbnail_url ?? null,
                        'image_url' => $videoImageUrl,
                        'description' => $video->description,
                        'release_date' => $video->release_date,
                        'language_name' => $video->language_name,
                        'progress_percentage' => min(100, round(($report->watch_time / ($video->video_duration / 1000)) * 100)),
                        'redirect_url' => $redirectUrl
                    ];
                }
            }
        }

        // Create paginator for the collection
        $continueWatchingCollection = collect($continueWatching);
        $currentPage = $watchReports->currentPage();
        $perPage = $watchReports->perPage();
        $currentItems = $continueWatchingCollection->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $paginatedItems = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $continueWatchingCollection->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $watchlist = app('wishlist');
        session(['watchlist' => $watchlist]);

        // Get filter options for the UI
        $filterOptions = [
            'all' => 'All',
            'videos' => 'Movies & Shows',
            'series' => 'Web Series'
        ];

        return view('continue-watching', [
            'continueWatching' => $paginatedItems,
            'watchlist' => $watchlist,
            'currentFilter' => $filter,
            'filterOptions' => $filterOptions
        ]);
    }

/**
 * Clear a single continue watching item
 */
public function clearContinueWatching(Request $request, $id)
{
    try {
        $user = auth()->user();
        if (!$user) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'User not authenticated']);
            }
            return redirect()->route('login');
        }

        // Try to delete by video_id or episode_id
        $deleted = UserWatchReport::where('user_id', $user->id)
            ->where(function($q) use ($id) {
                $q->where('video_id', $id)
                  ->orWhere('episode_id', $id);
            })
            ->where('status', 0) // Only delete incomplete items
            ->delete();

        Log::info('Clear continue watching item', [
            'user_id' => $user->id,
            'content_id' => $id,
            'deleted_count' => $deleted
        ]);

        if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
            if ($deleted) {
                return response()->json([
                    'success' => true, 
                    'message' => 'Item removed from continue watching',
                    'deleted' => $deleted
                ]);
            }
            return response()->json([
                'success' => false, 
                'message' => 'Item not found or already removed'
            ]);
        }

        return redirect()->back()->with('success', 'Item removed from continue watching');
        
    } catch (\Exception $e) {
        Log::error('Clear continue watching error: ' . $e->getMessage());
        if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'success' => false, 
                'message' => 'Failed to remove item: ' . $e->getMessage()
            ]);
        }
        return redirect()->back()->with('error', 'Failed to remove item');
    }
}

/**
 * Clear all continue watching items for the user
 */
public function clearAllContinueWatching(Request $request)
{
    try {
        $user = auth()->user();
        if (!$user) {
            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'User not authenticated']);
            }
            return redirect()->route('login');
        }

        $deleted = UserWatchReport::where('user_id', $user->id)
            ->where('status', 0)
            ->delete();

        Log::info('Clear all continue watching items', [
            'user_id' => $user->id,
            'deleted_count' => $deleted
        ]);

        if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'success' => true, 
                'message' => $deleted . ' items cleared from continue watching',
                'deleted_count' => $deleted
            ]);
        }

        return redirect()->back()->with('success', $deleted . ' items cleared from continue watching');
        
    } catch (\Exception $e) {
        Log::error('Clear all continue watching error: ' . $e->getMessage());
        if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'success' => false, 
                'message' => 'Failed to clear items: ' . $e->getMessage()
            ]);
        }
        return redirect()->back()->with('error', 'Failed to clear items');
    }
}

    public function indexAppSection()
    {
        // Eager load required video data once
        $commonVideoQuery = Video::active()
            ->select('video.*', 'language.name as language_name')
            ->join('language', 'language.id', '=', 'video.language_id')
            ->orderBy('video.created_at', 'desc')
            ->get();

        // Static, light queries
        $banner = StaticBanners::orderBy('id', 'desc')->limit(4)->get();
        $webseries = Series::all();
        $khlups = Kluphs::where('type_id', config('constant.TYPES.KHLUP'))->where('user_id', 1)->orderBy('id', 'desc')->get();

        $watchlist = app('wishlist');
        session(['watchlist' => $watchlist]);

        // Fetch app section and group video IDs by type
        $typeWiseVideos = [];
        $appsescion = App_Section::get();
        foreach ($appsescion as $a) {
            $type_id = $a['type_id'];
            $video_ids = array_map('intval', explode(',', $a['video_id']));
            $typeWiseVideos[$type_id] = array_unique(array_merge($typeWiseVideos[$type_id] ?? [], $video_ids));
        }

        // Predefine your targets
        $videosByType = [
            'movies' => config('constant.TYPES.MOVIES'),
            'trailer' => config('constant.TYPES.TRAILER'),
            'shortfilms' => config('constant.TYPES.SHORTFILMS'),
            'music' => config('constant.TYPES.MUSIC'),
            'comdeyData' => config('constant.TYPES.COMEDYSHOW'),
            'poadcastData' => config('constant.TYPES.PODCAST'),
            'upcomming' => config('constant.TYPES.UPCOMING'),
        ];

        // Filter videos once
        $videoCollections = [];

        foreach ($videosByType as $var => $typeId) {
            $videoIds = $typeWiseVideos[$typeId] ?? [];
            $videoCollections[$var] = $commonVideoQuery->filter(function ($video) use ($videoIds, $typeId) {
                return in_array($video->id, $videoIds)
                    && $video->type_id == $typeId
                    && $video->status == 1;
            });
        }

        // Handle category-based filtering (single loop)
        $romance = $action = $thrill = collect();

        foreach ($commonVideoQuery as $video) {
            if ($video->type_id == config('constant.TYPES.MOVIES') && $video->status == 1) {
                $categories = explode(',', $video->category_id);
                if (in_array(12, $categories))
                    $romance->push($video);
                if (in_array(13, $categories))
                    $action->push($video);
                if (in_array(14, $categories))
                    $thrill->push($video);
            }
        }

        // Limit them
        $romance = $romance->take(12);
        $action = $action->take(12);
        $thrill = $thrill->take(12);

        // Special Banner Videos
        $videos = Banner::join('video', 'video.id', '=', 'banner.video_id')
            ->join('type', 'type.id', '=', 'banner.type_id')
            ->where('banner.type_id', config('constant.TYPES.MOVIES'))
            ->where('banner.is_home_screen', 1)
            ->orderByDesc('banner.created_at')->limit(12)
            ->get(['banner.id as bid', 'video.id', 'video.name', 'release_date', 'landscape_url', 'video_duration', 'type.name as tname', 'description']);

        // Return response
        return view('index', [
            'videos' => $videos,
            'trailer' => $videoCollections['trailer'],
            'upcomming' => $videoCollections['upcomming'],
            'romance' => $romance,
            'action' => $action,
            'thrill' => $thrill,
            'music' => $videoCollections['music'],
            'poadcastData' => $videoCollections['poadcastData'],
            'comdeyData' => $videoCollections['comdeyData'],
            'movies' => $videoCollections['movies'],
            'shortfilms' => $videoCollections['shortfilms'],
            'series' => $webseries,
            'khlup' => $khlups,
            'banner' => $banner,
            'watchlist' => $watchlist,
        ]);
    }

    public function indexAppSectionWise()
    {
        $commonVideoQuery = Video::active()
            ->select('video.*', 'language.name as language_name')
            ->join('language', 'language.id', '=', 'video.language_id')
            ->orderBy('video.created_at', 'desc')
            ->get();

        // Other static data
        $banner = StaticBanners::orderBy('id', 'desc')->limit(4)->get();
        $webseries = Series::all();
        $khlups = Kluphs::where('type_id', config('constant.TYPES.KHLUP'))
            ->where('user_id', 1)
            ->orderBy('id', 'desc')->get();

        $watchlist = app('wishlist');
        session(['watchlist' => $watchlist]);

        // Get app sections and build sections based on title
        // $appSections = App_Section::orderby('order_by')->get();
        $appSections = App_Section::get();

        $dynamicSections = [];

        foreach ($appSections as $section) {
            $video_ids = array_map('intval', explode(',', $section->video_id));
            $sectionTitle = $section->title ?? 'Untitled Section';

            $videos = $commonVideoQuery->filter(function ($video) use ($video_ids) {
                return in_array($video->id, $video_ids) && $video->status == 1;
            });

            // $dynamicSections[$sectionTitle] = $videos;
            $dynamicSections[$sectionTitle] = [
                'type_id' => $section->type_id,
                'videos' => $videos
            ];
        }

        // Banner videos
        $videos = Banner::join('video', 'video.id', '=', 'banner.video_id')
            ->join('type', 'type.id', '=', 'banner.type_id')
            ->where('banner.type_id', config('constant.TYPES.MOVIES'))
            ->where('banner.is_home_screen', 1)
            ->orderByDesc('banner.created_at')->limit(12)
            ->get(['banner.id as bid', 'video.id', 'video.name', 'release_date', 'landscape_url', 'video_duration', 'type.name as tname', 'description']);

        return view('index', [
            'videos' => $videos,
            'sections' => $dynamicSections, // title => videos
            'series' => $webseries,
            'khlup' => $khlups,
            'banner' => $banner,
            'watchlist' => $watchlist,
        ]);
    }

    public function privacypolicy()
    {
        return view("privacy");
    }

    public function aboutus()
    {
        return view("about-us");
    }

    public function termsandconditions()
    {
        return view("terms-co");
    }

    public function refundandpolicy()
    {
        return view("refund");
    }

    public function helpcenterfq()
    {
        return view("help");
    }

    public function create()
    {
        $videoName = 'example_video';
        return view('videoUploadTest', ['videoName' => $videoName]);
    }

    public function store(Request $request)
    {
        // Validate the uploaded video file
        $request->validate([
            'video' => 'required|mimes:mp4,mov,avi' // Adjust max size as needed
        ]);

        $vidInput = $request->file('video');
        $videoName = time() . '_' . Str::random(10);
        $videoFileName = $videoName . '.' . $vidInput->getClientOriginalExtension();

        // Define resolutions for transcoding
        $resolutions = [
            240 => '426x240',
            360 => '640x360',
            480 => '854x480',
            720 => '1280x720',
            1080 => '1920x1080',
        ];

        // Create the main directory for this video in public storage
        $parentDir = "videos/$videoName";

        Storage::disk('public')->makeDirectory($parentDir);

        // Initialize the master playlist content
        $masterPlaylist = "#EXTM3U\n";

        // Path to the temporary uploaded file (in memory)
        $tempFilePath = $vidInput->getPathname();
        // Create the directory for thumbnails
        $thumbnailDir = "$parentDir/thumbnails";
        Storage::disk('public')->makeDirectory($thumbnailDir);
        foreach ($resolutions as $resolution => $size) {
            // Directory for each quality level
            $qualityDir = "$parentDir/{$resolution}p";
            Storage::disk('public')->makeDirectory($qualityDir);

            // Output file paths
            $outputFile = public_path("storage/$qualityDir/{$resolution}p.m3u8");
            $segmentPattern = public_path("storage/$qualityDir/{$resolution}p_%03d.ts");

            // FFmpeg command to transcode video at different resolutions
            $command = "ffmpeg -i " . escapeshellarg($tempFilePath) .
                " -vf scale=$size -c:a aac -ar 48000 -c:v h264 -profile:v main -crf 20" .
                " -sc_threshold 0 -g 48 -keyint_min 48 -hls_time 4 -hls_playlist_type vod" .
                " -hls_segment_filename \"$segmentPattern\" $outputFile";

            exec($command);
            // Generate thumbnails for each segment
            $segmentFiles = glob(public_path("storage/$qualityDir/*.ts"));  // Get all .ts segment files
            foreach ($segmentFiles as $index => $segmentFile) {
                $thumbnailPath = public_path("storage/$thumbnailDir/{$resolution}p_thumbnail_" . sprintf('%03d', $index) . ".jpg");
                // Generate thumbnail for each .ts file (segment)
                $thumbnailCommand = "ffmpeg -i " . escapeshellarg($segmentFile) .
                    " -vf \"thumbnail,scale=$size\" -vframes 1 -q:v 2 $thumbnailPath";
                exec($thumbnailCommand);
            }

            // Append each resolution to the master playlist
            $masterPlaylist .= "#EXT-X-STREAM-INF:BANDWIDTH=" . (100000 * $resolution) . ",RESOLUTION=$size\n";
            $masterPlaylist .= "{$resolution}p/{$resolution}p.m3u8\n";

        }

        // Move the original video to the public storage directory after transcoding
        $vidInput->storeAs($parentDir, $videoFileName, 'public');

        // Save the master playlist in the parent directory
        File::put(public_path("storage/$parentDir/master.m3u8"), $masterPlaylist);

        return back()->with('success', 'Video uploaded and processed successfully.');
    }

    public function thumbnail_create()
    {
        $output = shell_exec('C:/ffmpeg/bin/ffmpeg -version');
        // dd($output);
        $videoPath = public_path('storage/khlups/1749900542_mWsSn3j6vO.mp4');
        $thumbPath = public_path('storage/thumb/');
        $thumbFile = 'thumb.jpg';

        try {
            // ✅ Check if video file exists
            if (!file_exists($videoPath)) {
                throw new \Exception("Video file not found at: $videoPath");
            }

            // ✅ Check if directory exists and is writable
            if (!is_dir($thumbPath)) {
                mkdir($thumbPath, 0755, true); // create if missing
            }

            if (!is_writable($thumbPath)) {
                throw new \Exception("Thumbnail path is not writable: $thumbPath");
            }

            // ✅ Generate thumbnail
            $thumb = VideoThumbnail::createThumbnail(
                $videoPath,
                $thumbPath,
                $thumbFile,
                2,    // Second
                1080,  // Width
                1920   // Height
            );

            dd("Thumbnail created at: $thumb");

        } catch (\Exception $e) {
            dd("Error: " . $e->getMessage());
        }
    }

}
