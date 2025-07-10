<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Video;
use App\Models\Notification;
use App\Models\Watchlists;
use App\Models\UserDownloads;
use App\Models\Cast;
use App\Helpers\VideoHelper;
use App\Models\Language;


class ViewDetailsController extends Controller
{
    //

     public function index($id){
        $id = \App\Helpers\VideoHelper::decryptID($id);
        $video = Video::active()->select('video.*', 'language.name as language_name')
        ->join('language', 'language.id', '=', 'video.language_id')
        ->where('video.id', $id)
        ->first();

        $movieData = Video::active()->where('type_id',config('constant.TYPES.MOVIES'))
                        ->select('video.*', 'language.name as language_name')
                        ->join('language', 'language.id', '=', 'video.language_id')
                        ->where('video.id','!=',$id)->limit(12)->get();
                       
        $video->increment('view');   
        // Explode the comma-separated cast IDs
        $castIds = explode(',', $video->cast_id);
        $catlist = explode(',', $video->category_id);
        // Fetch the cast details for each ID
        $casts = Cast::whereIn('id', $castIds)->get();
        $catListData = Category::whereIn('id',$catlist)->get();

        $poadcastData = Video::active()->select('video.*', 'language.name as language_name')
        ->join('language', 'language.id', '=', 'video.language_id')
        ->whereRaw('FIND_IN_SET(?, video.category_id)', [27])
        ->where('video.type_id', config('constant.TYPES.PODCAST'))->where('video.id','!=',$id)
        ->limit(12)
        ->get();

        $shortfilmsData = Video::active()->select('video.*', 'language.name as language_name')
        ->join('language', 'language.id', '=', 'video.language_id')
        ->where('type_id', config('constant.TYPES.SHORTFILMS'))->where('video.id','!=',$id)
        ->get();

        $trailerData = Video::active()->where('video.type_id', config('constant.TYPES.TRAILER'))->select('video.*', 'language.name as language_name')
        ->join('language', 'language.id', '=', 'video.language_id')->where('video.id','!=',$id)->get();
        

        $musicData = Video::active()->select('video.*', 'language.name as language_name')
        ->join('language', 'language.id', '=', 'video.language_id')
        ->where('category_id', 20)
        ->where('type_id', config('constant.TYPES.MUSIC'))->where('video.id','!=',$id)
        ->limit(12)
        ->get();

        $user_downloads = UserDownloads::where('user_id', auth()->id())
        ->where('video_id', $id)
        ->first();

        $watchlist = app('wishlist');
        session(['watchlist' => $watchlist]);

        return view("view-detail", ['user_downloads'=>$user_downloads,'video' => $video,'poadcastData' => $poadcastData,'musicData'=>$musicData,'shortfilmsData'=>$shortfilmsData,'trailerData'=>$trailerData,'casts' => $casts, 'movieData' => $movieData,   'catListData'=> $catListData, 'watchlist' => $watchlist]);
    }
}
