<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Video;
use App\Models\Notification;
use App\Models\Watchlists;
class KidsController extends Controller
{
    public function index(){
        
        $catlist = Category::join('video', 'video.category_id', '=', 'category.id')
        ->select('category.name as category_name','category.id as cat_id')
        ->distinct()
        ->where('video.type_id','=',config('constant.TYPES.KIDS'))->get();
        $video = Video::active()->where('type_id',config('constant.TYPES.KIDS'))->get();
        
        $watchlist = app('wishlist');
        session(['watchlist' => $watchlist]);
        return view('kids', ['video'=> $video,  'catlist'=>$catlist,   'watchlist' => $watchlist]);
    }
}
