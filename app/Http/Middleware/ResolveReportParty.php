<?php

namespace App\Http\Middleware;

use App\Support\Demo\ReportContext;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * For the read-only report API routes.
 *
 * 1. Resolves the party server-side (own company, an assigned client for
 *    staff, or the sample company when the app sends report_mode=demo).
 * 2. Overwrites the `partyguid` input and header with that party's GUID, so
 *    controller code that reads `$request->partyguid` can no longer be
 *    pointed at another company's books.
 * 3. Adds `report_context` (is_demo, company_name) to JSON responses so the
 *    app can show the sample-data banner.
 */
class ResolveReportParty
{
    public function handle(Request $request, Closure $next): Response
    {
        $requested = $request->input('partyguid') ?: $request->header('partyguid');
        $party = ReportContext::party(is_string($requested) ? $requested : null);

        $request->merge(['partyguid' => $party->guid]);
        $request->headers->set('partyguid', (string) $party->guid);

        $response = $next($request);

        if ($response instanceof JsonResponse) {
            $data = $response->getData(true);
            if (is_array($data) && ! array_is_list($data)) {
                $data['report_context'] = $party->toResponseContext();
                $response->setData($data);
            }
        }

        return $response;
    }
}
