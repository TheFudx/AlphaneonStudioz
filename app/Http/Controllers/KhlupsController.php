<?php
namespace App\Http\Controllers;
use App\Helpers\SeoHelper;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Kluphs;
use App\Models\KluphsLikes;
use App\Models\KluphsComments;
use App\Models\KluphsCommentReply;
use App\Models\KhlupsCommentReaction;
use App\Models\User;
use App\Models\Notification;
use Validator;
use Str;
use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\TimeCode;
use Illuminate\Support\Facades\Storage;
use VideoThumbnail;
use Log;
use DB;
use Cache;
use App\Services\BunnyStorageService;

class KhlupsController extends Controller
{

    // public function index($tokengen = null)
    // {
    //     // Fetch the selected video
    //     $selectedVideo = Kluphs::with('user')->find($tokengen) ?? Kluphs::where('type_id',config('constant.TYPES.KHLUP'))->where('user_id',1)->orderBy('id', 'desc')->first();

    //     if (!$selectedVideo) {
    //         return redirect()->back()->with('error', 'Video not found.');
    //     }

    //     // Fetch all videos except the selected one
    //     $otherVideos = Kluphs::where('type_id',config('constant.TYPES.KHLUP'))
    //         ->where('id', '!=', $tokengen)->where('user_id',1)
    //         ->orderBy('id', 'desc')
    //         ->get();

    //     // Merge the selected video with the other videos, keeping the selected video first
    //     $videos = collect([$selectedVideo])->merge($otherVideos);

    //     // Fetch notifications and categories
    //     return view('khlups-view', [
    //         'videos' => $videos,
    //         'selectedVideoId' => $tokengen // Pass the selected video's ID to the view
    //     ]);
    // }
    public function index($tokengen = null)
    {
        $request = request();
        $perPage = 10; // change to how many you want per fetch
        if (!empty($tokengen)) {
            $tokengen = \App\Helpers\VideoHelper::decryptID($tokengen);
        }
        // Find selected video (if provided) or fallback to latest of user_id=1
        $selectedVideo = null;
        if ($tokengen) {
            $selectedVideo = Kluphs::with('user')->find($tokengen);

            if ($selectedVideo) {
                SeoHelper::setMeta(
                    $selectedVideo->name,
                    Str::limit(strip_tags($selectedVideo->description ?? ''), 160),
                    $selectedVideo->cast_id,
                    $selectedVideo->landscape_url,
                    url()->current()
                );
            }
        }
        if (!$selectedVideo) {
            $selectedVideo = Kluphs::with('user')
                ->where('type_id', config('constant.TYPES.KHLUP'))
                ->where('user_id', 1)
                ->orderBy('id', 'desc')
                ->first();
        }

        // Build query for other videos excluding selected (if exists)
        $query = Kluphs::with('user')
            ->where('type_id', config('constant.TYPES.KHLUP'))
            ->where('user_id', 1);

        if ($selectedVideo) {
            $query->where('id', '!=', $selectedVideo->id);
        }

        $query->orderBy('id', 'desc');

        // Paginate (works with AJAX)
        $videosPaginator = $query->paginate($perPage)->withQueryString();

        // When AJAX, return only the items HTML so client can append
        if ($request->ajax()) {
            return view('partials.khlup-items', [
                'videos' => $videosPaginator
            ])->render();
        }

        // For initial full-page load: render selected first, then first page of other videos
        return view('khlups-view', [
            'selectedVideo' => $selectedVideo,
            'videos' => $videosPaginator, // paginator (->items() are page results)
        ]);
    }

