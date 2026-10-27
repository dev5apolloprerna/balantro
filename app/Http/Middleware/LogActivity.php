<?php
// app/Http/Middleware/LogActivity.php
namespace App\Http\Middleware;

use App\Support\ActivityLogger;
use Closure;

class LogActivity
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // Skip noisy assets or health checks
        if (preg_match('#\.(css|js|png|jpg|jpeg|gif|svg|ico|webp)$#i', $request->path())) {
            return $response;
        }

        // Only log "interesting" write actions or named routes
        // if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE']) || $request->route()?->getName()) {
        //     ActivityLogger::log('request', [
         // Skip read-only and asset requests to avoid an extra database write on
        // every page view. Mutating requests still produce an audit trail.
        if (! in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true) || preg_match('#\.(css|js|png|jpg|jpeg|gif|svg|ico|webp)$#i', $request->path())) {
            return $response;
        }

        ActivityLogger::log('request', [
            'type' => 'route',
            'id'   => $request->route()?->getName() ?? $request->path(),
        ], [
            'query' => $request->query(),
            // Never store secrets in the activity log.
            'input' => collect($request->except([
                '_token', 'password', 'password_confirmation', 'current_password',
                'new_password', 'new_password_confirmation', 'otp', 'token', 'registration_token',
            ]))->take(25),
            'status' => method_exists($response, 'getStatusCode') ? $response->getStatusCode() : null,
        ]);
        // }

        return $response;
    }
}
