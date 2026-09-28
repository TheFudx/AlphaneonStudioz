<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Video;
use App\Models\Cast;
use App\Models\Category;
use App\Models\Notification;
use App\Models\Language;
use App\Models\Watchlists;
class TrailerDetailsController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('mobile_auth');
    // }

    public function trailersList(Request $request)
    {
        $filter = $request->category ?? null;

        $catlist = Category::join('video', 'video.category_id', '=', 'category.id')
            ->select('category.name as category_name', 'category.id as cat_id')
            ->distinct()
            ->where('video.type_id', config('constant.TYPES.TRAILER'))
            ->get();

        $query = Video::active()
            ->select('video.*', 'language.name as language_name')
            ->join('language', 'language.id', '=', 'video.language_id')
            ->where('video.type_id', config('constant.TYPES.TRAILER'))
            ->whereNotNull('video.landscape')
            ->where('video.landscape', '!=', '')
            ->orderBy('order_by', 'desc');

        // If filter selected apply it
        if ($filter && $filter != "All") {
            $query->where('video.trailer_for', $filter);
        }

        $video = $query->paginate(24)->withQueryString();

        $trailer_list = Video::active()
            ->whereNotNull('trailer_for')
            ->where('trailer_for', '!=', '')
            ->distinct('trailer_for')
            ->pluck('trailer_for');

        $watchlist = app('wishlist');
        session(['watchlist' => $watchlist]);

        return view('trailers-list', [
            'catlist' => $catlist,
            'trailer_list' => $trailer_list,
            'video' => $video,
            'watchlist' => $watchlist
        ]);
    }
}
