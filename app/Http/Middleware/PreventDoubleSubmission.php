<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventDoubleSubmission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $fingerprint = $request->fingerprint();

    if (cache()->has($fingerprint)) {
        return back()->withErrors(['Form already submitted. Please wait...']);
    }

    cache()->put($fingerprint, true, 2); // 2 seconds lock

    $response = $next($request);
        return $response;

    }
}
