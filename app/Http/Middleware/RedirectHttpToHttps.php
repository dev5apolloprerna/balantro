<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectHttpToHttps
{
    /**
     * Redirect insecure HTTP requests to HTTPS.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->secure()) {
            $secureUrl = 'https://' . $request->getHttpHost() . $request->getRequestUri();

            return redirect()->to($secureUrl, Response::HTTP_MOVED_PERMANENTLY);
        }

        return $next($request);
    }
}