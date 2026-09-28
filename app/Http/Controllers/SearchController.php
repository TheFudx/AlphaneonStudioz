<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        return view('search-result');
    }

    public function searchAjax(Request $request)
    {
        $query = $request->get('query');

    if (strlen($query) < 2) {
        return response()->json([]);
    }

    $results = [];

    $searchTerm = strtolower(trim($query));

    /**
     * Music Category Search
     *
     * If user searches:
     * song
     * songs
     *
     * Show category:
     * Songs
     *
     * Link:
     * /musics-index
     */
    if (in_array($searchTerm, ['song', 'songs', 'music', 'musics'])) {
        $results[] = [
            'id' => 'music-category',
            'title' => 'Songs',
            'type' => 'Music Category',
            'type_id' => 5,
            'url' => route('musicplayer.index'),
            'image' => null,
            'subtitle' => 'Browse All Songs',
            'views' => 0
        ];
    }

        // 1. SEARCH IN VIDEO TABLE (Main content) - Limit 10
        $videos = DB::table('video')
            ->leftJoin('type', 'video.type_id', '=', 'type.id')
            ->leftJoin('series', 'video.series_id', '=', 'series.id')
            ->where(function($q) use ($query) {
                $q->where('video.name', 'LIKE', "%{$query}%")
                  ->orWhere('video.slug', 'LIKE', "%{$query}%")
                  ->orWhere('type.name', 'LIKE', "%{$query}%")
                  ->orWhere('series.name', 'LIKE', "%{$query}%")
                  ->orWhere('video.release_year', 'LIKE', "%{$query}%")
                  ->orWhere('video.release_date', 'LIKE', "%{$query}%")
                  ->orWhere('video.language_id', 'LIKE', "%{$query}%");
            })
            ->select(
                'video.id',
                'video.name as title',
                'video.slug',
                'video.thumbnail',
                'video.landscape',
                'video.thumbnail_url',
                'video.landscape_url',
                'video.description',
                'video.type_id',
                'video.video_type',
                'video.series_id',
                'video.view',
                'type.name as type_name',
                'series.name as series_name'
            )
            ->where('video.status', 1)
            ->limit(30)
            ->get()
            ->map(function($video) {
                $image = !empty($video->thumbnail_url) ? $video->thumbnail_url : 
                        (!empty($video->thumbnail) ? asset('storage/' . $video->thumbnail) : null);
                
                // Get content type name from type_id
                $typeName = $this->getTypeName($video->type_id);
                $contentType = $this->getContentType($video->type_id);
                
                // Generate URL based on content type
                $url = $this->generateContentUrl($video->type_id, $video->slug, $video->id);
                
                return [
                    'id' => $video->id,
                    'title' => $video->title,
                    'type' => $contentType,
                    'type_id' => $video->type_id,
                    'url' => $url,
                    'image' => $image,
                    'subtitle' => $typeName ?? ($video->series_name ?? 'Video'),
                    'views' => $video->view ?? 0,
                    'series_name' => $video->series_name ?? null,
                    'slug' => $video->slug ?? null
                ];
            });

        $results = array_merge($results, $videos->toArray());

        // 2. SEARCH IN SERIES TABLE - Limit 10
        $series = DB::table('series')
            ->leftJoin('type', 'series.type', '=', 'type.id')
            ->where(function($q) use ($query) {
                $q->where('series.name', 'LIKE', "%{$query}%")
                  ->orWhere('series.slug', 'LIKE', "%{$query}%")
                  ->orWhere('type.name', 'LIKE', "%{$query}%");
            })
            ->select(
                'series.id',
                'series.name as title',
                'series.slug',
                'series.image',
                'series.image_url',
                'series.landscape_image',
                'series.landscape_image_url',
                'series.description',
                'series.type',
                'type.name as type_name',
                'series.status'
            )
            ->where('series.status', 1)
            ->limit(30)
            ->get()
            ->map(function($series) {
                $image = !empty($series->image_url) ? $series->image_url : 
                        (!empty($series->image) ? asset('storage/' . $series->image) : null);
                
                // Generate URL for series
                $url = $this->generateSeriesUrl($series->type, $series->slug, $series->id);
                $contentType = $this->getContentType($series->type);
                
                return [
                    'id' => $series->id,
                    'title' => $series->title,
                    'type' => $contentType,
                    'type_id' => $series->type ?? null,
                    'url' => $url,
                    'image' => $image,
                    'subtitle' => $series->type_name ?? 'Series',
                    'views' => 0,
                    'slug' => $series->slug ?? null
                ];
            });

        $results = array_merge($results, $series->toArray());

        // 3. SEARCH IN EPISODE TABLE - Limit 10
        $episodes = DB::table('episodes')
            ->leftJoin('series', 'episodes.series_id', '=', 'series.id')
            ->leftJoin('type', 'episodes.type_id', '=', 'type.id')
            ->where(function($q) use ($query) {
                $q->where('episodes.name', 'LIKE', "%{$query}%")
                  ->orWhere('episodes.slug', 'LIKE', "%{$query}%")
                  ->orWhere('series.name', 'LIKE', "%{$query}%")
                  ->orWhere('type.name', 'LIKE', "%{$query}%");
            })
            ->select(
                'episodes.id',
                'episodes.name as title',
                'episodes.slug',
                'episodes.thumbnail',
                'episodes.landscape',
                'episodes.thumbnail_url',
                'episodes.landscape_url',
                'episodes.description',
                'episodes.type_id',
                'episodes.episode_type',
                'episodes.series_id',
                'episodes.episode_no',
                'episodes.season_id',
                'episodes.view',
                'series.name as series_name',
                'series.slug as series_slug',
                'type.name as type_name'
            )
            ->where('episodes.status', 1)
            ->limit(30)
            ->get()
            ->map(function($episode) {
                $image = !empty($episode->thumbnail_url) ? $episode->thumbnail_url : 
                        (!empty($episode->thumbnail) ? asset('storage/' . $episode->thumbnail) : null);
                
                $typeName = $this->getTypeName($episode->type_id);
                $contentType = $this->getContentType($episode->type_id);
                
                // Generate URL for episode
                $url = $this->generateEpisodeUrl(
                    $episode->type_id, 
                    $episode->series_slug, 
                    $episode->season_id, 
                    $episode->episode_no, 
                    $episode->slug,
                    $episode->id
                );
                
                return [
                    'id' => $episode->id,
                    'title' => $episode->title,
                    'type' => $contentType . ' Episode',
                    'type_id' => $episode->type_id,
                    'url' => $url,
                    'image' => $image,
                    'subtitle' => $episode->series_name ?? ($typeName ?? 'Episode'),
                    'views' => $episode->view ?? 0,
                    'series_name' => $episode->series_name ?? null,
                    'series_slug' => $episode->series_slug ?? null,
                    'episode_no' => $episode->episode_no ?? null,
                    'season_id' => $episode->season_id ?? null
                ];
            });

        $results = array_merge($results, $episodes->toArray());

        // 4. SEARCH IN TYPE TABLE (Content Categories) - Limit 10
        $types = DB::table('type')
            ->where('name', 'LIKE', "%{$query}%")
            ->where('status', 1)
            ->limit(30)
            ->get()
            ->map(function($type) {
                // Generate URL for type/category
                $contentType = $this->getContentType($type->id);
                $slug = strtolower(str_replace(' ', '-', $type->name));
                
                // Use the list route for the content type
                $url = $this->getContentListRoute($type->id);
                
                return [
                    'id' => $type->id,
                    'title' => $type->name,
                    'type' => $contentType . ' Category',
                    'type_id' => $type->id,
                    'url' => $url,
                    'image' => null,
                    'subtitle' => 'Browse All ' . $contentType,
                    'views' => 0
                ];
            });

        $results = array_merge($results, $types->toArray());

        // 5. SEARCH IN SEASON TABLE - Limit 10
        $seasons = DB::table('season')
            ->leftJoin('series', 'season.series_id', '=', 'series.id')
            ->where(function($q) use ($query) {
                $q->where('season.season', 'LIKE', "%{$query}%")
                  ->orWhere('series.name', 'LIKE', "%{$query}%");
            })
            ->select(
                'season.id',
                'season.season as title',
                'season.series_id',
                'series.name as series_name',
                'series.slug as series_slug',
                'series.type'
            )
            ->where('season.status', 1)
            ->limit(30)
            ->get()
            ->map(function($season) {
                // Season URL
                $contentType = $this->getContentType($season->type);
                $url = $this->generateSeasonUrl($season->type, $season->series_slug, $season->title);
                
                return [
                    'id' => $season->id,
                    'title' => 'Season ' . ($season->title ?? '') . ' - ' . ($season->series_name ?? ''),
                    'type' => $contentType . ' Season',
                    'type_id' => $season->type,
                    'url' => $url,
                    'image' => null,
                    'subtitle' => $season->series_name ?? 'Season',
                    'views' => 0,
                    'series_slug' => $season->series_slug ?? null
                ];
            });

        $results = array_merge($results, $seasons->toArray());

        // Remove duplicates based on URL
        $uniqueResults = [];
        $seenUrls = [];
        foreach ($results as $item) {
            if (!in_array($item['url'], $seenUrls)) {
                $seenUrls[] = $item['url'];
                $uniqueResults[] = $item;
            }
        }

        // Sort by relevance
        usort($uniqueResults, function($a, $b) {
            $priority = [
                'upcoming' => 1,
                'music' => 2,
                'movies' => 3,
                'trailer' => 4,
                'shortfilms' => 5,
                'series' => 6,
                'podcast' => 7,
                'comedyshow' => 8,
                'episode' => 9,
                'category' => 10,
                'season' => 11
            ];
            
            $typeA = strtolower($a['type']);
            $typeB = strtolower($b['type']);
            
            $priorityA = $priority[$typeA] ?? 99;
            $priorityB = $priority[$typeB] ?? 99;
            
            if ($priorityA === $priorityB) {
                return strcmp($a['title'], $b['title']);
            }
            
            return $priorityA - $priorityB;
        });

        // Limit total results to 20
        $uniqueResults = array_slice($uniqueResults, 0, 20);

        return response()->json($uniqueResults);
    }

    /**
     * Get the list route for a content type
     */
    private function getContentListRoute($typeId)
    {
        switch ($typeId) {
            case 1:
                return route('upcoming.list'); // You may need to create this route
            case 5:
                return route('musics.list');
            case 6:
                return route('movies.list');
            case 8:
                return route('trailers.list');
            case 9:
                return route('shortfilms.list');
            case 10:
                return route('webseries.list');
            case 11:
                return route('podcasts.list');
            case 12:
                return route('comedy.show.list');
            default:
                return route('movies.list');
        }
    }

    /**
     * Generate URL for content detail
     */
    private function generateContentUrl($typeId, $slug, $id)
{
    $slug = $slug ?? $id ?? 'unknown';

    switch ((int) $typeId) {
        case 1: // Upcoming
            return route('view.details.slug', [
                'content' => 'upcoming',
                'slug' => $slug
            ]);

        case 5: // Music
            return route('view.details.slug', [
                'content' => 'musics',
                'slug' => $slug
            ]);

        case 6: // Movies
            return route('view.details.slug', [
                'content' => 'movies',
                'slug' => $slug
            ]);

        case 8: // Trailer
            return route('view.details.slug', [
                'content' => 'trailers',
                'slug' => $slug
            ]);

        case 9: // Short Films
            return route('view.details.slug', [
                'content' => 'shortfilms',
                'slug' => $slug
            ]);

        case 10: // Series
            return route('view.details.slug', [
                'content' => 'series',
                'slug' => $slug
            ]);

        case 11: // Podcast
            return route('podcast.details', [
                'slug' => $slug
            ]);

        case 12: // Comedy Show
            return route('comedy.show.episode.view.by.slug', [
                'slug' => $slug,
                'season' => 1,
                'episode' => 1
            ]);

        default:
            return route('view.details.slug', [
                'content' => 'movies',
                'slug' => $slug
            ]);
    }
}


