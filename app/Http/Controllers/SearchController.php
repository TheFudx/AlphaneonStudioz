<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Video;
use App\Models\Watchlists;
use App\Models\Category;
use App\Models\Episodes;
use App\Models\Notification;
use App\Helpers\VideoHelper;

class SearchController extends Controller
{   
    
    public function search(Request $request)
    {
        $keyword = $request->input('keyword') ?? $request->get('keyword');
       
        $videos = Video::active()->where('name', 'like', "%{$keyword}%")->where('type_id', config('constant.TYPES.MOVIES'))->get();
        $trailers = Video::active()->where('name', 'like', "%{$keyword}%")->where('type_id', config('constant.TYPES.TRAILER'))->get();
        $music = Video::active()->where('name', 'like', "%{$keyword}%")->where('type_id', config('constant.TYPES.MUSIC'))->get();
        $podcasts = Video::active()->where('name', 'like', "%{$keyword}%")->where('type_id', config('constant.TYPES.PODCAST'))->get();
        $episodes = Episodes::where('name', 'like', "%{$keyword}%")->get();
        $watchlist = app('wishlist');
        
        
        session(['watchlist' => $watchlist]);
        if ($request->expectsJson()) {
            $results = [];
            // Add videos to results
            foreach ($videos as $video) {
                $results[] = [
                    'id' => VideoHelper::encryptID($video->id),
                    'name' => $video->name,
                    'description' => $video->description,
                    'type_id' => config('constant.TYPES.MOVIES'), // Type for videos
                    'status' => $video->status, // Include status if needed
                ];
            }
            
            // Add trailers to results
            foreach ($trailers as $trailer) {
                $results[] = [
                    'id' => VideoHelper::encryptID($trailer->id),
                    'name' => $trailer->name,
                    'description' => $trailer->description,
                    'type_id' => config('constant.TYPES.TRAILER'), // Type for trailers
                    'status' => $trailer->status, // Include status if needed
                ];
            }
            
            // Add music to results
            foreach ($music as $song) {
                $results[] = [
                    'id' => VideoHelper::encryptID($song->id),
                    'name' => $song->name,
                    'description' => $song->description,
                    'type_id' => config('constant.TYPES.MUSIC'), // Type for music
                    'status' => $song->status, // Include status if needed
                ];
            }
            
            foreach ($podcasts as $podcast) {
                $results[] = [
                    'id' => VideoHelper::encryptID($podcast->id),
                    'name' => $podcast->name,
                    'description' => $podcast->description,
                    'type_id' => config('constant.TYPES.PODCAST'), // Type for podcasts
                    'status' => $podcast->status, // Include status if needed
                ];
            }
            
            // Add episodes to results
            foreach ($episodes as $episode) {
                $results[] = [
                    'id' => VideoHelper::encryptID($episode->id),
                    'name' => $episode->name,
                    'season' => $episode->season_id,
                    'episode' => $episode->episode_no,
                    'description' => $episode->description,
                    'type_id' => config('constant.TYPES.SERIES'), // Type for episodes
                ];
            }
            
            // Return the results as JSON
            return response()->json($results);
            // If not, return the view
        };
        return view('search-result', [
           
            'video' => $videos,
            'trailers' => $trailers,
            'music' => $music,
            'podcast' => $podcasts,
            'episode' => $episodes,
           'watchlist' => $watchlist,
           
           
        ]);
    }
}
