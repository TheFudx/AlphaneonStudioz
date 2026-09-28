<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Video;
use App\Models\Episodes;
use App\Models\Notification;
use App\Models\Watchlists;
use App\Models\UserDownloads;
use App\Models\Cast;
use App\Helpers\SeoHelper;
use App\Models\Language;
use App\Helpers\VideoHelper;
use App\Models\UserWatchReport;
use App\Models\UserWatchLaterList;
use App\Models\Series;
use Str;

class WatchlistController extends Controller
{
    public function index(){
        // $watchlist = session()->get('watchlist', []);
        // session(['watchlist' => $watchlist]);
        $watchlist = UserWatchLaterList::where('user_id', auth()->id())->get();
        return view('watchlist-list', ['watchlist'=>$watchlist,]);
    }
    public function watchlistToggle(Request $request)
    {
        $videoId = $request->input('video_id');
        $typeId = $request->input('type_id');
        $categoryId = $request->input('category_id');
        $contentType = $request->input('type'); // 'video', 'series', or 'episode'
        
        $video = null;
        $seriesId = null;
        $episodeId = null;
        
        if ($contentType == 'series') {
            // video_id is series_id
            $seriesId = $videoId;
            
            // Get the first episode of this series
            $video = Episodes::where('series_id', $seriesId)
                            ->orderBy('episode_no', 'asc')
                            ->first();
            
            if ($video) {
                $episodeId = $video->id;
                $typeId = $typeId ?: $video->type_id;
                $categoryId = $categoryId ?: $video->category_id;
            }
        } elseif ($contentType == 'episode') {
            // video_id is episode_id
            $episodeId = $videoId;
            $video = Episodes::find($episodeId);
            
            if ($video) {
                $seriesId = $video->series_id;
                $typeId = $typeId ?: $video->type_id;
                $categoryId = $categoryId ?: $video->category_id;
            }
        } else {
            // Regular video
            $video = Video::find($videoId);
        }
        
        if (!$video) {
            return redirect()->back()->with('error', 'Content not found.');
        }
        
        // Build the query based on content type
        $watchlistQuery = UserWatchLaterList::where('user_id', auth()->id());
        
        if ($contentType == 'series') {
            // For series, check if ANY episode of this series is in watchlist
            // Or check specifically for the first episode
            $watchlistQuery->where('series_id', $seriesId);
        } elseif ($contentType == 'episode') {
            $watchlistQuery->where('episode_id', $episodeId);
        } else {
            $watchlistQuery->where('video_id', $videoId);
        }
        
        $watchlistQuery->where('type_id', $typeId)
                    ->where('category_id', $categoryId);
        
        $watchlist = $watchlistQuery->first();
        
        if ($watchlist) {
            $watchlist->delete();
            $message = $contentType == 'series' ? 'Series removed from watchlist successfully.' : 'Item removed from watchlist successfully.';
            return redirect()->back()->with('success', $message);
        } else {
            $data = [
                'user_id' => auth()->id(),
                'type_id' => $typeId,
                'category_id' => $categoryId,
                'date_time' => now(),
            ];
            
            if ($contentType == 'series') {
                $data['series_id'] = $seriesId;
                $data['episode_id'] = $episodeId; // Store the first episode
                $data['video_id'] = null;
            } elseif ($contentType == 'episode') {
                $data['episode_id'] = $episodeId;
                $data['series_id'] = $seriesId;
                $data['video_id'] = null;
            } else {
                $data['video_id'] = $videoId;
                $data['series_id'] = null;
                $data['episode_id'] = null;
            }
            
            UserWatchLaterList::create($data);
            $message = $contentType == 'series' ? 'Series added to watchlist successfully.' : 'Item added to watchlist successfully.';
            return redirect()->back()->with('success', $message);
        }
    }
    public function clearAllWatchlist()
    {
        if (auth()->check()) {
            UserWatchLaterList::where('user_id', auth()->id())->delete();
        }
        if (session()->has('watchlist')) {
            session()->forget('watchlist');
        }

        return response()->json([
            'success' => true,
            'message' => 'Watchlist cleared successfully'
        ]);
    }
    public function viewDetails($id, $type)
    {
        $id = \App\Helpers\VideoHelper::decryptID($id);

        if($type == 'episode'){
            $video = Episodes::select('episodes.*', 'language.name as language_name')
            ->join('language', 'language.id', '=', 'episodes.language_id')
            ->where('episodes.id', $id)
            ->firstOrFail();
        }else{
            $video = Video::active()
                ->select('video.*', 'language.name as language_name')
                ->join('language', 'language.id', '=', 'video.language_id')
                ->where('video.id', $id)->where('video.status', 1)
                ->firstOrFail();
        }
        // Increment view
        $video->increment('view');
        // Cast & Category
        $castIds = !empty($video->cast_id) ? explode(',', $video->cast_id) : [];
        $catlist = !empty($video->category_id) ? explode(',', $video->category_id) : [];

        $casts = Cast::whereIn('id', $castIds)->get();
        $catListData = Category::whereIn('id', $catlist)->get();

        $watchlist = app('wishlist');

        // Watch resume logic
        if($type == 'episode'){
            $userWatchReport = UserWatchReport::where('user_id', auth()->id())
                ->where('episode_id', $id)->where('category_id', $video->category_id)->where('type_id', $video->type_id)
                ->orderByDesc('id')
                ->first();

        }else{

            $userWatchReport = UserWatchReport::where('user_id', auth()->id())
                ->where('video_id', $id)->where('category_id', $video->category_id)->where('type_id', $video->type_id)
                ->orderByDesc('id')
                ->first();
        }

        $userWatch = $userWatchReport->watch_time ?? 0;
        $videoDurationSeconds = ($video->video_duration ?? 0) / 1000;

        $lastWatchTime = ($userWatchReport && $userWatchReport->status == 1)
            ? 0
            : (($userWatch >= $videoDurationSeconds) ? 0 : $userWatch);
        // ======================================================
        // CURRENT WATCHLIST ITEM
        // ======================================================

        $currentWatchlist = UserWatchLaterList::where('user_id', auth()->id())
            ->where(function ($query) use ($id, $type) {

                // NORMAL VIDEO
                if ($type == 'video') {
                    $query->where('video_id', $id);
                }

                // EPISODE
                elseif ($type == 'episode') {
                    $query->where('episode_id', $id);
                }
            })
            ->first();

        $nextVideo = null;
        $previousVideo = null;
       

        if ($currentWatchlist) {

            // ======================================================
            // NEXT ITEM
            // ======================================================

            $nextWatchlist = UserWatchLaterList::where('user_id', auth()->id())
                ->where('id', '>', $currentWatchlist->id)
                ->orderBy('id', 'ASC')
                ->first();

            // LOOP TO FIRST
            if (!$nextWatchlist) {
                $nextWatchlist = UserWatchLaterList::where('user_id', auth()->id())
                    ->orderBy('id', 'ASC')
                    ->first();
            }

            // ======================================================
            // PREVIOUS ITEM
            // ======================================================

            $previousWatchlist = UserWatchLaterList::where('user_id', auth()->id())
                ->where('id', '<', $currentWatchlist->id)
                ->orderBy('id', 'DESC')
                ->first();

            // LOOP TO LAST
            if (!$previousWatchlist) {
                $previousWatchlist = UserWatchLaterList::where('user_id', auth()->id())
                    ->orderBy('id', 'DESC')
                    ->first();
            }

            // ======================================================
            // LOAD NEXT CONTENT
            // ======================================================

            if ($nextWatchlist) {

                // VIDEO
                if (!empty($nextWatchlist->video_id)) {

                    $nextVideo = Video::active()
                        ->where('id', $nextWatchlist->video_id)
                        ->first();

                    $nextType = 'video';
                }

                // EPISODE
                elseif (!empty($nextWatchlist->episode_id)) {

                    $nextVideo = Episodes::where('id', $nextWatchlist->episode_id)
                        ->first();

                    $nextType = 'episode';
                }
            }

            // ======================================================
            // LOAD PREVIOUS CONTENT
            // ======================================================

            if ($previousWatchlist) {

                // VIDEO
                if (!empty($previousWatchlist->video_id)) {

                    $previousVideo = Video::active()
                        ->where('id', $previousWatchlist->video_id)
                        ->first();

                    $previousType = 'video';
                }

                // EPISODE
                elseif (!empty($previousWatchlist->episode_id)) {

                    $previousVideo = Episodes::where('id', $previousWatchlist->episode_id)
                        ->first();

                    $previousType = 'episode';
                }
            }
        }

        // ======================================================
        // ENCRYPT IDS
        // ======================================================

        $nextVideoId = null;
        $previousVideoId = null;

        if ($nextVideo) {
            $nextVideoId = \App\Helpers\VideoHelper::encryptID($nextVideo->id);
        }

        if ($previousVideo) {
            $previousVideoId = \App\Helpers\VideoHelper::encryptID($previousVideo->id);
        }
        return view('watchlist-view-details', compact(
            'video',
            'casts','catListData',
            'lastWatchTime','currentWatchlist',
            'nextVideoId','nextType','previousType','type',
            'previousVideoId','previousVideo','nextVideo','watchlist'
        ));
    }
    public function watchlistRemove(Request $request){
        $id = $request->input('id');
        $watchlistItem = UserWatchLaterList::where('user_id', auth()->id())->where('id', $id)->first();

        if ($watchlistItem) {
            $watchlistItem->delete();
            $watchlist = UserWatchLaterList::where('user_id', auth()->id())->get();
            return redirect()->route('watchlist.list', ['watchlist'=>$watchlist,]);
        } 
    }


