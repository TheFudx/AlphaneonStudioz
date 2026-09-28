<?php
namespace App\Http\Controllers;
use App\Models\Category;
use App\Models\Cast;
use App\Models\Series;
use App\Models\Season;
use App\Models\Episodes;
use App\Models\UserWatchReport;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\Watchlists;
use App\Helpers\VideoHelper;
use App\Helpers\SeoHelper;
use Str;

class WebSeriesController extends Controller
{
    public function comingSoonPage()
    {
        return view('coming-soon', [ ]);
    }

    public function webseriesList()
    {
        
        $series = Episodes::with([
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

        $vidData = Episodes::where('series_id', $video->series_id)
            ->where('status', 1)
            ->orderBy('season_id')
            ->orderBy('episode_no')
            ->get();

        $seriesdata = Season::with('episodes')
            ->where('series_id', $video->series_id)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | NEXT EPISODE LOGIC
        |--------------------------------------------------------------------------
        */

        // STEP 1:
        // Next episode in same season
        $nextEpisode = Episodes::where('series_id', $video->series_id)
            ->where('season_id', $video->season_id)
            ->where('episode_no', '>', $video->episode_no)
            ->where('status', 1)
            ->orderBy('episode_no')
            ->first();

        // STEP 2:
        // If current season completed,
        // go to next season first episode
        if (!$nextEpisode) {

            $nextSeason = Season::where('series_id', $video->series_id)
                ->where('id', '>', $video->season_id)
                ->orderBy('id')
                ->first();

            if ($nextSeason) {

                $nextEpisode = Episodes::where('series_id', $video->series_id)
                    ->where('season_id', $nextSeason->id)
                    ->where('status', 1)
                    ->orderBy('episode_no')
                    ->first();
            }
        }

        // STEP 3:
        // If all seasons completed,
        // go to next series first season first episode
        if (!$nextEpisode) {

            $nextSeries = Series::where('id', '>', $video->series_id)
                ->where('status', 1)
                ->orderBy('id')
                ->first();

            if ($nextSeries) {

                // First season of next series
                $firstSeason = Season::where('series_id', $nextSeries->id)
                    ->orderBy('id')
                    ->first();

                if ($firstSeason) {

                    // First episode of first season
                    $nextEpisode = Episodes::where('series_id', $nextSeries->id)
                        ->where('season_id', $firstSeason->id)
                        ->where('status', 1)
                        ->orderBy('episode_no')
                        ->first();
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | PREVIOUS EPISODE LOGIC
        |--------------------------------------------------------------------------
        */

        // Previous episode in same season
        $previousEpisode = Episodes::where('series_id', $video->series_id)
            ->where('season_id', $video->season_id)
            ->where('episode_no', '<', $video->episode_no)
            ->where('status', 1)
            ->orderBy('episode_no', 'desc')
            ->first();

        // If no previous episode,
        // go to previous season last episode
        if (!$previousEpisode) {

            $previousSeason = Season::where('series_id', $video->series_id)
                ->where('id', '<', $video->season_id)
                ->orderBy('id', 'desc')
                ->first();

            if ($previousSeason) {

                $previousEpisode = Episodes::where('series_id', $video->series_id)
                    ->where('season_id', $previousSeason->id)
                    ->where('status', 1)
                    ->orderBy('episode_no', 'desc')
                    ->first();
            }
        }

        $series = Series::find($video->series_id);

        // Generate URLs for previous and next episodes
        $previousEpisodeUrl = $previousEpisode ? route('webseries.episode.view.by.slug', [
            'slug' => $previousEpisode->series->slug ?? Str::slug($previousEpisode->series->name),
            'season' => $previousEpisode->series->season_no ?? $previousEpisode->season_id,
            'episode' => $previousEpisode->episode_no
        ]) : null;

        $nextEpisodeUrl = $nextEpisode ? route('webseries.episode.view.by.slug', [
            'slug' => $nextEpisode->series->slug ?? Str::slug($nextEpisode->series->name),
            'season' => $nextEpisode->series->season_no ?? $nextEpisode->season_id,
            'episode' => $nextEpisode->episode_no
        ]) : null;

        return compact(
            'video',
            'casts',
            'catListData',
            'watchlist',
            'vidData',
            'seriesdata',
            'nextEpisode',
            'previousEpisode',
            'series',
            'previousEpisodeUrl',
            'nextEpisodeUrl'
        );
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

        SeoHelper::setMeta(
            $video->name,
            Str::limit(strip_tags($video->description), 160),
            $video->cast_id,
            $video->landscape_url,
            url()->current()
        );
        
        $user_watch_report_watch_time = UserWatchReport::where('user_id', auth()->id())
            ->where('series_id', $id)
            ->where('episode_id', $video->id)
            ->orderBy('id', 'desc')->latest()
            ->value('watch_time');
            
        $userWatch = $user_watch_report_watch_time; // seconds
        $videoDurationSeconds = $video->episode_duration / 1000; // ms → seconds

        // If user completed the video
        if ($userWatch >= $videoDurationSeconds) {
            $lastWatchTime = 0;  // start from beginning next time
        } else {
            $lastWatchTime = $userWatch; // resume where they left
        }
        
        // Extract URL variables for the view
        $previousEpisodeUrl = $viewData['previousEpisodeUrl'] ?? null;
        $nextEpisodeUrl = $viewData['nextEpisodeUrl'] ?? null;
        
        return view("webseries-view", array_merge($viewData, compact('lastWatchTime', 'previousEpisodeUrl', 'nextEpisodeUrl')));
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
        
        $video->increment('view'); 
        $viewData = $this->getEpisodeViewData($video);
        $viewData['allSeries'] = Series::where('status', 1)->where('id', '!=', $video->series_id)->get();
        $viewData['seasons'] = Series::where('id', $video->series_id)->select('season')->distinct()->get();

        SeoHelper::setMeta(
            $video->name,
            Str::limit(strip_tags($video->description), 160),
            $video->cast_id,
            $video->landscape_url,
            url()->current()
        );
        
        $user_watch_report_watch_time = UserWatchReport::where('user_id', auth()->id())
            ->where('episode_id', $id)
            ->orderBy('id', 'desc')->latest()->value('watch_time');
            
        $userWatch = $user_watch_report_watch_time; // seconds
        $videoDurationSeconds = $video->episode_duration / 1000; // ms → seconds

        // If user completed the video
        if ($userWatch >= $videoDurationSeconds) {
            $lastWatchTime = 0;  // start from beginning next time
        } else {
            $lastWatchTime = $userWatch; // resume where they left
        }
        
        // Extract URL variables for the view
        $previousEpisodeUrl = $viewData['previousEpisodeUrl'] ?? null;
        $nextEpisodeUrl = $viewData['nextEpisodeUrl'] ?? null;
        
        return view("webseries-episode-view", array_merge($viewData, compact('lastWatchTime', 'previousEpisodeUrl', 'nextEpisodeUrl')));
    }

    public function webseriesEpisodeViewBySlug($slug, $season, $episode)
    {
        $series = Series::where('status', 1)->get()->first(function ($s) use ($slug) {
            return Str::slug($s->name) === $slug || ($s->slug ?? null) === $slug;
        });

        if (!$series) {
            abort(404);
        }

        $video = Episodes::with([
                'series',
                'season',
                'language'
            ])
            ->where('series_id', $series->id)
            ->where('episode_no', $episode)
            ->where('status', 1)
            ->first();

        if (!$video) {
            abort(404);
        }

        $video->increment('view');

        // Get episode data including previous/next
        $viewData = $this->getEpisodeViewDataSlug($video);

        $viewData['allSeries'] = Episodes::with([
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
                'slug',
                'season_id'
            ]);

        $viewData['seasons'] = Season::where('series_id', $video->series_id)
            ->orderBy('id')
            ->get();

        SeoHelper::setMeta(
            $video->name,
            Str::limit(strip_tags($video->description), 160),
            $video->cast_id,
            $video->landscape_url,
            url()->current()
        );

        $userWatch = UserWatchReport::where('user_id', auth()->id())
            ->where('episode_id', $video->id)
            ->latest('id')
            ->value('watch_time') ?? 0;

        $videoDurationSeconds = $video->episode_duration / 1000;

        if ($userWatch >= $videoDurationSeconds) {
            $lastWatchTime = 0;
        } else {
            $lastWatchTime = $userWatch;
        }

        $viewData['lastWatchTime'] = $lastWatchTime;

        // Extract the URL variables so they're accessible in the view
        $previousEpisodeUrl = $viewData['previousEpisodeUrl'] ?? null;
        $nextEpisodeUrl = $viewData['nextEpisodeUrl'] ?? null;

        return view('webseries-episode-view', array_merge($viewData, compact('previousEpisodeUrl', 'nextEpisodeUrl')));
    }

    public function getEpisodeViewDataSlug(Episodes $video)
    {
        $castIds = array_filter(explode(',', $video->cast_id ?? ''));
        $catlist = array_filter(explode(',', $video->category_id ?? ''));

        $casts = Cast::whereIn('id', $castIds)->get();
        $catListData = Category::whereIn('id', $catlist)->get();

        $watchlist = app('wishlist');
        session(['watchlist' => $watchlist]);

        // All Episodes
        $vidData = Episodes::where('series_id', $video->series_id)
            ->where('status', 1)
            ->orderBy('season_id')
            ->orderBy('episode_no')
            ->get();

        // Seasons
        $seriesdata = Season::with('episodes')
            ->where('series_id', $video->series_id)
            ->orderBy('id')
            ->get();

        // PREVIOUS EPISODE
        $previousEpisode = Episodes::with('series') // Add this
            ->where('series_id', $video->series_id)
            ->where('season_id', $video->season_id)
            ->where('episode_no', '<', $video->episode_no)
            ->where('status', 1)
            ->orderByDesc('episode_no')
            ->first();

        if (!$previousEpisode) {
            $previousSeason = Season::where('series_id', $video->series_id)
                ->where('id', '<', $video->season_id)
                ->orderByDesc('id')
                ->first();

            if ($previousSeason) {
                $previousEpisode = Episodes::with('series') // Add this
                    ->where('series_id', $video->series_id)
                    ->where('season_id', $previousSeason->id)
                    ->where('status', 1)
                    ->orderByDesc('episode_no')
                    ->first();
            }
        }

        if (!$previousEpisode) {
            $previousSeries = Series::where('id', '<', $video->series_id)
                ->where('status', 1)
                ->orderByDesc('id')
                ->first();

            if ($previousSeries) {
                $lastSeason = Season::where('series_id', $previousSeries->id)
                    ->orderByDesc('id')
                    ->first();

                if ($lastSeason) {
                    $previousEpisode = Episodes::with('series') // Add this
                        ->where('series_id', $previousSeries->id)
                        ->where('season_id', $lastSeason->id)
                        ->where('status', 1)
                        ->orderByDesc('episode_no')
                        ->first();
                }
            }
        }

        // NEXT EPISODE
        $nextEpisode = Episodes::with('series') // Add this
            ->where('series_id', $video->series_id)
            ->where('season_id', $video->season_id)
            ->where('episode_no', '>', $video->episode_no)
            ->where('status', 1)
            ->orderBy('episode_no', 'asc')
            ->first();

        if (!$nextEpisode) {
            $nextSeason = Season::where('series_id', $video->series_id)
                ->where('id', '>', $video->season_id)
                ->whereHas('episodes', function ($query) {
                    $query->where('status', 1);
                })
                ->orderBy('id', 'asc')
                ->first();

            if ($nextSeason) {
                $nextEpisode = Episodes::with('series') // Add this
                    ->where('series_id', $video->series_id)
                    ->where('season_id', $nextSeason->id)
                    ->where('status', 1)
                    ->orderBy('episode_no', 'asc')
                    ->first();
            }
        }

        if (!$nextEpisode) {
            $nextSeries = Series::where('id', '>', $video->series_id)
                ->where('status', 1)
                ->whereHas('episodes', function ($query) {
                    $query->where('status', 1);
                })
                ->orderBy('id', 'asc')
                ->first();

            if ($nextSeries) {
                $firstSeason = Season::where('series_id', $nextSeries->id)
                    ->whereHas('episodes', function ($query) {
                        $query->where('status', 1);
                    })
                    ->orderBy('id', 'asc')
                    ->first();

                if ($firstSeason) {
                    $nextEpisode = Episodes::with('series') // Add this
                        ->where('series_id', $nextSeries->id)
                        ->where('season_id', $firstSeason->id)
                        ->where('status', 1)
                        ->orderBy('episode_no', 'asc')
                        ->first();
                }
            }
        }

        // Generate URLs using null coalescing with fallback
        $previousEpisodeUrl = null;
        if ($previousEpisode) {
            $series = $previousEpisode->series;
            $slug = $series->slug ?? Str::slug($series->name ?? '');
            $season = $series->season_no ?? $previousEpisode->season_id;
            $episode = $previousEpisode->episode_no;
            $previousEpisodeUrl = route('webseries.episode.view.by.slug', compact('slug', 'season', 'episode'));
        }

        $nextEpisodeUrl = null;
        if ($nextEpisode) {
            $series = $nextEpisode->series;
            $slug = $series->slug ?? Str::slug($series->name ?? '');
            $season = $series->season_no ?? $nextEpisode->season_id;
            $episode = $nextEpisode->episode_no;
            $nextEpisodeUrl = route('webseries.episode.view.by.slug', compact('slug', 'season', 'episode'));
        }

        return [
            'video' => $video,
            'casts' => $casts,
            'catListData' => $catListData,
            'watchlist' => $watchlist,
            'vidData' => $vidData,
            'seriesdata' => $seriesdata,
            'previousEpisode' => $previousEpisode,
            'nextEpisode' => $nextEpisode,
            'previousEpisodeUrl' => $previousEpisodeUrl,
            'nextEpisodeUrl' => $nextEpisodeUrl,
        ];
    }

}
