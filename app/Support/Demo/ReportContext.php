<?php

namespace App\Support\Demo;

use App\Services\Demo\ReportPartyResolver;
use Illuminate\Support\Facades\View;

/**
 * Request-scoped access to the resolved report party.
 *
 *   $party = ReportContext::party($request->input('guid'));
 *   $svc->pandl($party->id, ...);
 *
 * The result is memoised on the current request, and views get
 * `$reportParty` / `$isDemoReport` for the banner and PDF watermark.
 */
final class ReportContext
{
    public const ATTRIBUTE = 'report_party';

    public static function party(?string $requestedGuid = null): ReportParty
    {
        $request = request();
        $cacheKey = self::ATTRIBUTE . ':' . strtolower(trim((string) $requestedGuid));

        $party = $request->attributes->get($cacheKey);
        if (! $party instanceof ReportParty) {
            $party = app(ReportPartyResolver::class)->resolve($request, $requestedGuid);
            $request->attributes->set($cacheKey, $party);
        }

        self::remember($party);

        return $party;
    }

    /**
     * The party resolved by the `report.party` middleware for this request,
     * or a fresh resolution when called outside that middleware.
     */
    public static function resolved(): ReportParty
    {
        return self::current() ?? self::party();
    }

    /** The party resolved most recently on this request, if any. */
    public static function current(): ?ReportParty
    {
        $party = request()->attributes->get(self::ATTRIBUTE);

        return $party instanceof ReportParty ? $party : null;
    }

    /**
     * Session key for report filters (financial year, date range, selected
     * groups). Sample data uses its own keys so switching modes never changes
     * the client's real selections.
     */
    public static function sessionKey(string $key): string
    {
        return self::current()?->isDemo ? 'demo_' . $key : $key;
    }

    /** `sample-` while showing the sample company, otherwise empty. */
    public static function filePrefix(): string
    {
        return self::current()?->filePrefix() ?? '';
    }

    private static function remember(ReportParty $party): void
    {
        request()->attributes->set(self::ATTRIBUTE, $party);
        View::share('reportParty', $party);
        View::share('isDemoReport', $party->isDemo);
    }
}
