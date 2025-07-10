<?php
namespace App\Http\Controllers;
use App\Models\Video;
use App\Models\Banner;
use App\Models\Cast;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Notification;
class MusicController extends Controller
{
    public function __construct()
    {
        $this->middleware('mobile_auth');
    }
   
    public function musicsList(){
        
        $catlist = Category::join('video', 'video.category_id', '=', 'category.id')
        ->select('category.name as category_name','category.id as cat_id')
        ->distinct()
        ->where('video.type_id','=',config('constant.TYPES.MUSIC'))->get();
        $video = Video::active()->select('video.*', 'language.name as language_name')
        ->join('language', 'language.id', '=', 'video.language_id')
        ->where('type_id',config('constant.TYPES.MUSIC'))
        ->orderBy('created_at', 'desc')->paginate(config('constant.PER_PAGE')); 
        
        $watchlist = app('wishlist');
        session(['watchlist' => $watchlist]);
        return view('musics-list',[ 'catlist'=>$catlist, 'video'=> $video,  'watchlist' => $watchlist]);
    }


    public function musicshow($name, $id, $filename){
        
        $video = Video::active()->select('video.*', 'language.name as language_name')
        ->join('language', 'language.id', '=', 'video.language_id')
        ->where('video.id', $id)
        ->first();
        $vidData = Video::active()->where('type_id', 6)->get();
        $catlist = explode(',', $video->category_id);
        $castIds = explode(',', $video->cast_id);
        $casts = Cast::whereIn('id', $castIds)->get();
        $catListData = Category::whereIn('id',$catlist)->get();
        $video->increment('view');
        
        
        $watchlist = app('wishlist');
        session(['watchlist' => $watchlist]);
        return view("test-video-music", [
            'video' => $video,
            
            'casts' => $casts,
            'vidData' => $vidData,
            
            
            'watchlist' => $watchlist,
            'catListData'=> $catListData,
        ]);
    }
    public function musicpromot()
    {
       
        $videos = Banner::join('video', 'video.id', '=', 'banner.video_id')
        ->where('banner.type_id', '=', 5)
        ->where('banner.is_home_screen', '=', 1)
        ->orderByDesc('banner.created_at')
        ->get();
        $upcomming = Video::active()->where('type_id', 8)->orderBy('created_at', 'desc')->get();
        $music = Video::active()->where('type_id', 5)->get();
        $romance = Video::active()->where('category_id', 12)
                ->where('type_id', 6)
                ->get();
        $action = Video::active()->where('category_id', 13)
        ->where('type_id', 6)
        ->get();
        $thrill = Video::active()->where('category_id', 14)
        ->where('type_id', 6)
        ->get();
        
        
        $watchlist = app('wishlist');
        session(['watchlist' => $watchlist]);
        return view('index-music-promot', ['videos' => $videos, 'upcomming' => $upcomming, 'romance'=>$romance, 'action'=>$action, 'thrill'=>$thrill, 'music'=>$music,   'watchlist' => $watchlist]);
    }
}
