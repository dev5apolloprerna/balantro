<?php

namespace App\Support\Demo;

use App\Models\User;

/**
 * The company a report is run for: the caller's own party, a client a staff
 * member is assigned to, or the read-only sample company.
 *
 * Controllers must take the party id / guid from this object, never from
 * request input.
 */
final class ReportParty
{
    public function __construct(
        public readonly int $id,
        public readonly ?string $guid,
        public readonly string $name,
        public readonly ?string $email,
        public readonly bool $isDemo,
    ) {
    }

    public static function fromUser(User $user): self
    {
        $guid = trim((string) $user->guid);

        return new self(
            (int) $user->id,
            $guid === '' ? null : $guid,
            (string) $user->name,
            $user->email,
            false,
        );
    }

    public static function demo(): self
    {
        return new self(
            DemoParty::id(),
            DemoParty::guid(),
            DemoParty::name(),
            null,
            true,
        );
    }

    /** Company name for report headers and exports. */
    public function displayName(): string
    {
        return $this->isDemo ? $this->name . ' (sample)' : $this->name;
    }

    /** Prefix for export file names, so sample files are never mistaken for real ones. */
    public function filePrefix(): string
    {
        return $this->isDemo ? 'sample-' : '';
    }

    public function toResponseContext(): array
    {
        return [
            'is_demo' => $this->isDemo,
            'company_name' => $this->isDemo ? $this->displayName() : $this->name,
        ];
    }
}
