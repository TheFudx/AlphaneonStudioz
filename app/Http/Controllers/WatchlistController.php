<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Video;
use App\Models\Episodes;
use App\Models\Notification;
use App\Models\Watchlists;

class WatchlistController extends Controller
{
   public function index(){
        $watchlist = session()->get('watchlist', []);
        session(['watchlist' => $watchlist]);

        return view('watchlist-list', ['watchlist'=>$watchlist,]);
    }
    public function addToWatchlist(Request $request)
    {
        $videoId = $request->input('video_id');
        $type = $request->input('type');
        $watchlist = session()->get('watchlist', []);
    
        $item = ['id'=> $videoId, 'type' => $type ];
    
        // Prevent duplicates by searching for the exact match in watchlist
        foreach ($watchlist as $existingItem) {
            if ($existingItem['id'] === $item['id'] && $existingItem['type'] === $item['type']) {
                return redirect()->back()->with('error', 'This item is already in your watchlist.');
            }
        }
    
        // Add the new item to the watchlist array
        $watchlist[] = $item;
    
        // Update the session with the modified watchlist
        session(['watchlist' => $watchlist]);
    
        return redirect()->back()->with('success', 'Item added to watchlist successfully.');
    }
    
    
    public function removeFromWatchlist(Request $request)
    {
        $videoId = $request->input('video_id');
        $type = $request->input('type');
        $watchlist = session()->get('watchlist', []);
        $itemToRemove = ['id'=> $videoId, 'type' => $type ];
       
        // Remove the item from the watchlist
        foreach ($watchlist as $key => $existingItem) {
            if ($existingItem['id'] === $itemToRemove['id'] && $existingItem['type'] === $itemToRemove['type']) {
                unset($watchlist[$key]);
                session(['watchlist' => array_values($watchlist)]);  // Reset array keys after removal
                return redirect()->back()->with('success', 'Item removed from watchlist successfully.');
            }
        }
    
        return redirect()->back()->with('error', 'Item not found in watchlist.');
    }
    
    
  
    
    
}
