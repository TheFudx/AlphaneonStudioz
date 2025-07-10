<?php
namespace App\Http\Controllers;
use App\Models\Category;
use App\Models\Notification;
use Illuminate\Http\Request;
class ContactusController extends Controller
{
    public function contactUs(){
        
        
        return view('contact-us');
    }
}