    public function viewUsersKhlups($tokengen = null)
    {
        $id = app('logged-in-user')->id;
        $selectedVideo = Kluphs::with('user')->where('user_id', $id)->find($tokengen) ??
            Kluphs::where('type_id', config('constant.TYPES.KHLUP'))->where('user_id', $id)->orderBy('id', 'desc')->first();

        if (!$selectedVideo) {
            return redirect()->back()->with('error', 'Video not found.');
        }

        // Fetch all videos except the selected one
        $otherVideos = Kluphs::where('type_id', config('constant.TYPES.KHLUP'))
            ->where('id', '!=', $tokengen)->where('user_id', $id)
            ->orderBy('id', 'desc')
            ->get();

        // Merge the selected video with the other videos, keeping the selected video first
        $videos = collect([$selectedVideo])->merge($otherVideos);

        // Fetch notifications and categories
        return view('khlups-view', [
            'videos' => $videos,
            'selectedVideoId' => $tokengen // Pass the selected video's ID to the view
        ]);
    }
    function hasHighWeaponConfidence(array $weaponData, float $threshold = 0.7): bool
    {
        foreach (['classes', 'firearm_type', 'firearm_action'] as $category) {
            if (isset($weaponData[$category])) {
                foreach ($weaponData[$category] as $label => $prob) {
                    if ($prob >= $threshold) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
    function hasHighOffensiveConfidence(array $offensiveData, float $threshold = 0.7): bool
    {
        foreach ($offensiveData as $type => $prob) {
            if ($prob >= $threshold) {
                return true;
            }
        }
        return false;
    }
    function videoModeration($videoPath)
    {
        // https://dashboard.sightengine.com/usage
        $params = array(
            'media' => new \CurlFile($videoPath),
            // 'models' => 'weapon,alcohol,recreational_drug,medical,offensive-2.0,faces,tobacco,violence,self-harm,nudity-2.1',
            'models' => 'weapon,alcohol,offensive-2.0,tobacco,violence,self-harm',
            'api_user' => '1944693587',
            'api_secret' => 'tVNxa59ubULHncDJoFGSwZ34r33vfwCn',
        );

        $ch = curl_init('https://api.sightengine.com/1.0/video/check-sync.json');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            return response()->json(['status' => 500, 'error' => $error_msg]);
        }

        curl_close($ch);
        $output = json_decode($response, true);


        $reject = false;
        $reasons = [];

        if ($output['ststue'] == 'success') {

            foreach ($output['data']['frames'] as $frame) {
                if (isset($frame['alcohol']) && $frame['alcohol']['prob'] > 0.7) {
                    $reject = true;
                    $reasons[] = 'alcohol';
                }
                if (isset($frame['weapon']) && $this->hasHighWeaponConfidence($frame['weapon'], 0.7)) {
                    $reject = true;
                    $reasons[] = 'weapon';
                }
                if (isset($frame['offensive']) && $this->hasHighOffensiveConfidence($frame['offensive'], 0.7)) {
                    $reject = true;
                    $reasons[] = 'offensive';
                }
                if (isset($frame['tobacco']) && $frame['tobacco']['prob'] > 0.7) {
                    $reject = true;
                    $reasons[] = 'tobacco';
                }

                if (isset($frame['violence']) && $frame['violence']['prob'] > 0.7) {
                    $reject = true;
                    $reasons[] = 'violence';
                }
                if (isset($frame['self-harm']) && $frame['self-harm']['prob'] > 0.7) {
                    $reject = true;
                    $reasons[] = 'self-harm';
                }
            }

            return $reject;
        } else {
            return "Daily Limit Reached";
        }
        // dd($reject);
        // if ($reject) {
        //     return response()->json([
        //         'status' => 403,
        //         'message' => 'Video rejected due to: ' . implode(', ', array_unique($reasons))
        //     ]);
        // }
    }
    public function generate_videoThumbail($videoPath, $thumbPath, $thumbFile)
    {
        try {
            if (!file_exists($videoPath)) {
                throw new \Exception("Video file not found at: $videoPath");
            }
            if (!is_dir($thumbPath)) {
                mkdir($thumbPath, 0755, true); // create if missing
            }
            if (!is_writable($thumbPath)) {
                throw new \Exception("Thumbnail path is not writable: $thumbPath");
            }
            $thumb = VideoThumbnail::createThumbnail(
                $videoPath,
                $thumbPath,
                $thumbFile,
                5,    // Second
                1080,  // Width
                1920   // Height
            );  
            return true;
        } catch (\Exception $e) {
            return $e;
        }
    }
    public function saveKhlup(Request $request)
    {
        Log::info('saveKhlup called-');
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:2',
            'description' => 'required',
            'video' => 'required|file|mimes:mp4,mov,avi,wmv|max:500000',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'validation_error', 'message' => $validator->errors()->first()]);
        }

        DB::beginTransaction();
        $fileName = null;
        if ($request->hasFile('video')) {
            $video = $request->file('video');
            $fileName = time() . '_' . Str::random(10) . '.' . $video->getClientOriginalExtension();
            try {
                $video->storeAs('user-khlups/', $fileName, 'public');
            } catch (\Exception $e) {
                return response()->json(['status' => 'error', 'message' => 'Failed to store video locally.']);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => 'Video file is missing.']);
        }

