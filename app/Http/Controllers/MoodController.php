<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Video;
use App\Models\Episodes;
use App\Models\Cast;
use App\Models\Category;
use App\Models\Notification;
use App\Models\Watchlists;
use App\Models\Series;
use App\Models\UserDownloads;
use App\Helpers\VideoHelper;
use App\Helpers\SeoHelper;
use App\Models\Language;
use App\Models\UserWatchReport;
use Str;

class MoodController extends Controller
{
    public function index($id)
    {
        $id = \App\Helpers\VideoHelper::decryptID($id);
        $categoryIds = intval($id); // Convert $id to integer
        
        if($categoryIds === 26){
            $videos = Series::all();
        }else{
            $videos = Video::where('status',1)->whereRaw("FIND_IN_SET(?, category_id)", [$categoryIds])->orderBy('order_by', 'ASC')// Add the limit of 1
                ->get();
                if ($videos->isEmpty()) {
                    $videos = Episodes::where('status',1)->orderBy('order_by', 'ASC')
                    ->whereRaw("FIND_IN_SET(?, category_id)", [$categoryIds])
                        ->get();
                }
        }
        $categoryName = Category::where('id',$id)->first();
        if($categoryName){
            $name = $categoryName->name;
        }else{
            $name = 'Video';
        }
        
        $watchlist = app('wishlist');
        session(['watchlist' => $watchlist]);
        return view('mood', [ 'video' => $videos, 'id' => $id,'name'=>$name, 'watchlist' => $watchlist,'id' => $id]);
    }

    public function yourMoodDetails($id, $category_id)
    {
        $id = \App\Helpers\VideoHelper::decryptID($id);
        $categoryIds = $category_id;

    
        $video = Video::active()
            ->select('video.*', 'language.name as language_name')
            ->join('language', 'language.id', '=', 'video.language_id')
            ->where('video.id', $id)
            ->whereRaw("FIND_IN_SET(?, video.category_id)", [$categoryIds])->where('video.status', 1)
            ->firstOrFail();

        // Increment view
        $video->increment('view');
        // Cast & Category
        $castIds = !empty($video->cast_id) ? explode(',', $video->cast_id) : [];
        $catlist = !empty($video->category_id) ? explode(',', $video->category_id) : [];

        $casts = Cast::whereIn('id', $castIds)->get();
        $catListData = Category::whereIn('id', $catlist)->get();

       
        $user_downloads = UserDownloads::where('user_id', auth()->id())
            ->where('video_id', $id)
            ->first();

        $watchlist = app('wishlist');
        session(['watchlist' => $watchlist]);

        SeoHelper::setMeta(
            $video->name,
            Str::limit(strip_tags($video->description), 160),
            $video->cast_id,
            $video->landscape_url,
            url()->current()
        );

        // Watch resume logic
        $userWatchReport = UserWatchReport::where('user_id', auth()->id())
            ->where('video_id', $id)
            ->orderByDesc('id')
            ->first();

        $userWatch = $userWatchReport->watch_time ?? 0;
        $videoDurationSeconds = ($video->video_duration ?? 0) / 1000;

        $lastWatchTime = ($userWatchReport && $userWatchReport->status == 1)
            ? 0
            : (($userWatch >= $videoDurationSeconds) ? 0 : $userWatch);

        $nextVideo = null;
        $previousVideo = null;

       
            $nextVideo = Video::active()->select('video.*', 'language.name as language_name')
                ->join('language', 'language.id', '=', 'video.language_id')
                ->whereRaw("FIND_IN_SET(?, video.category_id)", [$categoryIds])
                ->where('order_by', '>', $video->order_by)
                ->orderBy('order_by', 'ASC')
                ->first();

            // ✅ If no next video, wrap around to the FIRST video
            if (!$nextVideo) {
                $nextVideo = Video::active()->select('video.*', 'language.name as language_name')
                        ->join('language', 'language.id', '=', 'video.language_id')
                    ->whereRaw("FIND_IN_SET(?, video.category_id)", [$categoryIds])
                    ->where('video.id', '!=', $video->id)
                    ->orderBy('order_by', 'ASC') // highest order_by = first added
                    ->first();
            }

            $previousVideo = Video::active()->select('video.*', 'language.name as language_name')
            ->join('language', 'language.id', '=', 'video.language_id')
                ->whereRaw("FIND_IN_SET(?, video.category_id)", [$categoryIds])
               
                ->where('order_by', '<', $video->order_by)
                ->orderBy('order_by', 'DESC')
                ->first();

            // ✅ If no previous video, wrap around to the LAST video
            if (!$previousVideo) {
                $previousVideo = Video::active()->select('video.*', 'language.name as language_name')
            ->join('language', 'language.id', '=', 'video.language_id')
                    ->whereRaw("FIND_IN_SET(?, video.category_id)", [$categoryIds])
                    
                    ->where('video.id', '!=', $video->id)
                    ->orderBy('order_by', 'DESC') // lowest order_by = last added
                    ->first();
            }

    
        // Encrypt IDs for safe URL usage
        $nextVideoId     = $nextVideo     ? \App\Helpers\VideoHelper::encryptID($nextVideo->id)     : null;
        $previousVideoId = $previousVideo ? \App\Helpers\VideoHelper::encryptID($previousVideo->id) : null;

        return view('view-detail-mood', compact(
            'lastWatchTime',
            'user_downloads',
            'video',
            'casts',
            'catListData',
            'nextVideo',        
            'previousVideo',    
            'nextVideoId',   'previousVideoId',
            'categoryIds'   ,'watchlist'
        ));
    }
}