/**
 * Generate URL for series
 */
private function generateSeriesUrl($typeId, $slug, $id)
{
    $slug = $slug ?? $id ?? 'unknown';

    switch ((int) $typeId) {
        case 10: // Series
            return route('view.details.slug', [
                'content' => 'series',
                'slug' => $slug
            ]);

        case 11: // Podcast
            return route('podcast.details', [
                'slug' => $slug
            ]);

        case 12: // Comedy Show
            // If this route is specifically for episode,
            // use the actual episode route only when episode data exists.
            return route('comedy.show.episode.view.by.slug', [
                'slug' => $slug,
                'season' => 1,
                'episode' => 1
            ]);

        default:
            return route('view.details.slug', [
                'content' => 'series',
                'slug' => $slug
            ]);
    }
}


/**
 * Generate URL for season
 */
private function generateSeasonUrl($typeId, $seriesSlug, $seasonNumber)
{
    $seriesSlug = $seriesSlug ?? 'unknown';
    $seasonNumber = $seasonNumber ?? 1;

    switch ((int) $typeId) {

        case 10: // Series
            // No season-only route provided.
            // Redirect to series detail.
            return route('view.details.slug', [
                'content' => 'series',
                'slug' => $seriesSlug
            ]);

        case 11: // Podcast
            // No season-only route provided.
            // Redirect to podcast detail.
            return route('podcast.details', [
                'slug' => $seriesSlug
            ]);

        case 12: // Comedy Show
            // No season-only route provided.
            // Cannot generate a correct episode URL without episode number.
            return route('comedy.show.episode.view.by.slug', [
                'slug' => $seriesSlug,
                'season' => $seasonNumber,
                'episode' => 1
            ]);

        default:
            return route('view.details.slug', [
                'content' => 'series',
                'slug' => $seriesSlug
            ]);
    }
}


