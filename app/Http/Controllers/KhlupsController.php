<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Kluphs;
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

class KhlupsController extends Controller
{
    
    public function index($tokengen = null)
    {
        // Fetch the selected video
        $selectedVideo = Kluphs::with('user')->find($tokengen) ?? Kluphs::where('type_id',config('constant.TYPES.KHLUP'))->where('user_id',1)->orderBy('id', 'desc')->first();
    
        if (!$selectedVideo) {
            return redirect()->back()->with('error', 'Video not found.');
        }
    
        // Fetch all videos except the selected one
        $otherVideos = Kluphs::where('type_id',config('constant.TYPES.KHLUP'))
            ->where('id', '!=', $tokengen)->where('user_id',1)
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
    public function viewUsersKhlups($tokengen = null){
        $id = app('logged-in-user')->id;
        $selectedVideo = Kluphs::with('user')->where('user_id',$id)->find($tokengen) ?? 
                            Kluphs::where('type_id',config('constant.TYPES.KHLUP'))->where('user_id',$id)->orderBy('id', 'desc')->first();
    
        if (!$selectedVideo) {
            return redirect()->back()->with('error', 'Video not found.');
        }
    
        // Fetch all videos except the selected one
        $otherVideos = Kluphs::where('type_id',config('constant.TYPES.KHLUP'))
            ->where('id', '!=', $tokengen)->where('user_id',$id)
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
    function hasHighWeaponConfidence(array $weaponData, float $threshold = 0.7): bool {
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
    function hasHighOffensiveConfidence(array $offensiveData, float $threshold = 0.7): bool {
        foreach ($offensiveData as $type => $prob) {
            if ($prob >= $threshold) {
                return true;
            }
        }
        return false;
    }
    function videoModeration($videoPath){
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
       
        if($output['ststue'] == 'success'){

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
        }else{
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
    public function generate_videoThumbail($videoPath,$thumbPath,$thumbFile){
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
       Log::info('called-');
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
                    return response()->json(['status' => 'error', 'message' => 'Failed to upload video.']);
                }
            } else {
                return response()->json(['status' => 'error', 'message' => 'Video file is missing.']);
            }

      
                $videoPath = public_path('storage/user-khlups/' . $fileName);
                $thumbPath = public_path('storage/user-khlups/');
                $thumbFile = $fileName . '-thumb.jpg';


                $thumb = $this->generate_videoThumbail($videoPath, $thumbPath, $thumbFile);
                 // Set lock for 5 seconds
                // Cache::put($cacheKey, true, 5);
                if($thumb){
                    // Save to database
                    $khlup = new Kluphs;
                    $khlup->language_id = 1;
                    $khlup->type_id = config('constant.TYPES.KHLUP');
                    $khlup->user_id = auth()->id();
                    $khlup->name = $request->title;
                    $khlup->description = $request->description;
    
                    $khlup->video_320_url = $fileName;
                    $khlup->video_type = 1;
                    $khlup->thumbnail = $thumbFile;
                    $khlup->landscape = $fileName;
                    $khlup->thumbnail_url = 'http://192.168.1.150:2000/storage/user-khlups/' . $thumbFile;
                    $khlup->landscape_url = 'http://192.168.1.150:2000/storage/user-khlups/' . $fileName;
                    $khlup->download = 1;
                    $khlup->video_duration = 100000; // This should ideally be extracted from the video
                    $khlup->release_date = date('d-m-Y');
                    $khlup->release_year = date('Y');
                    $khlup->view = 1;
                    $khlup->status = 1;
    
                    $khlup->video_320 = $khlup->video_480 = $khlup->video_720 = $khlup->video_1080 = $fileName;
                    $khlup->video_320_url = $khlup->video_480_url = $khlup->video_720_url = $khlup->video_1080_url = 'http://192.168.1.150:2000/storage/user-khlups/' . $fileName;
    
                    $khlup->video_extension = $video->getClientOriginalExtension();
                    $khlup->save();
                    DB::commit();
                    return response()->json(['status' => 'success', 'message' => 'Your video added successfully']);
                }else{
                    DB::rollback();
                    return response()->json(['status' => 'error', 'message' => 'Something went wrong']);
                }

            
    }
    public function getKhlup($id)
    {
        Log::info('getKhlup called for ID: ' . $id);

        $khlup = Kluphs::find($id); // Find the Khlup by its ID

        if (!$khlup) {
            // If the Khlup is not found, return a 404 response
            return response()->json(['status' => 'error', 'message' => 'Khlup not found.'], 404);
        }

        // Return the Khlup data as a JSON response
        // It's crucial to return a 'video_url' that is directly accessible by the frontend
        // For local development, this might involve Storage::url() or direct concatenation
        // $khlup->video_url = asset('storage/user-khlups/' . $khlup->video_320_url);
        // You might already have a getter for this in your model.
        // If your model already has an accessor for `video_url`, ensure it returns a full URL.
        // Example: in Kluphs model:
        /*
            public function getVideoUrlAttribute()
            {
                return asset('storage/user-khlups/' . $this->video_320_url);
            }
        */

        return response()->json(['status' => 'success', 'khlup' => $khlup]);
    }
    public function updateKhlup(Request $request, $id)
    {
        Log::info('updateKhlup called for ID: ' . $id);

        // 1. Validation Rules
        $rules = [
            'title' => 'required|min:2',
            'description' => 'required',
            // Video is OPTIONAL for update.
            // If provided, it must be a file, with allowed mimes, and max size.
            'video' => 'sometimes|file|mimes:mp4,mov,avi,wmv|max:500000',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['status' => 'validation_error', 'message' => $validator->errors()->first()], 422); // 422 Unprocessable Entity
        }

        // 2. Find the Khlup
        $khlup = Kluphs::find($id); // Use find() or findOrFail()
        if (!$khlup) {
            return response()->json(['status' => 'error', 'message' => 'Khlup not found.'], 404);
        }

        DB::beginTransaction();
        try {
            $fileName = $khlup->video_320_url; // Keep existing file name by default
            $thumbFile = $khlup->thumbnail; // Keep existing thumbnail by default

            // 3. Handle New Video Upload (if provided)
            if ($request->hasFile('video')) {
                $video = $request->file('video');

                // Delete old video and thumbnail files from storage
                if ($khlup->video_320_url && Storage::disk('public')->exists('user-khlups/' . $khlup->video_320_url)) {
                    Storage::disk('public')->delete('user-khlups/' . $khlup->video_320_url);
                }
                if ($khlup->thumbnail && Storage::disk('public')->exists('user-khlups/' . $khlup->thumbnail)) {
                    Storage::disk('public')->delete('user-khlups/' . $khlup->thumbnail);
                }

                // Store new video
                $fileName = time() . '_' . Str::random(10) . '.' . $video->getClientOriginalExtension();
                $video->storeAs('user-khlups/', $fileName, 'public');

                // Generate new thumbnail for the new video
                $videoPath = public_path('storage/user-khlups/' . $fileName);
                $thumbPath = public_path('storage/user-khlups/');
                $thumbFile = $fileName . '-thumb.jpg';

                $thumbGenerated = $this->generate_videoThumbail($videoPath, $thumbPath, $thumbFile);

                if (!$thumbGenerated) {
                    DB::rollback();
                    return response()->json(['status' => 'error', 'message' => 'Failed to generate thumbnail for new video.'], 500);
                }

                // Update video specific fields for the new video
                $khlup->video_320_url = $fileName;
                $khlup->thumbnail = $thumbFile;
                // Update other resolution fields if you have them
                $khlup->video_320 = $khlup->video_480 = $khlup->video_720 = $khlup->video_1080 = $fileName;
                $khlup->video_320_url = $khlup->video_480_url = $khlup->video_720_url = $khlup->video_1080_url = 'http://192.168.1.150:2000/storage/user-khlups/' . $fileName;
                $khlup->video_extension = $video->getClientOriginalExtension();
                // If you extract video duration, update it here for the new video
                // $khlup->video_duration = 100000;
            }

            // 4. Update General Khlup Fields
            $khlup->name = $request->title;
            $khlup->description = $request->description;
            $khlup->thumbnail_url = 'http://192.168.1.150:2000/storage/user-khlups/' . $thumbFile; // Update thumbnail URL
            $khlup->landscape_url = 'http://192.168.1.150:2000/storage/user-khlups/' . $fileName; // Update landscape URL (pointing to video file)

            // Other fields that might be updated (or remain constant)
            // $khlup->language_id = 1;
            // $khlup->type_id = config('constant.TYPES.KHLUP');
            // $khlup->user_id = auth()->id(); // Should remain the same for an update
            // $khlup->download = 1;
            // $khlup->view = 1;
            // $khlup->status = 1;
            // $khlup->release_date = date('d-m-Y');
            // $khlup->release_year = date('Y');

            $khlup->save();

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Khlup updated successfully!']);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating Khlup: ' . $e->getMessage() . ' on line ' . $e->getLine() . ' in ' . $e->getFile());
            return response()->json(['status' => 'error', 'message' => 'Failed to update Khlup. Please try again.'], 500);
        }
    }
    public function deleteKhlup(Request $request){
        $data = $request->all();
        $id = $data['id'];
        $user_id = auth()->id();

        $check = Kluphs::where('id',$id)->where('user_id',$user_id)->first();

        if($check){
            $check->delete();
            return response()->json(['status' => 'success', 'message' => 'Your video Deleted successfully']);
        }else{
            return response()->json(['status' => 'error', 'message' => 'Something went wrong']);
        }
    }
    
}
