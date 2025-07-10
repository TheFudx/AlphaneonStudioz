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
    
    public function moviesList(){
        
        $catlist = Category::join('video', 'video.category_id', '=', 'category.id')
        ->select('category.name as category_name','category.id as cat_id')
        ->distinct()
        ->where('video.type_id','=',config('constant.TYPES.MOVIES'))->get();
        $video = Video::active()->select('video.*', 'language.name as language_name')
        ->join('language', 'language.id', '=', 'video.language_id')
        ->where('type_id', config('constant.TYPES.MOVIES'))
        ->orderBy('created_at', 'desc')->paginate(config('constant.PER_PAGE')); 
        
        $watchlist = app('wishlist');
        session(['watchlist' => $watchlist]);
        return view('movies-list',[ 'catlist'=>$catlist, 'video'=> $video,  'watchlist' => $watchlist]);
    }
}
