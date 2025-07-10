<?php
namespace App\Http\Controllers;
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
class HomeController extends Controller
{
    public function maintainance(){
        return view('underMaintainance');
    }
    public function index()
    {
        
       $videos = Banner::join('video', 'video.id', '=', 'banner.video_id')
        ->join('type', 'type.id', '=', 'banner.type_id')
        ->where('banner.type_id', '=', config('constant.TYPES.MOVIES'))
        ->where('banner.is_home_screen', '=', 1)
        ->orderByDesc('banner.created_at')->limit(12)
        ->get(['banner.id as bid','video.id','video.name','release_date','landscape_url','video_duration','type.name as tname','description']);
        
        $commonVideoQuery = Video::active()->select('video.*', 'language.name as language_name')
        ->join('language', 'language.id', '=', 'video.language_id')
        ->orderBy('created_at', 'desc')->get();

        $trailer = $commonVideoQuery->filter(function ($video) {
            return $video->type_id == config('constant.TYPES.TRAILER') && $video->status == 1; // Filter by status on the collection
        })->take(12);
        
        $upcomming = $commonVideoQuery->filter(function ($video) {
            return $video->type_id == config('constant.TYPES.UPCOMING') && $video->status == 1; // Filter by status on the collection
        })->take(12);
        $movies = $commonVideoQuery->filter(function ($video) {
            return $video->type_id == config('constant.TYPES.MOVIES') && $video->status == 1; // Filter by status on the collection
        })->take(12);
        $shortfilms = $commonVideoQuery->filter(function ($video) {
            return $video->type_id == config('constant.TYPES.SHORTFILMS') && $video->status == 1; // Filter by status on the collection
        })->take(12);
        $banner = StaticBanners::orderBy('id', 'desc')->limit(4)->get();

        $webseries = Series::all();
        
        $music = $commonVideoQuery->filter(function ($video) {
            return $video->type_id == config('constant.TYPES.MUSIC') && $video->category_id == 20 && $video->status == 1; // Filter by status on the collection
        })->take(12);
        $romance = $commonVideoQuery->filter(function ($video) {
            return $video->type_id == config('constant.TYPES.MOVIES') && $video->category_id == 12 && $video->status == 1; // Filter by status on the collection
        })->take(12);
        $action = $commonVideoQuery->filter(function ($video) {
            return $video->type_id == config('constant.TYPES.MOVIES') && $video->category_id == 13 && $video->status == 1; // Filter by status on the collection
        })->take(12);
        $thrill = $commonVideoQuery->filter(function ($video) {
            $categories = explode(',', $video->category_id);
            return $video->type_id == config('constant.TYPES.MOVIES') && in_array(14, $categories) && $video->status == 1; // Filter by status on the collection
        })->take(12); 
        $poadcastData = $commonVideoQuery->filter(function ($video) {
            // Check if category_id (comma-separated string) contains '27'
            $categories = explode(',', $video->category_id);
            return in_array(27, $categories) &&
                $video->type_id == config('constant.TYPES.PODCAST') &&
                $video->status == 1; // Filter by status on the collection
        })->take(12);
        $comdeyData = $commonVideoQuery->filter(function ($video) {
            // Check if category_id (comma-separated string) contains '16'
            $categories = explode(',', $video->category_id);
            return in_array(16, $categories) &&
                $video->type_id == config('constant.TYPES.COMEDYSHOW') &&
                $video->status == 1; // Filter by status on the collection
        })->take(12);
        $khlups = Kluphs::where('type_id', config('constant.TYPES.KHLUP'))->where('user_id',1)
         ->orderBy('id', 'desc') // Replace 'id' with the column you want to sort by
         ->get();
    
        
        $watchlist = app('wishlist');
        session(['watchlist' => $watchlist]);
        
        return view('index', ['videos' => $videos, 'trailer'=>$trailer, 'upcomming' => $upcomming, 
        'romance'=>$romance, 'action'=>$action, 'thrill'=>$thrill, 'music'=>$music, 'poadcastData'=>$poadcastData,
         'comdeyData' => $comdeyData,  'movies'=> $movies, 
         'shortfilms' => $shortfilms, 'series' => $webseries, 'khlup' => $khlups, 'banner' => $banner, 
         'watchlist' => $watchlist]);
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
        foreach (App_Section::get() as $a) {
            $type_id = $a['type_id'];
            $video_ids = array_map('intval', explode(',', $a['video_id']));
            $typeWiseVideos[$type_id] = array_unique(array_merge($typeWiseVideos[$type_id] ?? [], $video_ids));
        }

        // Predefine your targets
        $videosByType = [
            'movies'      => config('constant.TYPES.MOVIES'),
            'trailer'     => config('constant.TYPES.TRAILER'),
            'shortfilms'  => config('constant.TYPES.SHORTFILMS'),
            'music'       => config('constant.TYPES.MUSIC'),
            'comdeyData'  => config('constant.TYPES.COMEDYSHOW'),
            'poadcastData'=> config('constant.TYPES.PODCAST'),
            'upcomming'   => config('constant.TYPES.UPCOMING'),
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
                if (in_array(12, $categories)) $romance->push($video);
                if (in_array(13, $categories)) $action->push($video);
                if (in_array(14, $categories)) $thrill->push($video);
            }
        }

        // Limit them
        $romance = $romance->take(12);
        $action  = $action->take(12);
        $thrill  = $thrill->take(12);

        // Special Banner Videos
        $videos = Banner::join('video', 'video.id', '=', 'banner.video_id')
            ->join('type', 'type.id', '=', 'banner.type_id')
            ->where('banner.type_id', config('constant.TYPES.MOVIES'))
            ->where('banner.is_home_screen', 1)
            ->orderByDesc('banner.created_at')->limit(12)
            ->get(['banner.id as bid', 'video.id', 'video.name', 'release_date', 'landscape_url', 'video_duration', 'type.name as tname', 'description']);

        // Return response
        return view('index', [
            'videos'        => $videos,
            'trailer'       => $videoCollections['trailer'],
            'upcomming'     => $videoCollections['upcomming'],
            'romance'       => $romance,
            'action'        => $action,
            'thrill'        => $thrill,
            'music'         => $videoCollections['music'],
            'poadcastData'  => $videoCollections['poadcastData'],
            'comdeyData'    => $videoCollections['comdeyData'],
            'movies'        => $videoCollections['movies'],
            'shortfilms'    => $videoCollections['shortfilms'],
            'series'        => $webseries,
            'khlup'         => $khlups,
            'banner'        => $banner,
            'watchlist'     => $watchlist,
        ]);
    }

    public function privacypolicy(){
        return view("privacy");
    }
    public function aboutus(){
        return view("about-us");
    }
    public function termsandconditions(){
        return view("terms-co");
    }
    public function refundandpolicy(){
        return view("refund");
    }
    public function helpcenterfq(){
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
    
    
    public function thumbnail_create(){
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
