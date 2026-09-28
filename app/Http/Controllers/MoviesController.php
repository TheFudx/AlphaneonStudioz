<?php
namespace App\Http\Controllers;
use App\Models\Category;
use App\Models\Video;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\Watchlists;
use App\Models\Cast;

class MoviesController extends Controller
{
    
    public function moviesList(Request $request)
    {
        $categoryId = $request->get('category'); // get selected category

        // Category list
        $catlist = Category::join('video', 'video.category_id', '=', 'category.id')
            ->select('category.name as category_name', 'category.id as cat_id')
            ->distinct()
            ->where('video.type_id', config('constant.TYPES.MOVIES'))
            ->get();

        // Video Query
        $videoQuery = Video::active()
            ->select('video.*', 'language.name as language_name')
            ->join('language', 'language.id', '=', 'video.language_id')
            ->where('type_id', config('constant.TYPES.MOVIES'));

        // ✅ Apply Category Filter if selected
        if (!empty($categoryId) && $categoryId != 'All') {
            $videoQuery->whereRaw("FIND_IN_SET(?, video.category_id)", [$categoryId]);
        }

        $video = $videoQuery->whereNotNull('video.landscape')
            ->where('video.landscape', '!=', '')
            ->orderBy('order_by', 'desc')
            ->paginate(24)
            ->appends(['category' => $categoryId]); // 👈 VERY IMPORTANT

        $watchlist = app('wishlist');
        session(['watchlist' => $watchlist]);

        return view('movies-list', [
            'catlist' => $catlist,
            'video' => $video,
            'watchlist' => $watchlist,
            'selectedCategory' => $categoryId
        ]);
    }
    public function filterMovies(Request $request)
    {
        $categoryId = $request->get('category_id');

        $videoQuery = Video::active()
            ->select('video.*', 'language.name as language_name')
            ->join('language', 'language.id', '=', 'video.language_id')
            ->where('type_id', config('constant.TYPES.MOVIES'))
            ->orderBy('created_at', 'desc');

        if ($categoryId && $categoryId !== 'All') {
            $videoQuery->where('category_id', $categoryId);
        }

        $video = $videoQuery->whereNotNull('video.landscape')
            ->where('video.landscape', '!=', '')
            ->paginate(24);

        $watchlist = app('wishlist'); // same as moviesList()

        // Instead of partial view, return section from main view
        return view('movies-list', compact('video', 'watchlist'))->renderSections()['videoList'];
    }
}
