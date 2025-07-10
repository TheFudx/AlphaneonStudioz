<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Notification;
class comingsoonController extends Controller
{
    public function coming(){
        
        return view('coming-soon');
    }
}
