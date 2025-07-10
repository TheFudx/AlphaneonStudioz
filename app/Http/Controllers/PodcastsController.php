<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Video;
use App\Models\Cast;
use App\Models\Category;
use App\Models\Notification;
use App\Models\Language;
use App\Models\Watchlists;

class PodcastsController extends Controller
{
    
    public function podcastsList(){
        
        $catlist = Category::join('video', 'video.category_id', '=', 'category.id')
        ->select('category.name as category_name','category.id as cat_id')
        ->distinct()
        ->where('video.type_id','=',config('constant.TYPES.PODCAST'))->get();
        
        $video = Video::active()->select('video.*', 'language.name as language_name')
        ->join('language', 'language.id', '=', 'video.language_id')
        ->where('video.type_id', config('constant.TYPES.PODCAST')) // Specify the table for `type_id`
        ->orderBy('created_at', 'desc')->paginate(config('constant.PER_PAGE')); 
        
        $watchlist = app('wishlist');
        session(['watchlist' => $watchlist]);
        return view('podcast-list',[ 'catlist'=>$catlist, 'video'=> $video,  'watchlist' => $watchlist]);
    }
        
}
