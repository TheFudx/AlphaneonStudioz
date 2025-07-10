<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
class VerifyPhoneNumber
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        // if (!(Auth::check() || (Session::has('is_mobile_login') && Session::get('is_mobile_login') === true))) {
        //     // return redirect()->route('login')->with('error', 'Please log in.');
        //     Session::put('url.intended', $request->url());
        //     return redirect('/login');
        // }
        // return $next($request);
        if (!(Auth::check() || (Session::has('is_mobile_login') && Session::get('is_mobile_login') === true))) {
            // If it's an AJAX request, return a JSON error
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401); // 401 Unauthorized
            }
            // For traditional web requests, redirect
            Session::put('url.intended', $request->url());
            return redirect('/login');
        }
        return $next($request);
    }
}
