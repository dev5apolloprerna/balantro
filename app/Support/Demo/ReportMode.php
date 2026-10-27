<?php

namespace App\Support\Demo;

use App\Models\User;
use App\Services\Demo\ReportPartyResolver;
use Illuminate\Http\Request;

/**
 * Decides whether a client is looking at the sample company or their own books.
 *
 * Order of precedence:
 *   1. explicit `report_mode` input or `X-Report-Mode` header (demo|own)
 *   2. the choice saved in the web session
 *   3. API requests: `own` (older app versions never receive sample data)
 *   4. web requests: `demo` until the client has data of their own
 */
final class ReportMode
{
    public const DEMO = 'demo';
    public const OWN = 'own';

    public const SESSION_KEY = 'report_mode';
    public const INPUT_KEY = 'report_mode';
    public const HEADER = 'X-Report-Mode';

    public static function current(Request $request, User $user): string
    {
        if (! DemoParty::available() || ! self::isClient($user)) {
            return self::OWN;
        }

        $explicit = self::explicit($request);
        if ($explicit !== null) {
            return $explicit;
        }

        // API routes run without a session: the app must ask for sample data.
        if (! $request->hasSession()) {
            return self::OWN;
        }

        $saved = $request->session()->get(self::SESSION_KEY);
        if (in_array($saved, [self::DEMO, self::OWN], true)) {
            return $saved;
        }

        return app(ReportPartyResolver::class)->hasOwnData($user) ? self::OWN : self::DEMO;
    }

    /** Mode requested on this request, if any. */
    public static function explicit(Request $request): ?string
    {
        $value = $request->header(self::HEADER) ?: $request->input(self::INPUT_KEY);
        $value = is_string($value) ? strtolower(trim($value)) : null;

        return in_array($value, [self::DEMO, self::OWN], true) ? $value : null;
    }

    /** Clients (role 0) are the only users who get the sample company. */
    public static function isClient(User $user): bool
    {
        return (int) $user->role === User::ROLES['client'];
    }

    /** Whether to show the Sample / My business switch to this user. */
    public static function switchVisibleFor(?User $user): bool
    {
        return $user !== null && DemoParty::available() && self::isClient($user);
    }
}
