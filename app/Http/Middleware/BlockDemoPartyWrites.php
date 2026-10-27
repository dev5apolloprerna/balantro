<?php

namespace App\Http\Middleware;

use App\Support\Demo\DemoParty;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Keeps the sample company read-only.
 *
 * Runs in the `web` and `api` groups. Refuses any state-changing request that
 * targets the sample company, by company selection (session iPartyId), id or
 * GUID input, or route parameter. Read-only report routes (those carrying the
 * `report.party` middleware) are exempt because they legitimately send the
 * sample GUID.
 *
 * This is a second line of defence: the Client model's global scope already
 * hides the sample company from route-model binding and staff lists.
 */
class BlockDemoPartyWrites
{
    private const ID_FIELDS = ['iPartyId', 'ipartyid', 'party_id', 'client_id', 'client_user_id', 'user_id'];
    private const GUID_FIELDS = ['guid', 'partyguid', 'party_guid'];
    // Only parameters that always name a client. Generic `{id}` parameters are
    // transaction or document ids and must not be compared with a user id.
    private const ROUTE_PARAMS = ['client', 'guid'];

    public function handle(Request $request, Closure $next): Response
    {
        if (DemoParty::id() <= 0) {
            return $next($request);
        }

        $route = $request->route();

        // Selecting the sample company for bulk uploads is a write path even though it is a GET.
        if ($route && $route->getName() === 'sales.select.company' && DemoParty::isDemoId($route->parameter('id'))) {
            return $this->deny($request);
        }

        if ($request->isMethodSafe() || $this->isReadOnlyReportRoute($request)) {
            return $next($request);
        }

        if ($request->hasSession() && DemoParty::isDemoId($request->session()->get('iPartyId'))) {
            return $this->deny($request);
        }

        foreach (self::ID_FIELDS as $field) {
            if (DemoParty::isDemoId($request->input($field))) {
                return $this->deny($request);
            }
        }

        foreach (self::GUID_FIELDS as $field) {
            if (DemoParty::isDemoGuid($request->input($field))) {
                return $this->deny($request);
            }
        }

        if ($route) {
            foreach (self::ROUTE_PARAMS as $param) {
                $value = $route->parameter($param);
                if (DemoParty::isDemoId(is_object($value) ? $value->getKey() : $value) || DemoParty::isDemoGuid($value)) {
                    return $this->deny($request);
                }
            }
        }

        return $next($request);
    }

    private function isReadOnlyReportRoute(Request $request): bool
    {
        $route = $request->route();

        return $route !== null && in_array('report.party', $route->gatherMiddleware(), true);
    }

    private function deny(Request $request): Response
    {
        $message = 'The sample company is read-only. Switch to My business to make changes.';

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['status' => false, 'message' => $message], 403);
        }

        return redirect()->back()->with('alert', $message);
    }
}
