<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Video;
use App\Models\Notification;
use App\Models\Watchlists;
use App\Models\UserDownloads;
use App\Models\Cast;
use App\Helpers\VideoHelper;
use App\Helpers\SeoHelper;
use App\Models\Language;
use App\Models\UserWatchReport;
use Str;

class ViewDetailsController extends Controller
{
    //

    public function index($id)
    {
        $id = \App\Helpers\VideoHelper::decryptID($id);

        $video = Video::active()
            ->select('video.*', 'language.name as language_name')
            ->join('language', 'language.id', '=', 'video.language_id')
            ->where('video.id', $id)->where('video.status', 1)
            ->firstOrFail();

        // Increment view
        $video->increment('view');
        SeoHelper::setMeta(
            $video->name,
            Str::limit(strip_tags($video->description), 160),
            $video->cast_id,
            $video->landscape_url,
            url()->current()
        );
        $movieData = Video::active()
            ->where('type_id', config('constant.TYPES.MOVIES'))
            ->select('video.*', 'language.name as language_name')
            ->join('language', 'language.id', '=', 'video.language_id')
            ->where('video.id', '!=', $id)->orderbyDesc('order_by')->where('video.status', 1)
            ->limit(12)
            ->get();

        // Cast & Category
        $castIds = !empty($video->cast_id) ? explode(',', $video->cast_id) : [];
        $catlist = !empty($video->category_id) ? explode(',', $video->category_id) : [];

        $casts = Cast::whereIn('id', $castIds)->get();
        $catListData = Category::whereIn('id', $catlist)->get();

        // Podcast
        $poadcastData = Video::active()
            ->select('video.*', 'language.name as language_name')
            ->join('language', 'language.id', '=', 'video.language_id')
            ->whereRaw('FIND_IN_SET(?, video.category_id)', [27])
            ->where('video.type_id', config('constant.TYPES.PODCAST'))->where('video.status', 1)
            ->where('video.id', '!=', $id)->where('video.language_id', 1)->orderbyDesc('order_by')
            ->limit(12)
            ->get();
        $marathipoadcastData = Video::active()
            ->select('video.*', 'language.name as language_name')
            ->join('language', 'language.id', '=', 'video.language_id')
            ->whereRaw('FIND_IN_SET(?, video.category_id)', [27])
            ->where('video.type_id', config('constant.TYPES.PODCAST'))->where('video.status', 1)
            ->where('video.id', '!=', $id)->where('video.language_id', 2)->orderbyDesc('order_by')
            ->limit(12)
            ->get();

        // Shortfilms
        $shortfilmsData = Video::active()
            ->select('video.*', 'language.name as language_name')
            ->join('language', 'language.id', '=', 'video.language_id')
            ->where('type_id', config('constant.TYPES.SHORTFILMS'))->where('video.status', 1)
            ->where('video.id', '!=', $id)->orderbyDesc('order_by')
            ->get();

        // Trailers
        $trailerData = Video::active()
            ->select('video.*', 'language.name as language_name')
            ->join('language', 'language.id', '=', 'video.language_id')
            ->where('video.type_id', config('constant.TYPES.TRAILER'))->where('video.status', 1)
            ->where('video.id', '!=', $id)->orderbyDesc('order_by')
            ->get();

        // Music
        $musicData = Video::active()
            ->select('video.*', 'language.name as language_name')
            ->join('language', 'language.id', '=', 'video.language_id')
            ->where('category_id', 20)
            ->where('type_id', config('constant.TYPES.MUSIC'))->where('video.status', 1)
            ->where('video.id', '!=', $id)->orderbyDesc('order_by')
            ->limit(12)
            ->get();
        $comedyCategories = [16, 28]; // Add more if needed in future
        $videoCategories = !empty($video->category_id) 
            ? array_map('trim', explode(',', $video->category_id)) 
            : [];

        // Step 2: Find the matching comedy category for this video
        $currentComedyCategory = null;
        foreach ($comedyCategories as $cat) {
            if (in_array($cat, $videoCategories)) {
                $currentComedyCategory = $cat;
                break;
            }
        }

        // Step 3: Get seasons ONLY for this specific comedy show category
        $seasons = collect(); // empty default
        if ($currentComedyCategory) {
            $seasons = Video::where('type_id', config('constant.TYPES.COMEDYSHOW'))
                ->where('status', 1)
                ->whereRaw('FIND_IN_SET(?, category_id)', [$currentComedyCategory]) // ✅ only this show's episodes
                ->orderBy('season_no')
                ->orderBy('episode_no')
                ->get()
                ->groupBy('season_no');
        }

        // Step 4: Get related comedy data for same category only
        $comdeyData = collect(); // empty default
        // if ($currentComedyCategory) {
            $comdeyData = Video::active()
                ->where('type_id', config('constant.TYPES.COMEDYSHOW'))
                ->where('is_banner', 1)
                ->where('status', 1)
                ->where('id', '!=', $id)
                // ->whereRaw('FIND_IN_SET(?, category_id)', [$currentComedyCategory]) // ✅ same show only
                ->with('language')
                ->orderByDesc('order_by')
                ->limit(12)
                ->get();
        // }
        $currentSeasonNo = $video->season_no ?? null;
        $user_downloads = UserDownloads::where('user_id', auth()->id())
            ->where('video_id', $id)
            ->first();

        $watchlist = app('wishlist');
        session(['watchlist' => $watchlist]);

       

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

        if ($video->type_id != 8) {
            $nextVideo = Video::active()
                ->where('type_id', $video->type_id)
                ->where('language_id', $video->language_id)
                ->where('order_by', '>', $video->order_by)
                ->orderBy('order_by', 'ASC')
                ->first();

            // ✅ If no next video, wrap around to the FIRST video
            if (!$nextVideo) {
                $nextVideo = Video::active()
                    ->where('type_id', $video->type_id)
                    ->where('language_id', $video->language_id)
                    ->where('id', '!=', $video->id)
                    ->orderBy('order_by', 'ASC') // highest order_by = first added
                    ->first();
            }

            $previousVideo = Video::active()
                ->where('type_id', $video->type_id)
                ->where('language_id', $video->language_id)
                ->where('order_by', '<', $video->order_by)
                ->orderBy('order_by', 'DESC')
                ->first();

            // ✅ If no previous video, wrap around to the LAST video
            if (!$previousVideo) {
                $previousVideo = Video::active()
                    ->where('type_id', $video->type_id)
                    ->where('language_id', $video->language_id)
                    ->where('id', '!=', $video->id)
                    ->orderBy('order_by', 'DESC') // lowest order_by = last added
                    ->first();
            }

        } else {
            // type_id = 8
            $nextVideo = Video::active()
                ->where('type_id', 8)
                ->where('order_by', '>', $video->order_by)
                ->orderBy('order_by', 'ASC')
                ->first();

            // ✅ Wrap to first video of type 8
            if (!$nextVideo) {
                $nextVideo = Video::active()
                    ->where('type_id', 8)
                    ->where('id', '!=', $video->id)
                    ->orderBy('order_by', 'ASC')
                    ->first();
            }

            $previousVideo = Video::active()
                ->where('type_id', 8)
                ->where('order_by', '<', $video->order_by)
                ->orderBy('order_by', 'DESC')
                ->first();

            // ✅ Wrap to last video of type 8
            if (!$previousVideo) {
                $previousVideo = Video::active()
                    ->where('type_id', 8)
                    ->where('id', '!=', $video->id)
                    ->orderBy('order_by', 'DESC')
                    ->first();
            }
        }

        // Encrypt IDs for safe URL usage
        $nextVideoId     = $nextVideo     ? \App\Helpers\VideoHelper::encryptID($nextVideo->id)     : null;
        $previousVideoId = $previousVideo ? \App\Helpers\VideoHelper::encryptID($previousVideo->id) : null;

        return view('view-detail', compact(
            'lastWatchTime',
            'user_downloads',
            'video',
            'poadcastData',
            'marathipoadcastData',
            'musicData',
            'shortfilmsData',
            'trailerData',
            'casts',
            'movieData',
            'catListData',
            'watchlist','comdeyData','seasons',
            'nextVideo',        
            'previousVideo',    
            'nextVideoId',      
            'previousVideoId','currentSeasonNo'
        ));
    }

