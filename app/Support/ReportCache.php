<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ReportCache
{
    /**
     * Short cache lifetime for report data that can be changed outside Laravel (Tally sync/merge).
     */
    public static function ttl()
    {
        return now()->addMinutes(2);
    }

    /**
     * Include a lightweight database fingerprint in report cache keys so deletes/merges made by
     * Tally stop serving old cached vouchers/groups before the TTL expires.
     */
    public static function version(int $partyId): string
    {
        $vouchers = DB::table('VchHistory')->where('iPartyId', $partyId)->count();
        $ledgers = DB::table('LedgerMaster')->where('iPartyId', $partyId)->count();
        $groups = DB::table('GroupMaster')->where('iPartyId', $partyId)->count();
        $years = DB::table('YearMaster')->where('iPartyId', $partyId)->count();

        return md5("v:{$vouchers}|l:{$ledgers}|g:{$groups}|y:{$years}");
    }

    public static function key(string $prefix, int $partyId, string $suffix = ''): string
    {
        $key = trim($prefix, ':') . ":{$partyId}:" . self::version($partyId);

        return $suffix === '' ? $key : $key . ':' . ltrim($suffix, ':');
    }
}