/**
 * Generate episode URL
 */
private function generateEpisodeUrl(
    $typeId,
    $seriesSlug,
    $seasonId,
    $episodeNo,
    $slug,
    $episodeId
) {
    // Get season number
    $seasonNumber = 1;

    if ($seasonId) {
        $season = DB::table('season')
            ->where('id', $seasonId)
            ->first();

        $seasonNumber = $season->season ?? 1;
    }

    $episodeNo = $episodeNo ?? 1;
    $seriesSlug = $seriesSlug ?? 'unknown';

    switch ((int) $typeId) {

        case 10: // Series
            return route('webseries.episode.view.by.slug', [
                'slug' => $seriesSlug,
                'season' => $seasonNumber,
                'episode' => $episodeNo
            ]);

        case 11: // Podcast
            return route('podcast.details', [
                'slug' => $seriesSlug
            ]);

        case 12: // Comedy Show
            return route('comedy.show.episode.view.by.slug', [
                'slug' => $seriesSlug,
                'season' => $seasonNumber,
                'episode' => $episodeNo
            ]);

        default:
            return route('view.details.slug', [
                'content' => 'movies',
                'slug' => $slug ?? $episodeId
            ]);
    }
}

    /**
     * Get type name from type_id
     */
    private function getTypeName($typeId)
    {
        if (!$typeId) {
            return null;
        }
        $type = DB::table('type')->where('id', $typeId)->first();
        return $type->name ?? null;
    }

    /**
     * Get content type display name from type_id
     */
    private function getContentType($typeId)
    {
        switch ($typeId) {
            case 1:
                return 'Upcoming';
            case 5:
                return 'Music';
            case 6:
                return 'Movies';
            case 8:
                return 'Trailer';
            case 9:
                return 'Short Films';
            case 10:
                return 'Series';
            case 11:
                return 'Podcast';
            case 12:
                return 'Comedy Show';
            default:
                return 'Content';
        }
    }

    public function getTrendingContent()
    {
        $trending = [];

        // 1. Trending Videos (most viewed) - Limit 10
        $videos = DB::table('video')
            ->leftJoin('type', 'video.type_id', '=', 'type.id')
            ->leftJoin('series', 'video.series_id', '=', 'series.id')
            ->where('video.status', 1)
            ->orderBy('video.view', 'desc')
            ->limit(30)
            ->select(
                'video.id',
                'video.name as title',
                'video.slug',
                'video.thumbnail_url',
                'video.landscape_url',
                'video.view',
                'video.type_id',
                'video.series_id',
                'type.name as type_name',
                'series.name as series_name',
                'video.video_type'
            )
            ->get()
            ->map(function($video) {
                $image = !empty($video->landscape_url) ? $video->landscape_url : 
                        (!empty($video->thumbnail_url) ? $video->thumbnail_url : null);
                
                $url = $this->generateContentUrl($video->type_id, $video->slug, $video->id);
                $contentType = $this->getContentType($video->type_id);
                
                return [
                    'id' => $video->id,
                    'title' => $video->title,
                    'type' => $contentType,
                    'url' => $url,
                    'image' => $image,
                    'subtitle' => $video->type_name ?? ($video->series_name ?? 'Video'),
                    'views' => $video->view ?? 0
                ];
            });

        $trending = array_merge($trending, $videos->toArray());

        // 2. Trending Series - Limit 10
        $series = DB::table('series')
            ->leftJoin('type', 'series.type', '=', 'type.id')
            ->where('series.status', 1)
            ->orderBy('series.id', 'desc')
            ->limit(30)
            ->select(
                'series.id',
                'series.name as title',
                'series.slug',
                'series.image_url',
                'series.landscape_image_url',
                'series.type',
                'type.name as type_name'
            )
            ->get()
            ->map(function($series) {
                $image = !empty($series->landscape_image_url) ? $series->landscape_image_url : 
                        (!empty($series->image_url) ? $series->image_url : null);
                
                $url = $this->generateSeriesUrl($series->type, $series->slug, $series->id);
                $contentType = $this->getContentType($series->type);
                
                return [
                    'id' => $series->id,
                    'title' => $series->title,
                    'type' => $contentType,
                    'url' => $url,
                    'image' => $image,
                    'subtitle' => $series->type_name ?? 'Series',
                    'views' => 0
                ];
            });

        $trending = array_merge($trending, $series->toArray());

        // 3. Trending Episodes - Limit 10
        $episodes = DB::table('episodes')
            ->leftJoin('series', 'episodes.series_id', '=', 'series.id')
            ->where('episodes.status', 1)
            ->orderBy('episodes.view', 'desc')
            ->limit(30)
            ->select(
                'episodes.id',
                'episodes.name as title',
                'episodes.slug',
                'episodes.thumbnail_url',
                'episodes.landscape_url',
                'episodes.view',
                'episodes.type_id',
                'episodes.season_id',
                'episodes.episode_no',
                'series.name as series_name',
                'series.slug as series_slug'
            )
            ->get()
            ->map(function($episode) {
                $image = !empty($episode->landscape_url) ? $episode->landscape_url : 
                        (!empty($episode->thumbnail_url) ? $episode->thumbnail_url : null);
                
                $url = $this->generateEpisodeUrl(
                    $episode->type_id, 
                    $episode->series_slug, 
                    $episode->season_id, 
                    $episode->episode_no, 
                    $episode->slug,
                    $episode->id
                );
                $contentType = $this->getContentType($episode->type_id);
                
                return [
                    'id' => $episode->id,
                    'title' => $episode->title,
                    'type' => $contentType . ' Episode',
                    'url' => $url,
                    'image' => $image,
                    'subtitle' => $episode->series_name ?? 'Episode',
                    'views' => $episode->view ?? 0
                ];
            });

        $trending = array_merge($trending, $episodes->toArray());

        // Shuffle and limit to 15
        // shuffle($trending);
        $trending = array_slice($trending, 0, 15);

        return response()->json($trending);
    }

    public function advancedSearch(Request $request)
    {
        $query = $request->get('query');
        $type = $request->get('type');
        $sortBy = $request->get('sort', 'relevance');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $results = [];

        if ($type == 'all' || $type == 'video') {
            $videoQuery = DB::table('video')
                ->leftJoin('type', 'video.type_id', '=', 'type.id')
                ->where('video.name', 'LIKE', "%{$query}%")
                ->orWhere('type.name', 'LIKE', "%{$query}%")
                ->where('video.status', 1)
                ->limit(30);

            if ($sortBy == 'views') {
                $videoQuery->orderBy('video.view', 'desc');
            } elseif ($sortBy == 'latest') {
                $videoQuery->orderBy('video.created_at', 'desc');
            }

            $videos = $videoQuery->get();
            // Process videos...
        }

        return response()->json($results);
    }
}