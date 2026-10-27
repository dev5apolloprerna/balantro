<?php

namespace App\Services\Demo;

use App\Models\Client;
use App\Models\User;
use App\Support\Demo\DemoParty;
use App\Support\Demo\ReportMode;
use App\Support\Demo\ReportParty;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * The single place that decides which party a report reads.
 *
 * Rules:
 *  - Clients read their own party, or the sample company when in demo mode.
 *    A GUID in the request is accepted only if it is their own or the
 *    sample company's; anything else is refused (403).
 *  - Staff read their own id when no GUID is given (unchanged behaviour), or a
 *    client they are assigned to when a GUID is given. Super admins may read
 *    any client. Staff never read the sample company except super admins.
 */
class ReportPartyResolver
{
    public function resolve(Request $request, ?string $requestedGuid = null): ReportParty
    {
        $user = $request->user();
        if (! $user instanceof User) {
            throw new AuthenticationException();
        }

        $requestedGuid = $this->normaliseGuid($requestedGuid);

        if (ReportMode::isClient($user)) {
            return $this->resolveForClient($request, $user, $requestedGuid);
        }

        return $this->resolveForStaff($user, $requestedGuid);
    }

    /** True once the Tally sync has written any year or voucher for the user. */
    public function hasOwnData(User $user): bool
    {
        if (DemoParty::isDemoId($user->id)) {
            return false;
        }

        $ttl = max(1, (int) config('demo.own_data_cache_seconds', 300));

        return (bool) Cache::remember("report_party:has_own_data:{$user->id}", $ttl, function () use ($user) {
            return DB::table('YearMaster')->where('iPartyId', $user->id)->exists()
                || DB::table('VchHistory')->where('iPartyId', $user->id)->exists();
        });
    }

    /** Whether a staff user may read reports for a client. */
    public function staffCanAccessClient(User $staff, int $clientId): bool
    {
        if ($staff->isSuperAdmin()) {
            return true;
        }

        $pivot = match ((int) $staff->role) {
            User::ROLES['manager'] => ['clients_managers', 'manager_id'],
            User::ROLES['supervisor'] => ['clients_supervisors', 'supervisor_id'],
            User::ROLES['data_entry_operator'] => ['clients_data_entry_operators', 'data_entry_operator_id'],
            default => null,
        };

        if ($pivot === null) {
            return false;
        }

        return DB::table($pivot[0])
            ->where($pivot[1], $staff->id)
            ->where('client_id', $clientId)
            ->exists();
    }

    private function resolveForClient(Request $request, User $user, ?string $requestedGuid): ReportParty
    {
        $ownGuid = $this->normaliseGuid($user->guid);
        $asksForDemo = $requestedGuid !== null && DemoParty::isDemoGuid($requestedGuid);
        $asksForOwn = $requestedGuid === null
            || ($ownGuid !== null && strcasecmp($requestedGuid, $ownGuid) === 0);

        if (! $asksForDemo && ! $asksForOwn) {
            abort(403, 'You can only view reports for your own company.');
        }

        if ($asksForDemo && ! DemoParty::available()) {
            abort(404);
        }

        $mode = $asksForDemo ? ReportMode::DEMO : ReportMode::current($request, $user);

        return $mode === ReportMode::DEMO && DemoParty::available()
            ? ReportParty::demo()
            : ReportParty::fromUser($user);
    }

    private function resolveForStaff(User $user, ?string $requestedGuid): ReportParty
    {
        if ($requestedGuid === null) {
            return ReportParty::fromUser($user);
        }

        if (DemoParty::isDemoGuid($requestedGuid)) {
            abort_unless($user->isSuperAdmin() && DemoParty::available(), 404);

            return ReportParty::demo();
        }

        // Client's global scope already excludes the sample company.
        $client = Client::query()->where('guid', $requestedGuid)->first();
        abort_if($client === null, 404);
        abort_unless($this->staffCanAccessClient($user, (int) $client->id), 403, 'This client is not assigned to you.');

        return ReportParty::fromUser($client);
    }

    private function normaliseGuid($guid): ?string
    {
        $guid = is_string($guid) ? trim($guid) : '';

        return $guid === '' ? null : $guid;
    }
}
