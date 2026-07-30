<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Support\Collection;

trait BuildsEmptyAccountSummary
{
    /**
     * Build display-only account cards for a party that has no group data yet.
     *
     * Null IDs prevent the placeholders from masquerading as persisted ledgers.
     */
    protected function emptyAccountSummaryGroups(array $groupNames): Collection
    {
        return collect($groupNames)->map(static fn (string $groupName): object => (object) [
            'iGroupId' => null,
            'strGroupName' => $groupName,
            'Closing' => 0,
            'Opening' => 0,
        ]);
    }
}