<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifyClient
{
    // public function handle(Request $request, Closure $next)
    // {
    //     if (!Auth::check() || !Auth::user()->isClient()) {
    //         return redirect('/')->with('error', 'You are not authorized to access this page.');
    //     }

    //     return $next($request);
    // }

    public function handle(Request $request, Closure $next)
    {
        // Example check: only allow users with "client" role
        if (!Auth::check() || Auth::user()->role !== 'client') {
            abort(403, 'Unauthorized client.');
        }

        return $next($request);
    }
}
