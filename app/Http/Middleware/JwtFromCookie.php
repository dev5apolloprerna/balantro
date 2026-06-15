<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class JwtFromCookie
{
    public function handle(Request $request, Closure $next)
    {
        // If no Authorization header but we have jwt cookie, promote it.
        if (!$request->bearerToken() && $request->cookies->has('jwt')) {
            $token = $request->cookie('jwt');
            if ($token) {
                // Make it look like a normal Bearer token for auth:api
                $request->headers->set('Authorization', 'Bearer ' . $token);
            }
        }
        return $next($request);
    }
}
