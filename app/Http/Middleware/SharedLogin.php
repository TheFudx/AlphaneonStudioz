<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SharedLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
         if (!Auth::check()) {
            $userId = $request->cookie('shared_user_id');
            if ($userId) {
                $user = User::find($userId);
                if ($user) {
                    Auth::login($user);
                }
            }
        }
        $response = $next($request);
        return $response;
    }
}
