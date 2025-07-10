<?php
namespace App\Http\Controllers;
use App\Models\Category;
use App\Models\Cast;
use App\Models\Series;
use App\Models\Season;
use App\Models\Episodes;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\Watchlists;
use App\Helpers\VideoHelper;
class WebSeriesController extends Controller
{
    public function comingSoonPage(){
        
        
        
        return view('coming-soon', [ ]);
    }
    public function webseriesList(){
        
        $series = Series::all();
        
        $watchlist = app('wishlist');
        session(['watchlist' => $watchlist]); 
        // $series = $seriesdata->unique('name');
        return view('webseries-list',[  'series'=> $series,  'watchlist' => $watchlist]);
    }

    private function getEpisodeViewData(Episodes $video)
    {
        $castIds = explode(',', $video->cast_id ?? '');
        $catlist = explode(',', $video->category_id ?? '');

        $casts = Cast::whereIn('id', $castIds)->get();
        $catListData = Category::whereIn('id', $catlist)->get();
        $watchlist = app('wishlist');
        session(['watchlist' => $watchlist]);

        $vidData = Episodes::where('series_id', $video->series_id)->orderBy('episode_no')->get();
        $seriesdata = Season::with('episodes')->where('series_id', $video->series_id)->get();
        $nextEpisode = Episodes::where('series_id', $video->series_id)
            ->where('season_id', $video->season_id)
            ->where('episode_no', '>', $video->episode_no)
            ->where('status', 1)
            ->orderBy('episode_no')
            ->first();
        $previousEpisode = Episodes::where('series_id', $video->series_id)
            ->where('season_id', $video->season_id)
            ->where('episode_no', '<', $video->episode_no)
            ->orderBy('episode_no', 'desc')
            ->first();
        $series = Series::find($video->series_id);

        return compact('video', 'casts', 'catListData', 'watchlist', 'vidData', 'seriesdata', 'nextEpisode', 'previousEpisode', 'series');
    }


    public function index($id)
    {
        $id = VideoHelper::decryptID($id);
        $video = Episodes::select('episodes.*', 'language.name as language_name')
            ->join('language', 'language.id', '=', 'episodes.language_id')
            ->where('episodes.series_id', $id)
            ->first();

        if (!$video) {
            abort(404);
        }

        $viewData = $this->getEpisodeViewData($video);
        $viewData['allSeries'] = Series::where('status', 1)->where('id', '!=', $video->series_id)->get();
        $viewData['seasons'] = Series::where('id', $id)->select('season')->distinct()->get();

        return view("webseries-view", $viewData);
    }

    public function webseriesEpisodeView($id)
    {
        $id = VideoHelper::decryptID($id);
        $video = Episodes::select('episodes.*', 'language.name as language_name')
            ->join('language', 'language.id', '=', 'episodes.language_id')
            ->where('episodes.id', $id)
            ->first();

        if (!$video) {
            abort(404);
        }

        $viewData = $this->getEpisodeViewData($video);
        $viewData['allSeries'] = Series::where('status', 1)->where('id', '!=', $video->series_id)->get();
        $viewData['seasons'] = Series::where('id', $video->series_id)->select('season')->distinct()->get();

        return view("webseries-episode-view", $viewData);
    }
}
