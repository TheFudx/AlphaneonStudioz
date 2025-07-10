<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Notification;
class TvShowController extends Controller
{
    public function index(){
        
        
        return view('tvshows-list', [ ]);
    }
}
