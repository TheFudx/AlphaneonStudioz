<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Video;
use App\Models\Episodes;
use App\Models\Cast;
use App\Models\Category;
use App\Models\Notification;
use App\Models\Watchlists;
use App\Models\Series;

class MoodController extends Controller
{
    public function index($id)
    {
        $id = \App\Helpers\VideoHelper::decryptID($id);
        $categoryIds = intval($id); // Convert $id to integer
        
        if($categoryIds === 26){
            $videos = Series::all();
        }else{
            $videos = Video::where('status',1)->whereRaw("FIND_IN_SET(?, category_id)", [$categoryIds]) // Add the limit of 1
                ->get();
                if ($videos->isEmpty()) {
                    $videos = Episodes::where('status',1)->whereRaw("FIND_IN_SET(?, category_id)", [$categoryIds])
                        ->get();
                }
        }
        $categoryName = Category::where('id',$id)->first();
        $name = $categoryName->name;
        
        $watchlist = app('wishlist');
        session(['watchlist' => $watchlist]);
        return view('mood', [ 'video' => $videos, 'id' => $id,'name'=>$name, 'watchlist' => $watchlist]);
    }
}