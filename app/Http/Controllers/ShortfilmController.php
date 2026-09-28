<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Video;
use App\Models\Notification;
use App\Models\Watchlists;
class ShortfilmController extends Controller
{
    public function index() {
        
        $catlist = Category::join('video', 'video.category_id', '=', 'category.id')
        ->select('category.name as category_name','category.id as cat_id')
        ->distinct()
        ->where('video.type_id','=',config('constant.TYPES.SHORTFILMS'))->get();
        $video = Video::active()->where('type_id',config('constant.TYPES.SHORTFILMS'))
        ->whereNotNull('video.landscape')
        ->where('video.landscape', '!=', '')
        ->select('video.*', 'language.name as language_name')
        ->join('language', 'language.id', '=', 'video.language_id')->orderBy('video.order_by', 'desc')
        ->paginate(24);
        
        $watchlist = app('wishlist');
        session(['watchlist' => $watchlist]);
        return view('shortfilms-list', ['video'=> $video,  'catlist'=>$catlist,   'watchlist' => $watchlist]);
        
    }
}
