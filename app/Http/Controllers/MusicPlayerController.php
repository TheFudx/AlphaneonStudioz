<?php
namespace App\Http\Controllers;
use App\Models\Video;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Notification;
use Illuminate\Support\Facades\Redirect;
class MusicPlayerController extends Controller
{
    public function index(){
        // if (!Auth::check()) {
        //     // User is not logged in, redirect to login page
        //     return redirect()->route('login');
        // }
        // $user = app('logged-in-user');
    
        // // User is authenticated, redirect to the external URL

        $catlist = Category::join('video', 'video.category_id', '=', 'category.id')
        ->select('category.name as category_name','category.id as cat_id')
        ->distinct()
        ->where('video.type_id','=',config('constant.TYPES.MUSIC'))->get();
        
        $video = Video::active()->select('video.*', 'language.name as language_name')
        ->join('language', 'language.id', '=', 'video.language_id')
        ->where('video.type_id', config('constant.TYPES.MUSIC'))
        ->whereNotNull('video.landscape')
        ->where('video.landscape', '!=', '')
        ->orderBy('created_at', 'desc')->paginate(24); 
        
        $watchlist = app('wishlist');
        session(['watchlist' => $watchlist]);
        return view('musics-list',[ 'catlist'=>$catlist, 'video'=> $video,  'watchlist' => $watchlist]);
    }
    
}
