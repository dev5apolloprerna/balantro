<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\ReportsService;
use App\Support\Demo\DemoParty;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Read-only checks for the sample company: configuration, isolation and the
 * P&L figures the sample vouchers are meant to produce.
 *
 * Runs only SELECTs and the existing report stored procedures. Exit code is
 * non-zero when any check fails, so it can run in CI or after each re-sync.
 */
class DemoVerify extends Command
{
    protected $signature = 'demo:verify {--from= : Start date (Y-m-d)} {--to= : End date (Y-m-d)}';

    protected $description = 'Check the sample company configuration, isolation and reconciliation targets';

    private int $failures = 0;

    public function handle(ReportsService $reports): int
    {
        $id = DemoParty::id();
        $guid = DemoParty::guid();

        $this->check('DEMO_PARTY_ID and DEMO_PARTY_GUID are set', $id > 0 && $guid !== null);
        if ($id <= 0 || $guid === null) {
            return $this->finish();
        }

        $user = User::withTrashed()->find($id);
        $this->check("users #{$id} exists", $user !== null);
        if ($user === null) {
            return $this->finish();
        }

        $this->check('row is a client (role 0)', (int) $user->role === User::ROLES['client']);
        $this->check('row is inactive', ! (bool) $user->is_active);
        $this->check('row GUID matches DEMO_PARTY_GUID', strcasecmp((string) $user->guid, $guid) === 0);
        $this->check('row is not soft-deleted', $user->deleted_at === null);

        // Isolation: the sample GUID must never appear under another party id, and
        // no real client may share the sample GUID.
        $this->check(
            'no VchHistory row pairs the sample GUID with another party id',
            ! DB::table('VchHistory')->where('PartyGUID', $guid)->where('iPartyId', '<>', $id)->exists()
        );
        $this->check(
            'no other user has the sample GUID',
            ! User::withTrashed()->where('guid', $guid)->where('id', '<>', $id)->exists()
        );
        foreach (['clients_managers', 'clients_supervisors', 'clients_data_entry_operators'] as $pivot) {
            $this->check("sample company is not assigned in {$pivot}", ! DB::table($pivot)->where('client_id', $id)->exists());
        }

        $years = DB::table('YearMaster')->where('iPartyId', $id)->count();
        $vouchers = DB::table('VchHistory')->where('iPartyId', $id)->count();
        $this->check("YearMaster has rows for the sample company ({$years})", $years > 0);
        $this->check("VchHistory has rows for the sample company ({$vouchers})", $vouchers > 0);

        $from = $this->option('from') ?: config('demo.reconciliation.from');
        $to = $this->option('to') ?: config('demo.reconciliation.to');
        $this->newLine();
        $this->line("P&L reconciliation for {$from} to {$to} (via ReportsService::pandl):");

        $pl = $reports->pandl($id, $from, $to);
        $this->check('P&L stored procedure ran', (bool) ($pl['success'] ?? false));
        $data = $pl['data'] ?? [];

        $sumGroup = function (string $side, string $group) use ($data): float {
            return (float) collect($data[$side] ?? [])
                ->where('strGroupName', $group)
                ->sum(fn ($row) => (float) ($row['decMainAmount'] ?? 0));
        };
        $sumAll = fn (string $side): float => (float) collect($data[$side] ?? [])
            ->sum(fn ($row) => (float) ($row['decMainAmount'] ?? 0));

        $actual = [
            'sales' => $sumGroup('cr', 'Sales Accounts'),
            'purchases' => $sumGroup('dr', 'Purchase Accounts'),
            'direct_expenses' => $sumGroup('dr', 'Direct Expenses'),
            'opening_stock' => (float) ($data['OpeningStock'] ?? 0),
            'closing_stock' => (float) ($data['ClosingStock'] ?? 0),
            'indirect_incomes' => $sumAll('IndirectIncomes'),
            'indirect_expenses' => $sumAll('IndirectExpenses'),
            'gross_profit' => (float) ($data['GrossPandL'] ?? 0),
            'net_profit' => (float) ($data['NetPandL'] ?? 0),
        ];

        $tolerance = (float) config('demo.reconciliation.tolerance', 1.0);
        $rows = [];
        foreach ((array) config('demo.reconciliation.targets', []) as $key => $target) {
            $value = $actual[$key] ?? null;
            $ok = $value !== null && abs($value - (float) $target) <= $tolerance;
            $this->failures += $ok ? 0 : 1;
            $rows[] = [$key, number_format((float) $target, 2), $value === null ? 'n/a' : number_format($value, 2), $ok ? 'OK' : 'MISMATCH'];
        }
        $this->table(['Figure', 'Target', 'Report', 'Result'], $rows);

        $bs = $reports->balanceSheet($guid, $id, $from, $to);
        $this->check('Balance sheet stored procedure ran', (bool) ($bs['success'] ?? false));
        $this->line('Balance sheet rows returned: ' . count($bs['data']['rows'] ?? []) . ' (check totals against the sample company in Tally).');

        return $this->finish();
    }

    private function check(string $label, bool $ok): void
    {
        $this->line(($ok ? '<info>  OK  </info> ' : '<error> FAIL </error> ') . $label);
        $this->failures += $ok ? 0 : 1;
    }

    private function finish(): int
    {
        $this->newLine();
        if ($this->failures > 0) {
            $this->error("{$this->failures} check(s) failed.");
            return self::FAILURE;
        }

        $this->info('All sample company checks passed.');
        return self::SUCCESS;
    }
}
