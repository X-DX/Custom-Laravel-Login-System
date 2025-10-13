<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class checkSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->session_id !== Session::getId()) {
                Auth::logout();
                Session::flush();

                return redirect('/login')->withErrors(['error' => 'You have been logged out because your account was logged in elsewhere.']);
            }
        }
        
        return $next($request);
    }
}
