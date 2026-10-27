<?php

namespace App\Support\Demo;

/**
 * Read-only access to the configured sample company.
 */
final class DemoParty
{
    /** True when the feature is switched on and the sample party is configured. */
    public static function available(): bool
    {
        return (bool) config('demo.enabled')
            && self::id() > 0
            && self::guid() !== null;
    }

    /** users.id of the sample company, or 0 when not configured. */
    public static function id(): int
    {
        return (int) config('demo.party_id', 0);
    }

    public static function guid(): ?string
    {
        $guid = trim((string) config('demo.party_guid', ''));

        return $guid === '' ? null : $guid;
    }

    public static function name(): string
    {
        return (string) config('demo.company_name', 'Sample Traders Pvt Ltd');
    }

    public static function email(): string
    {
        return strtolower((string) config('demo.email', ''));
    }

    /**
     * Whether an id is the sample company. Works even when the feature flag
     * is off, so the sample row stays protected while the feature is disabled.
     */
    public static function isDemoId($id): bool
    {
        $demoId = self::id();

        return $demoId > 0 && is_numeric($id) && (int) $id === $demoId;
    }

    public static function isDemoGuid($guid): bool
    {
        $demoGuid = self::guid();

        return $demoGuid !== null
            && is_string($guid)
            && strcasecmp(trim($guid), $demoGuid) === 0;
    }

    public static function isDemoEmail($email): bool
    {
        $demoEmail = self::email();

        return $demoEmail !== ''
            && is_string($email)
            && strtolower(trim($email)) === $demoEmail;
    }
}