    public function indexTest($id)
    {
        $id = \App\Helpers\VideoHelper::decryptID($id);
        $video = Video::active()->select('video.*', 'language.name as language_name')
            ->join('language', 'language.id', '=', 'video.language_id')
            ->where('video.id', $id)
            ->first();

        $movieData = Video::active()->where('type_id', config('constant.TYPES.MOVIES'))
            ->select('video.*', 'language.name as language_name')
            ->join('language', 'language.id', '=', 'video.language_id')
            ->where('video.id', '!=', $id)->limit(12)->get();

        if ($video) {
            $video->increment('view');
        }
        // Explode the comma-separated cast IDs
        $castIds = explode(',', $video->cast_id);
        $catlist = explode(',', $video->category_id);
        // Fetch the cast details for each ID
        $casts = Cast::whereIn('id', $castIds)->get();
        $catListData = Category::whereIn('id', $catlist)->get();

        $poadcastData = Video::active()->select('video.*', 'language.name as language_name')
            ->join('language', 'language.id', '=', 'video.language_id')
            ->whereRaw('FIND_IN_SET(?, video.category_id)', [27])
            ->where('video.type_id', config('constant.TYPES.PODCAST'))->where('video.id', '!=', $id)
            ->limit(12)
            ->get();

        $shortfilmsData = Video::active()->select('video.*', 'language.name as language_name')
            ->join('language', 'language.id', '=', 'video.language_id')
            ->where('type_id', config('constant.TYPES.SHORTFILMS'))->where('video.id', '!=', $id)
            ->get();

        $trailerData = Video::active()->where('video.type_id', config('constant.TYPES.TRAILER'))
            ->select('video.*', 'language.name as language_name')
            ->join('language', 'language.id', '=', 'video.language_id')->where('video.id', '!=', $id)->get();


        $musicData = Video::active()->select('video.*', 'language.name as language_name')
            ->join('language', 'language.id', '=', 'video.language_id')
            ->where('category_id', 20)
            ->where('type_id', config('constant.TYPES.MUSIC'))->where('video.id', '!=', $id)
            ->limit(12)
            ->get();
        $comdeyData = Video::active()
            ->where('type_id', config('constant.TYPES.COMEDYSHOW'))
            ->whereRaw("FIND_IN_SET(16, category_id)")->where('is_banner',1)->where('video.id', '!=', $id)
            ->with('language')
            ->orderByDesc('order_by')
            ->limit(12)
            ->get();

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
        
        return view("view-detail-download-test", ['user_downloads' => $user_downloads, 'video' => $video, 'poadcastData' => $poadcastData, 'musicData' => $musicData, 'shortfilmsData' => $shortfilmsData, 'trailerData' => $trailerData, 'casts' => $casts, 'movieData' => $movieData, 'catListData' => $catListData, 'watchlist' => $watchlist, 'comdeyData' => $comdeyData]);
    }