        $videoPath = public_path('storage/user-khlups/' . $fileName);
        $thumbPath = public_path('storage/user-khlups/');
        $thumbFile = $fileName . '-thumb.jpg';

        $thumb = $this->generate_videoThumbail($videoPath, $thumbPath, $thumbFile);

        if ($thumb) {
            $bunny = new BunnyStorageService();

            // Upload Video to Bunny Storage
            $videoUpload = $bunny->uploadFile($videoPath, 'user-khlups/' . $fileName);
            if (!$videoUpload['success']) {
                DB::rollback();
                Log::error('Failed to upload video to BunnyCDN: ' . json_encode($videoUpload));
                return response()->json(['status' => 'error', 'message' => 'Failed to upload video to Bunny storage.']);
            }

            // Upload Thumbnail to Bunny Storage (khlup-thumbnails folder)
            $thumbUpload = $bunny->uploadFile($thumbPath . '/' . $thumbFile, 'khlup-thumbnails/' . $thumbFile);
            if (!$thumbUpload['success']) {
                DB::rollback();
                Log::error('Failed to upload thumbnail to BunnyCDN: ' . json_encode($thumbUpload));
                return response()->json(['status' => 'error', 'message' => 'Failed to upload thumbnail to Bunny storage.']);
            }

            $videoBunnyUrl = $videoUpload['url'];
            $thumbBunnyUrl = $thumbUpload['url'];

            // Save to database
            $khlup = new Kluphs;
            $khlup->language_id = 1;
            $khlup->type_id = config('constant.TYPES.KHLUP');
            $khlup->user_id = auth()->id();
            $khlup->name = $request->title;
            $khlup->description = $request->description;

            $khlup->video_type = 1;
            $khlup->thumbnail = $thumbFile;
            $khlup->landscape = $fileName;
            $khlup->thumbnail_url = $thumbBunnyUrl;
            $khlup->landscape_url = $thumbBunnyUrl;
            $khlup->download = 1;
            $khlup->video_duration = 100000;
            $khlup->release_date = date('d-m-Y');
            $khlup->release_year = date('Y');
            $khlup->view = 1;
            $khlup->status = 1;

            $khlup->video_320 = $khlup->video_480 = $khlup->video_720 = $khlup->video_1080 = $fileName;
            $khlup->video_320_url = $khlup->video_480_url = $khlup->video_720_url = $khlup->video_1080_url = $videoBunnyUrl;

            $khlup->video_extension = $video->getClientOriginalExtension();
            $khlup->save();
            DB::commit();

            return response()->json(['status' => 'success', 'message' => 'Your video added successfully']);
        } else {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Failed to generate thumbnail for video.']);
        }
    }

    public function getKhlup($id)
    {
        Log::info('getKhlup called for ID: ' . $id);

        $khlup = Kluphs::find($id);

        if (!$khlup) {
            return response()->json(['status' => 'error', 'message' => 'Khlup not found.'], 404);
        }

        return response()->json(['status' => 'success', 'khlup' => $khlup]);
    }

    public function updateKhlup(Request $request, $id)
    {
        Log::info('updateKhlup called for ID: ' . $id);

        // 1. Validation Rules
        $rules = [
            'title' => 'required|min:2',
            'description' => 'required',
            'video' => 'sometimes|file|mimes:mp4,mov,avi,wmv|max:500000',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['status' => 'validation_error', 'message' => $validator->errors()->first()], 422);
        }

        // 2. Find the Khlup
        $khlup = Kluphs::find($id);
        if (!$khlup) {
            return response()->json(['status' => 'error', 'message' => 'Khlup not found.'], 404);
        }

        DB::beginTransaction();
        try {
            $bunny = new BunnyStorageService();

            // 3. Handle New Video Upload (if provided)
            if ($request->hasFile('video')) {
                $video = $request->file('video');

                // Delete old video and thumbnail from Bunny Storage and local storage
                if ($khlup->video_320) {
                    $bunny->deleteFile('user-khlups/' . $khlup->video_320);
                    if (Storage::disk('public')->exists('user-khlups/' . $khlup->video_320)) {
                        Storage::disk('public')->delete('user-khlups/' . $khlup->video_320);
                    }
                }
                if ($khlup->thumbnail) {
                    $bunny->deleteFile('khlup-thumbnails/' . $khlup->thumbnail);
                    if (Storage::disk('public')->exists('user-khlups/' . $khlup->thumbnail)) {
                        Storage::disk('public')->delete('user-khlups/' . $khlup->thumbnail);
                    }
                }

                // Store new video locally for thumbnail generation
                $fileName = time() . '_' . Str::random(10) . '.' . $video->getClientOriginalExtension();
                $video->storeAs('user-khlups/', $fileName, 'public');

                $videoPath = public_path('storage/user-khlups/' . $fileName);
                $thumbPath = public_path('storage/user-khlups/');
                $thumbFile = $fileName . '-thumb.jpg';

                $thumbGenerated = $this->generate_videoThumbail($videoPath, $thumbPath, $thumbFile);

                if (!$thumbGenerated) {
                    DB::rollback();
                    return response()->json(['status' => 'error', 'message' => 'Failed to generate thumbnail for new video.'], 500);
                }

                // Upload new video & thumbnail to Bunny Storage
                $videoUpload = $bunny->uploadFile($videoPath, 'user-khlups/' . $fileName);
                $thumbUpload = $bunny->uploadFile($thumbPath . '/' . $thumbFile, 'khlup-thumbnails/' . $thumbFile);

                if (!$videoUpload['success'] || !$thumbUpload['success']) {
                    DB::rollback();
                    return response()->json(['status' => 'error', 'message' => 'Failed to upload new files to Bunny storage.'], 500);
                }

                $khlup->video_320 = $khlup->video_480 = $khlup->video_720 = $khlup->video_1080 = $fileName;
                $khlup->video_320_url = $khlup->video_480_url = $khlup->video_720_url = $khlup->video_1080_url = $videoUpload['url'];
                $khlup->thumbnail = $thumbFile;
                $khlup->thumbnail_url = $thumbUpload['url'];
                $khlup->landscape = $fileName;
                $khlup->landscape_url = $thumbUpload['url'];
                $khlup->video_extension = $video->getClientOriginalExtension();
            }

            // 4. Update General Khlup Fields
            $khlup->name = $request->title;
            $khlup->description = $request->description;
            $khlup->save();

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Khlup updated successfully!']);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating Khlup: ' . $e->getMessage() . ' on line ' . $e->getLine() . ' in ' . $e->getFile());
            return response()->json(['status' => 'error', 'message' => 'Failed to update Khlup. Please try again.'], 500);
        }
    }

    public function deleteKhlup(Request $request)
    {
        $data = $request->all();
        $id = $data['id'];
        $user_id = auth()->id();

        $check = Kluphs::where('id', $id)->where('user_id', $user_id)->first();

        if ($check) {
            $bunny = new BunnyStorageService();
            if ($check->video_320) {
                $bunny->deleteFile('user-khlups/' . $check->video_320);
                if (Storage::disk('public')->exists('user-khlups/' . $check->video_320)) {
                    Storage::disk('public')->delete('user-khlups/' . $check->video_320);
                }
            }
            if ($check->thumbnail) {
                $bunny->deleteFile('khlup-thumbnails/' . $check->thumbnail);
                if (Storage::disk('public')->exists('user-khlups/' . $check->thumbnail)) {
                    Storage::disk('public')->delete('user-khlups/' . $check->thumbnail);
                }
            }

            $check->delete();
            return response()->json(['status' => 'success', 'message' => 'Your video Deleted successfully']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something went wrong']);
        }
    }

    /**
     * Format a comment item (and recursively its replies) for JSON responses and views
     */
    private function formatCommentItem($comment, $userId = null)
    {
        $u = $comment->user;
        $profilePicUrl = null;
        $initial = 'G';
        $name = 'Guest';

        if ($u) {
            $nameTrimmed = trim($u->name ?? '');
            $emailTrimmed = trim($u->email ?? '');
            $usernameTrimmed = trim($u->username ?? '');
            $firstNameTrimmed = trim($u->first_name ?? '');

            $isAdmin = ($u->id == 1 || strcasecmp($nameTrimmed, config('app.project_name')) === 0);

            if ($isAdmin) {
                $name = config('app.project_name');
                $initial = 'A';
            } else {
                if ($nameTrimmed !== '') {
                    $name = $nameTrimmed;
                    $initial = strtoupper(mb_substr($nameTrimmed, 0, 1));
                } elseif ($usernameTrimmed !== '') {
                    $name = $usernameTrimmed;
                    $initial = strtoupper(mb_substr($usernameTrimmed, 0, 1));
                } elseif ($firstNameTrimmed !== '') {
                    $name = $firstNameTrimmed;
                    $initial = strtoupper(mb_substr($firstNameTrimmed, 0, 1));
                } elseif ($emailTrimmed !== '') {
                    $name = explode('@', $emailTrimmed)[0];
                    $initial = strtoupper(mb_substr($emailTrimmed, 0, 1));
                } else {
                    $name = 'Guest';
                    $initial = 'G';
                }
            }

            if (!$isAdmin && !empty($u->profile_picture)) {
                $rawPath = $u->profile_picture;
                $cleanPath = preg_replace('/^profile_pictures\//', '', $rawPath);
                if (file_exists(public_path('storage/profile_pictures/' . $cleanPath))) {
                    $profilePicUrl = asset('storage/profile_pictures/' . $cleanPath);
                } elseif (file_exists(public_path('storage/' . $rawPath))) {
                    $profilePicUrl = asset('storage/' . $rawPath);
                }
            }
        }

        $reactions = $comment->reactions ?? collect();
        $reactionCount = $reactions->count();
        $userReaction = null;
        if ($userId) {
            $userReactionRecord = $reactions->firstWhere('user_id', $userId);
            if ($userReactionRecord) {
                $userReaction = $userReactionRecord->reaction;
            }
        }

        $reactionsBreakdown = $reactions->groupBy('reaction')->map->count()->sortDesc()->take(3)->toArray();

        $replies = [];
        if ($comment->relationLoaded('replies') && $comment->replies) {
            foreach ($comment->replies as $reply) {
                $replies[] = $this->formatCommentItem($reply, $userId);
            }
        }

        return [
            'id' => (int) $comment->id,
            'parent_id' => $comment->parent_id ? (int) $comment->parent_id : null,
            'khlup_id' => (int) $comment->khlup_id,
            'user_id' => (int) $comment->user_id,
            'uname' => $name,
            'initial' => $initial,
            'profile_picture' => $profilePicUrl,
            'comment' => (string) $comment->comment,
            'created_at' => $comment->created_at ? $comment->created_at->toDateTimeString() : null,
            'time_ago' => $comment->created_at ? $comment->created_at->diffForHumans(null, true) : 'just now',
            'reactions_count' => (int) $reactionCount,
            'user_reaction' => $userReaction,
            'reactions_breakdown' => $reactionsBreakdown,
            'can_delete' => ($userId && (int) $userId === (int) $comment->user_id),
            'replies' => $replies,
            'replies_count' => count($replies),
        ];
    }

    /**
     * Toggle Like on a Khlup
     */
    public function khlupsLikes(Request $request)
    {
        $user = auth()->user() ?? (app()->bound('logged-in-user') ? app('logged-in-user') : null);
        if (!$user) {
            return response()->json([
                'status' => 'unauthenticated',
                'message' => 'Please login to like this video.'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'action' => 'nullable|string|in:like,unlike,toggle'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
        }

        $id = (int) $request->input('id');
        $check = Kluphs::where('id', $id)->first();
        if (!$check) {
            return response()->json(['status' => 'error', 'message' => 'Video not found'], 404);
        }

        $existing = KluphsLikes::where('khlup_id', $id)->where('user_id', $user->id)->first();
        $action = $request->input('action', 'toggle');

        if ($action === 'unlike' || ($action === 'toggle' && $existing)) {
            if ($existing) {
                $existing->delete();
                $check->decrement('likes');
                $check->save();
            }
            $liked = false;
        } else {
            if (!$existing) {
                KluphsLikes::create([
                    'khlup_id' => $id,
                    'user_id' => $user->id,
                    'count' => 1
                ]);
                $check->increment('likes');
                $check->save();
            }
            $liked = true;
        }

        $check->refresh();
        $likesCount = max(0, (int) $check->likes);

        return response()->json([
            'status' => 'success',
            'success' => true,
            'liked' => $liked,
            'likes_count' => $likesCount,
            'message' => $liked ? 'Video liked' : 'Video unliked'
        ]);
    }

    /**
     * Get all comments for a Khlup (including nested replies & reactions)
     */
    public function getKhlupsComments($id)
    {
        $user = auth()->user() ?? (app()->bound('logged-in-user') ? app('logged-in-user') : null);
        $userId = $user ? $user->id : null;

        $video = Kluphs::find($id);
        if (!$video) {
            return response()->json(['status' => 'error', 'message' => 'Video not found'], 404);
        }

        $topComments = KluphsComments::with([
            'user',
            'reactions',
            'replies.user',
            'replies.reactions'
        ])
        ->where('khlup_id', $id)
        ->whereNull('parent_id')
        ->orderBy('created_at', 'desc')
        ->get();

        $formatted = [];
        foreach ($topComments as $comment) {
            $formatted[] = $this->formatCommentItem($comment, $userId);
        }

        return response()->json([
            'status' => 'success',
            'success' => true,
            'total_count' => max(0, (int) $video->comments),
            'comments' => $formatted
        ]);
    }

    /**
     * Create a comment or reply on a Khlup
     */
    public function khlupsComments(Request $request)
    {
        $user = auth()->user() ?? (app()->bound('logged-in-user') ? app('logged-in-user') : null);
        if (!$user) {
            return response()->json([
                'status' => 'unauthenticated',
                'message' => 'Please login to comment.'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'comment' => 'required|string|min:1|max:1000',
            'parent_id' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'validation_error',
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $id = (int) $request->input('id');
        $commentText = trim(strip_tags($request->input('comment')));
        $parentId = $request->input('parent_id') ? (int) $request->input('parent_id') : null;

        if (empty($commentText)) {
            return response()->json([
                'status' => 'validation_error',
                'success' => false,
                'message' => 'Comment cannot be empty.'
            ], 422);
        }

        $video = Kluphs::where('id', $id)->first();
        if (!$video) {
            return response()->json(['status' => 'error', 'message' => 'Video not found'], 404);
        }

        // Verify parent comment belongs to this video if replying
        if ($parentId) {
            $parentCheck = KluphsComments::where('id', $parentId)->where('khlup_id', $id)->first();
            if (!$parentCheck) {
                $parentId = null;
            }
        }

        $comment = KluphsComments::create([
            'khlup_id' => $id,
            'user_id' => $user->id,
            'comment' => $commentText,
            'parent_id' => $parentId,
            'comment_likes' => 0
        ]);

        $video->increment('comments');
        $video->refresh();

        $comment->load(['user', 'reactions', 'replies']);
        $formatted = $this->formatCommentItem($comment, $user->id);

        return response()->json([
            'status' => 'success',
            'success' => true,
            'comment' => $formatted,
            'total_comments' => max(0, (int) $video->comments),
            'message' => $parentId ? 'Reply posted successfully' : 'Comment posted successfully'
        ]);
    }

    /**
     * Delete a comment or reply (Owner only)
     */
    public function deleteKhlupsComment($id)
    {
        $user = auth()->user() ?? (app()->bound('logged-in-user') ? app('logged-in-user') : null);
        if (!$user) {
            return response()->json([
                'status' => 'unauthenticated',
                'message' => 'Please login to delete comments.'
            ], 401);
        }

        $comment = KluphsComments::where('id', $id)->first();
        if (!$comment) {
            return response()->json(['status' => 'error', 'message' => 'Comment not found.'], 404);
        }

        if ((int) $comment->user_id !== (int) $user->id) {
            return response()->json([
                'status' => 'unauthorized',
                'message' => 'You can only delete your own comments.'
            ], 403);
        }

        $khlupId = $comment->khlup_id;
        $video = Kluphs::find($khlupId);

        // Count this comment plus any replies if it's a parent comment
        $childReplyIds = KluphsComments::where('parent_id', $comment->id)->pluck('id')->toArray();
        $totalDeletedCount = 1 + count($childReplyIds);

        DB::beginTransaction();
        try {
            // Delete reactions on this comment and child replies
            $allIdsToDelete = array_merge([$comment->id], $childReplyIds);
            KhlupsCommentReaction::whereIn('comment_id', $allIdsToDelete)->delete();

            // Delete child replies
            if (!empty($childReplyIds)) {
                KluphsComments::whereIn('id', $childReplyIds)->delete();
            }

            // Delete comment
            $comment->delete();

            if ($video) {
                $newCount = max(0, (int) $video->comments - $totalDeletedCount);
                $video->comments = $newCount;
                $video->save();
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'success' => true,
                'deleted_id' => (int) $id,
                'parent_id' => $comment->parent_id ? (int) $comment->parent_id : null,
                'new_comments_count' => $video ? (int) $video->comments : 0,
                'message' => 'Comment deleted successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting comment: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to delete comment.'], 500);
        }
    }

    /**
     * Update a comment or reply (Owner only)
     */
    public function updateKhlupsComment(Request $request, $id)
    {
        $user = auth()->user() ?? (app()->bound('logged-in-user') ? app('logged-in-user') : null);
        if (!$user) {
            return response()->json([
                'status' => 'unauthenticated',
                'message' => 'Please login to edit your comment.'
            ], 401);
        }

        $newText = trim($request->input('comment', ''));
        if ($newText === '') {
            return response()->json([
                'status' => 'error',
                'message' => 'Comment cannot be empty.'
            ], 422);
        }

        if (mb_strlen($newText) > 1000) {
            return response()->json([
                'status' => 'error',
                'message' => 'Comment cannot exceed 1000 characters.'
            ], 422);
        }

        $comment = KluphsComments::where('id', $id)->first();
        if (!$comment) {
            return response()->json(['status' => 'error', 'message' => 'Comment not found.'], 404);
        }

        if ((int) $comment->user_id !== (int) $user->id) {
            return response()->json([
                'status' => 'unauthorized',
                'message' => 'You can only edit your own comments.'
            ], 403);
        }

        $comment->comment = $newText;
        $comment->save();

        return response()->json([
            'status' => 'success',
            'success' => true,
            'comment_id' => (int) $comment->id,
            'comment' => $comment->comment,
            'message' => 'Comment updated successfully.'
        ]);
    }

    /**
     * React / Unreact to a comment with an emoji
     */
    public function khlupsCommentReact(Request $request, $id)
    {
        $user = auth()->user() ?? (app()->bound('logged-in-user') ? app('logged-in-user') : null);
        if (!$user) {
            return response()->json([
                'status' => 'unauthenticated',
                'message' => 'Please login to react to comments.'
            ], 401);
        }

        $allowedEmojis = ['❤️', '🔥', '😂', '👏', '😮', '😢'];

        $validator = Validator::make($request->all(), [
            'reaction' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Invalid reaction'], 422);
        }

        $requestedReaction = $request->input('reaction');
        if ($requestedReaction && !in_array($requestedReaction, $allowedEmojis)) {
            $requestedReaction = '❤️';
        }

        $comment = KluphsComments::find($id);
        if (!$comment) {
            return response()->json(['status' => 'error', 'message' => 'Comment not found'], 404);
        }

        $existing = KhlupsCommentReaction::where('comment_id', $id)
            ->where('user_id', $user->id)
            ->first();

        $activeReaction = null;

        if ($existing) {
            if ($existing->reaction === $requestedReaction || empty($requestedReaction)) {
                // Clicking same reaction removes it (unreact)
                $existing->delete();
                $activeReaction = null;
            } else {
                // Change reaction to the new emoji
                $existing->reaction = $requestedReaction;
                $existing->save();
                $activeReaction = $requestedReaction;
            }
        } else {
            if (!empty($requestedReaction)) {
                KhlupsCommentReaction::create([
                    'comment_id' => $id,
                    'user_id' => $user->id,
                    'reaction' => $requestedReaction
                ]);
                $activeReaction = $requestedReaction;
            }
        }

        // Recalculate totals and breakdown
        $reactions = KhlupsCommentReaction::where('comment_id', $id)->get();
        $reactionCount = $reactions->count();
        $breakdown = $reactions->groupBy('reaction')->map->count()->sortDesc()->take(3)->toArray();

        // Update legacy comment_likes column for backwards compatibility
        $comment->comment_likes = $reactionCount;
        $comment->save();

        return response()->json([
            'status' => 'success',
            'success' => true,
            'user_reaction' => $activeReaction,
            'reactions_count' => (int) $reactionCount,
            'reactions_breakdown' => $breakdown,
            'comment_id' => (int) $id
        ]);
    }

    /**
     * Increment shares count for a Khlup
     */
    public function khlupsShare(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Invalid video ID'], 422);
        }

        $id = (int) $request->input('id');
        $video = Kluphs::find($id);
        if (!$video) {
            return response()->json(['status' => 'error', 'message' => 'Video not found'], 404);
        }

        $video->increment('shares');
        $video->refresh();

        return response()->json([
            'status' => 'success',
            'success' => true,
            'shares_count' => max(0, (int) $video->shares),
            'message' => 'Share registered'
        ]);
    }

    /**
     * Legacy endpoints kept for backwards compatibility
     */
    public function khlupsCommentlike(Request $request)
    {
        return $this->khlupsCommentReact($request, $request->input('id'));
    }

    public function khlupsCommentReplies(Request $request)
    {
        $request->merge([
            'id' => $request->input('khlup_id'),
            'parent_id' => $request->input('comment_id'),
            'comment' => $request->input('reply')
        ]);
        return $this->khlupsComments($request);
    }
}