    public function addToWatchlist(Request $request)
    {
        $videoId = $request->input('video_id');
        $type = $request->input('type');
        $watchlist = session()->get('watchlist', []);
    
        $item = ['id'=> $videoId, 'type' => $type ];
    
        // Prevent duplicates by searching for the exact match in watchlist
        foreach ($watchlist as $existingItem) {
            if ($existingItem['id'] === $item['id'] && $existingItem['type'] === $item['type']) {
                return redirect()->back()->with('error', 'This item is already in your watchlist.');
            }
        }
    
        // Add the new item to the watchlist array
        $watchlist[] = $item;
    
        // Update the session with the modified watchlist
        session(['watchlist' => $watchlist]);
    
        return redirect()->back()->with('success', 'Item added to watchlist successfully.');
    }
    public function removeFromWatchlist(Request $request)
    {
        $videoId = $request->input('video_id');
        $type = $request->input('type');
        $watchlist = session()->get('watchlist', []);
        $itemToRemove = ['id'=> $videoId, 'type' => $type ];
       
        // Remove the item from the watchlist
        foreach ($watchlist as $key => $existingItem) {
            if ($existingItem['id'] === $itemToRemove['id'] && $existingItem['type'] === $itemToRemove['type']) {
                unset($watchlist[$key]);
                session(['watchlist' => array_values($watchlist)]);  // Reset array keys after removal
                return redirect()->back()->with('success', 'Item removed from watchlist successfully.');
            }
        }
    
        return redirect()->back()->with('error', 'Item not found in watchlist.');
    }
    public function clearWatchlist()
    {
        if (auth()->check()) {
            UserWatchLaterList::where('user_id', auth()->id())->delete();
        }
        if (session()->has('watchlist')) {
            session()->forget('watchlist');
        }

        return response()->json([
            'success' => true,
            'message' => 'Watchlist cleared successfully.'
        ]);
    }