    public function slugAndContentDetails($content, $slug)
    {
        // for movies, shortfilms, music-videos and trailers
        if($content == 'movies'){
            $type_id = config('constant.TYPES.MOVIES');
        }elseif($content == 'shortfilms'){
            $type_id = config('constant.TYPES.SHORTFILMS');
        }elseif($content == 'musics'){
            $type_id = config('constant.TYPES.MUSIC');
        }elseif($content == 'trailers'){
            $type_id = config('constant.TYPES.TRAILER');
        }elseif($content == 'podcasts'){
            $type_id = config('constant.TYPES.PODCAST');
        }elseif($content == 'comedy-shows'){
            $type_id = config('constant.TYPES.COMEDYSHOW');
        }else{
            abort(404);
        }
        $video = Video::active()
            ->select('video.*', 'language.name as language_name')
            ->join('language', 'language.id', '=', 'video.language_id')
            ->where('video.slug', $slug)
            ->where('video.type_id', $type_id)
            ->firstOrFail();

        // Increment view
        $video->increment('view');

        $previousVideo = Video::active()
            ->where('type_id', $type_id)
            ->where('order_by', '<', $video->order_by)
            ->orderBy('order_by', 'DESC')
            ->first();

        $nextVideo = Video::active()
            ->where('type_id', $type_id)
            ->where('order_by', '>', $video->order_by)
            ->orderBy('order_by', 'ASC')
            ->first();
        // Encrypt IDs for safe URL usage
        $nextVideoId     = $nextVideo     ? \App\Helpers\VideoHelper::encryptID($nextVideo->id)     : null;
        $previousVideoId = $previousVideo ? \App\Helpers\VideoHelper::encryptID($previousVideo->id) : null;
        SeoHelper::setMeta(
            $video->name,
            Str::limit(strip_tags($video->description), 160),
            $video->cast_id,
            $video->landscape_url,
            url()->current()
        );
         // Cast & Category
        $castIds = !empty($video->cast_id) ? explode(',', $video->cast_id) : [];
        $catlist = !empty($video->category_id) ? explode(',', $video->category_id) : [];

        $casts = Cast::whereIn('id', $castIds)->get();
        $catListData = Category::whereIn('id', $catlist)->get();

        $watchlist = app('wishlist');
        session(['watchlist' => $watchlist]);

       

        // Watch resume logic
        $userWatchReport = UserWatchReport::where('user_id', auth()->id())
            ->where('video_id', $video->id)
            ->orderByDesc('id')
            ->first();

        $userWatch = $userWatchReport->watch_time ?? 0;
        $videoDurationSeconds = ($video->video_duration ?? 0) / 1000;

        $lastWatchTime = ($userWatchReport && $userWatchReport->status == 1)
            ? 0
            : (($userWatch >= $videoDurationSeconds) ? 0 : $userWatch);

        $morelikethisData = Video::active()
            ->select('video.*', 'language.name as language_name')
            ->join('language', 'language.id', '=', 'video.language_id')
            ->where('video.type_id', $type_id)
            ->where('video.id', '!=', $video->id)
            ->orderbyDesc('order_by')
            ->limit(12)
            ->get();

        return view('view-detail-slug', compact('casts','watchlist','videoDurationSeconds','lastWatchTime','catListData','video','content','slug','morelikethisData', 'previousVideo', 'nextVideo','nextVideoId','previousVideoId'));
    }
}
