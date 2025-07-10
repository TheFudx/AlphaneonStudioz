<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Notification;
use Illuminate\Support\Facades\Redirect;
class MusicPlayerController extends Controller
{
    public function index(){
        if (!Auth::check()) {
            // User is not logged in, redirect to login page
            return redirect()->route('login');
        }
        $user = app('logged-in-user');
    
        // User is authenticated, redirect to the external URL
        return redirect()->to('https://alphastudioz.in/public/music-player/');
    }
    
}
