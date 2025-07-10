<?php
namespace App\Http\Controllers;
use App\Models\Transction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Category;
use App\Models\Notification;
class TransactionController extends Controller
{
    public function thankyou(){
        
        
        $userid = app('logged-in-user')->id;
        $transaction = Transction::where('user_id', $userid)
        ->where('status', 1)
        ->orderBy('created_at', 'desc')
        ->first();
        return view('thankyou', [  'transaction'=>$transaction]);
    }
     
   
}
