<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = app('logged-in-user');

        if (!$user || !$user->hasActiveSubscription()) {
            return redirect()->route('subscription.page');
        }
        $response = $next($request);
        return $response;
    }
}
