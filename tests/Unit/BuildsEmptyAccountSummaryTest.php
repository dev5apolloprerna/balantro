<?php

namespace Tests\Unit;

use App\Http\Controllers\Concerns\BuildsEmptyAccountSummary;
use PHPUnit\Framework\TestCase;

class BuildsEmptyAccountSummaryTest extends TestCase
{
    public function test_empty_summary_groups_have_zero_balances_and_no_persisted_ids(): void
    {
        $builder = new class
        {
            use BuildsEmptyAccountSummary;

            public function build(array $names)
            {
                return $this->emptyAccountSummaryGroups($names);
            }
        };

        $groups = $builder->build(['Sales Accounts', 'Bank Accounts']);

        $this->assertSame(['Sales Accounts', 'Bank Accounts'], $groups->pluck('strGroupName')->all());
        $this->assertSame([0, 0], $groups->pluck('Closing')->all());
        $this->assertSame([0, 0], $groups->pluck('Opening')->all());
        $this->assertSame([null, null], $groups->pluck('iGroupId')->all());
    }
}