    /**
     * Get resolved Watch Later items for a user formatted for display in cards.
     *
     * @param int|null $userId
     * @return \Illuminate\Support\Collection
     */
    public static function getWatchlistItems($userId = null)
    {
        $userId = $userId ?: auth()->id();
        if (!$userId) {
            return collect();
        }

        $watchlist = UserWatchLaterList::where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->get();

        $items = [];
        foreach ($watchlist as $w) {
            $item = null;
            $type = null;

            if (!empty($w->video_id)) {
                $item = Video::active()
                    ->select('video.*', 'language.name as language_name')
                    ->leftJoin('language', 'language.id', '=', 'video.language_id')
                    ->where('video.id', $w->video_id)
                    ->first();
                $type = 'video';
            } elseif (!empty($w->episode_id)) {
                $item = Episodes::select('episodes.*', 'language.name as language_name')
                    ->leftJoin('language', 'language.id', '=', 'episodes.language_id')
                    ->where('episodes.id', $w->episode_id)
                    ->first();
                $type = 'episode';
            } elseif (!empty($w->series_id)) {
                $item = Episodes::select('episodes.*', 'language.name as language_name')
                    ->leftJoin('language', 'language.id', '=', 'episodes.language_id')
                    ->where('episodes.series_id', $w->series_id)
                    ->orderBy('episode_no', 'asc')
                    ->first();
                $type = 'episode';
            }

            if ($item) {
                $isSeries = ($type === 'episode');
                $detailRoute = route('watchlist.view.details', [
                    'id' => VideoHelper::encryptID($item->id),
                    'type' => $type,
                ]);

                $imagePath = $isSeries
                    ? config('app.website_url') . 'admin_panel/public/images/series/'
                    : config('app.website_url') . 'admin_panel/public/images/video/';
                $image = $item->landscape ?: ($item->thumbnail ?: '');

                $seriesName = null;
                if ($isSeries && !empty($item->series_id)) {
                    $series = Series::find($item->series_id);
                    $seriesName = $series ? $series->name : null;
                }

                $items[] = [
                    'id' => $item->id,
                    'watch_later_id' => $w->id,
                    'type_id' => $item->type_id ?? $w->type_id ?? ($isSeries ? 10 : 1),
                    'category_id' => $item->category_id ?? $w->category_id ?? '',
                    'type' => $type,
                    'title' => $item->name ?: ($item->title ?: ''),
                    'series_name' => $seriesName,
                    'season' => $item->season_id ?? null,
                    'episode_num' => $item->episode_no ?? null,
                    'image_url' => $imagePath . $image,
                    'redirect_url' => $detailRoute,
                    'trailer_url' => $item->trailer_url ?? null,
                    'release_date' => $item->release_date ?? null,
                    'duration' => $isSeries ? ($item->episode_duration ?? 0) : ($item->video_duration ?? 0),
                    'language_name' => $item->language_name ?? '',
                    'description' => $item->description ?? '',
                ];
            }
        }

        return collect($items);
    }
}
