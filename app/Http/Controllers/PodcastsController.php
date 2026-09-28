<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Video;
use App\Models\Cast;
use App\Models\Category;
use App\Models\Notification;
use App\Models\Language;
use App\Models\Watchlists;
use App\Models\UserDownloads;
use App\Helpers\VideoHelper;
use App\Helpers\SeoHelper;
use App\Models\UserWatchReport;
use Str;

class PodcastsController extends Controller
{
    
    public function podcastsList(Request $request)
    {
        $categoryId = $request->get('category', 'All');
        $languageId = $request->get('language');

        $catlist = Category::join('video', 'video.category_id', '=', 'category.id')
            ->select('category.name as category_name','category.id as cat_id')
            ->distinct()
            ->where('video.type_id', config('constant.TYPES.PODCAST'))
            ->get();

        $videoQuery = Video::active()
            ->select('video.*', 'language.name as language_name')
            ->join('language', 'language.id', '=', 'video.language_id')
            ->where('video.type_id', config('constant.TYPES.PODCAST'));

        if ($categoryId != 'All') {
            $videoQuery->whereRaw("FIND_IN_SET(?, video.category_id)", [$categoryId]);
        }

        if (!empty($languageId)) {
            $videoQuery->where('video.language_id', $languageId);
        }

        $video = $videoQuery->whereNotNull('video.landscape')
            ->where('video.landscape', '!=', '')
            ->orderBy('order_by', 'desc')
            ->paginate(24)
            ->appends([
                'category' => $categoryId,
                'language' => $languageId
            ]);

        $language = Language::where('language.status', 1)
        ->join('video', 'video.language_id', '=', 'language.id')
        ->where('video.type_id', config('constant.TYPES.PODCAST'))
        ->select('language.*')
        ->distinct()
        ->get();

        $watchlist = app('wishlist');

        return view('podcast-list',[
            'catlist'=>$catlist,
            'video'=>$video,
            'language'=>$language,
            'watchlist'=>$watchlist,
            'selectedCategory'=>$categoryId,
            'selectedLanguage'=>$languageId
        ]);
    }
    public function marathipodcastsList(){
        
        $catlist = Category::join('video', 'video.category_id', '=', 'category.id')
        ->select('category.name as category_name','category.id as cat_id')
        ->distinct()
        ->where('video.type_id','=',config('constant.TYPES.PODCAST'))->where('video.language_id',2)->get();
        
        $video = Video::active()->select('video.*', 'language.name as language_name')
        ->join('language', 'language.id', '=', 'video.language_id')
        ->where('video.type_id', config('constant.TYPES.PODCAST'))->where('video.language_id',2) // Specify the table for `type_id`
        ->where('video.language_id', 2) // Specify the table for `language_id`
        ->orderBy('created_at', 'desc')->paginate(config('constant.PER_PAGE')); 
        
        $watchlist = app('wishlist');
        session(['watchlist' => $watchlist]);
        return view('podcast-list',[ 'catlist'=>$catlist, 'video'=> $video,  'watchlist' => $watchlist]);
    }

    public function podcastDetails($slug)
    {
        $video = Video::active()
            ->select('video.*', 'language.name as language_name')
            ->join('language', 'language.id', '=', 'video.language_id')
            ->where('video.slug', $slug)->where('video.status', 1)
            ->firstOrFail();

        // Increment view
        $video->increment('view');

       
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
            ->where('video.slug', '!=', $slug)->where('video.language_id', 1)->orderbyDesc('order_by')
            ->limit(12)
            ->get();
        $marathipoadcastData = Video::active()
            ->select('video.*', 'language.name as language_name')
            ->join('language', 'language.id', '=', 'video.language_id')
            ->whereRaw('FIND_IN_SET(?, video.category_id)', [27])
            ->where('video.type_id', config('constant.TYPES.PODCAST'))->where('video.status', 1)
            ->where('video.slug', '!=', $slug)->where('video.language_id', 2)->orderbyDesc('order_by')
            ->limit(12)
            ->get();

        
        $videoCategories = !empty($video->category_id) 
            ? array_map('trim', explode(',', $video->category_id)) 
            : [];

       
       
        $user_downloads = UserDownloads::where('user_id', auth()->id())
            ->where('video_id', $video->id)
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
            ->where('video_id', $video->id)
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
        $nextVideoId     = $nextVideo     ? $nextVideo->slug     : null;
        $previousVideoId = $previousVideo ? $previousVideo->slug : null;

        $language = $video->language_id == 2 ? 'marathi' : 'hindi';

        return view('view-detail-podcast', compact(
            'lastWatchTime',
            'user_downloads',
            'video',
            'poadcastData',
            'marathipoadcastData',
            'casts',
            'catListData',
            'watchlist',
            'nextVideo',        
            'previousVideo',    
            'nextVideoId',      
            'previousVideoId','slug','language'
        ));
    }
    public function MarathipodcastDetails($slug)
    {
        $video = Video::active()
            ->select('video.*', 'language.name as language_name')
            ->join('language', 'language.id', '=', 'video.language_id')
            ->where('video.slug', $slug)->where('video.language_id', 2)->where('video.status', 1)
            ->firstOrFail();

        // Increment view
        $video->increment('view');

       
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
            ->where('video.slug', '!=', $slug)->where('video.language_id', 1)->orderbyDesc('order_by')
            ->limit(12)
            ->get();
        $marathipoadcastData = Video::active()
            ->select('video.*', 'language.name as language_name')
            ->join('language', 'language.id', '=', 'video.language_id')
            ->whereRaw('FIND_IN_SET(?, video.category_id)', [27])
            ->where('video.type_id', config('constant.TYPES.PODCAST'))->where('video.status', 1)
            ->where('video.slug', '!=', $slug)->where('video.language_id', 2)->orderbyDesc('order_by')
            ->limit(12)
            ->get();

        
        $videoCategories = !empty($video->category_id) 
            ? array_map('trim', explode(',', $video->category_id)) 
            : [];

       
       
        $user_downloads = UserDownloads::where('user_id', auth()->id())
            ->where('video_id', $video->id)
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
            ->where('video_id', $video->id)
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
        $language = $video->language_id == 2 ? 'marathi' : 'hindi';
        return view('view-detail-podcast', compact(
            'lastWatchTime',
            'user_downloads',
            'video',
            'poadcastData',
            'marathipoadcastData',
            'casts',
            'catListData',
            'watchlist',
            'nextVideo',        
            'previousVideo',    
            'nextVideoId',      
            'previousVideoId','slug','language'
        ));
    }
        
}
