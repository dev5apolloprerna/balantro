<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApplyThemePreferences
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // If no theme is set in session, try to get from localStorage via cookie
        if (!session()->has('theme')) {
            $themeFromCookie = $request->cookie('theme', null);
            session(['theme' => $themeFromCookie ?: 'dark']);
            // if ($themeFromCookie) {
            //     session(['theme' => $themeFromCookie]);
            // }
        }
        
        return $next($request);
    }
}
