<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use App\Models\Sessions;
use Auth;
class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {   
        $user = Auth::user();
        if($user){
            $sessions = Sessions::where('user_id',$user->id)->first();
            if(!$sessions){
                return route('login');
            }else{
                return $request->expectsJson();
            }
        }else{
             return route('login');
        }
    }
}
