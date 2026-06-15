<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ReportsService
{
    private function parseToYmd(?string $d): ?string
    {
        if (!$d) return null;
        return Carbon::parse($d)->format('Y-m-d');
    }

    private function defaultIndianFyRange(?string $startDate, ?string $endDate): array
    {
        if ($startDate && $endDate) {
            return [$startDate, $endDate];
        }

        $today = Carbon::today();
        if ($today->month < 4) {
            $fyStart = Carbon::create($today->year - 1, 4, 1);
            $fyEnd   = Carbon::create($today->year, 3, 31);
        } else {
            $fyStart = Carbon::create($today->year, 4, 1);
            $fyEnd   = Carbon::create($today->year + 1, 3, 31);
        }

        return [
            $startDate ?: $fyStart->format('Y-m-d'),
            $endDate   ?: $fyEnd->format('Y-m-d'),
        ];
    }


    public function pandl(int $partyId, ?string $from = null, ?string $to = null): array
    {
        try {
            // Convert incoming dates to Y-m-d (accept Y-m-d or d-m-Y or empty)
            $toYmd = function (?string $d): ?string {
                if (!$d) return null;
                // Carbon can parse both formats safely
                return Carbon::parse($d)->format('Y-m-d');
            };
            $startDate = $toYmd($from);
            $endDate   = $toYmd($to);

            if (!$startDate || !$endDate) {
                // today
                $today = Carbon::today();

                // Determine the financial year range
                if ($today->month < 4) {
                    // Jan, Feb, Mar → financial year started last year
                    $fyStart = Carbon::create($today->year - 1, 4, 1);
                    $fyEnd   = Carbon::create($today->year, 3, 31);
                } else {
                    // Apr to Dec → financial year started this year
                    $fyStart = Carbon::create($today->year, 4, 1);
                    $fyEnd   = Carbon::create($today->year + 1, 3, 31);
                }

                // Only override if not provided
                $startDate = $startDate ?: $fyStart->format('Y-m-d');
                $endDate   = $endDate   ?: $fyEnd->format('Y-m-d');
            }

            // Call your proc
            $rows = collect(DB::select('EXEC dbo.GetBalanceSheetBase ?, ?, ?', [
                $partyId,
                $startDate,
                $endDate
            ]));
            // dd($rows);
            // If nothing, still return a valid array
            if ($rows->isEmpty()) {
                return [
                    'success' => true,
                    'data' => [
                        'cr' => [],
                        'dr' => [],
                        'IndirectIncomes' => [],
                        'IndirectExpenses' => [],
                        'totalCr' => '0.00',
                        'totalDr' => '0.00',
                        'GrossPandL' => '0.00',
                        'NetPandL' => '0.00',
                    ],
                ];
            }

            $data = ['cr' => [], 'dr' => [], 'IndirectIncomes' => [], 'IndirectExpenses' => []];
            $totalCr = 0.0;
            $totalDr = 0.0;
            $totII = 0.0;
            $totIE = 0.0;

            foreach ($rows as $r) {
                $dr = (float)($r->DrAmount ?? 0);
                $cr = (float)($r->CRAmount ?? 0);

                switch ($r->strGroupName) {
                    case 'Sales Accounts':
                    case 'Direct Incomes':
                        $val = $cr - $dr;
                        $data['cr'][] = [
                            'iPrimaryGroupId' => $r->iPrimaryGroupId ?? null,
                            'strGroupName'    => $r->strGroupName ?? '',
                            'decMainAmount'   => $this->fmt($val),
                            'iPartyId'        => $r->iPartyId ?? null,
                            'iYearId'         => $r->iYearId ?? null,
                        ];
                        $totalCr += $val;
                        break;

                    case 'Purchase Accounts':
                    case 'Direct Expenses':
                        $neg = (-1 * $dr) - $cr; // negative
                        $pos = -1 * $neg;        // make positive
                        $data['dr'][] = [
                            'iPrimaryGroupId' => $r->iPrimaryGroupId ?? null,
                            'strGroupName'    => $r->strGroupName ?? '',
                            'decMainAmount'   => $this->fmt($pos),
                            'iPartyId'        => $r->iPartyId ?? null,
                            'iYearId'         => $r->iYearId ?? null,
                        ];
                        $totalDr += $pos;
                        break;

                    case 'Indirect Incomes':
                        $val = $cr - $dr;
                        $data['IndirectIncomes'][] = [
                            'iPrimaryGroupId' => $r->iPrimaryGroupId ?? null,
                            'strGroupName'    => $r->strGroupName ?? '',
                            'decMainAmount'   => $this->fmt($val),
                            'iPartyId'        => $r->iPartyId ?? null,
                            'iYearId'         => $r->iYearId ?? null,
                        ];
                        $totII += $val;
                        break;

                    case 'Indirect Expenses':
                        $neg = (-1 * $dr) - $cr;
                        $pos = -1 * $neg;
                        $data['IndirectExpenses'][] = [
                            'iPrimaryGroupId' => $r->iPrimaryGroupId ?? null,
                            'strGroupName'    => $r->strGroupName ?? '',
                            'decMainAmount'   => $this->fmt($pos),
                            'iPartyId'        => $r->iPartyId ?? null,
                            'iYearId'         => $r->iYearId ?? null,
                        ];
                        $totIE += $pos;
                        break;
                }
            }

            $gross = $totalCr - $totalDr;
            $net   = $gross + $totII - $totIE;

            $data['totalCr']    = $this->fmt($totalCr);
            $data['totalDr']    = $this->fmt($totalDr);
            $data['GrossPandL'] = $this->fmt($gross);
            $data['NetPandL']   = $this->fmt($net);

            return ['success' => true, 'data' => $data];
        } catch (\Throwable $e) {
            Log::error('ReportsService::pandl error', ['msg' => $e->getMessage()]);
            // Always return an array, even on error
            return [
                'success' => false,
                'message' => 'Failed to build P&L',
                'error'   => $e->getMessage(),
                'data'    => [
                    'cr' => [],
                    'dr' => [],
                    'IndirectIncomes' => [],
                    'IndirectExpenses' => [],
                    'totalCr' => '0.00',
                    'totalDr' => '0.00',
                    'GrossPandL' => '0.00',
                    'NetPandL' => '0.00',
                ],
            ];
        }
    }

    public function balanceSheet(?string $partyguid = null, int $partyId, ?string $from = null, ?string $to = null): array
    {
        try {
            $startDate = $this->parseToYmd($from);
            $endDate   = $this->parseToYmd($to);
            [$startDate, $endDate] = $this->defaultIndianFyRange($startDate, $endDate);

            // EXEC dbo.GetBalanceSheetDataByParty :guid, :pid, :sd, :ed
            // Using DB::select with positional bindings since you already use that style elsewhere.

            $rows = collect(DB::select(
                'EXEC dbo.GetBalanceSheetDataByParty ?, ?, ?, ?',
                [$partyguid, $partyId, $startDate, $endDate]
            ));

            // If you want to organize assets/liabilities/equity, adapt below.
            // Without exact schema, we return raw rows + computed totals when possible.
            $totals = [
                'assets'      => '0.00',
                'liabilities' => '0.00',
                'equity'      => '0.00',
            ];

            // Attempt to accumulate if columns exist
            $assets = 0.0;
            $liab = 0.0;
            $equity = 0.0;
            foreach ($rows as $r) {
                // Adjust these keys to your SP result set
                $grp = $r->GroupName ?? $r->strGroupName ?? '';
                $amt = (float)($r->Amount ?? $r->decMainAmount ?? 0);

                if (stripos($grp, 'asset') !== false)      $assets += $amt;
                elseif (stripos($grp, 'liabil') !== false) $liab   += $amt;
                elseif (
                    stripos($grp, 'equity') !== false
                    || stripos($grp, 'capital') !== false
                ) $equity += $amt;
            }
            $totals['assets']      = $this->fmt($assets);
            $totals['liabilities'] = $this->fmt($liab);
            $totals['equity']      = $this->fmt($equity);

            return [
                'success' => true,
                'meta' => [
                    'from' => $startDate,
                    'to'   => $endDate,
                ],
                'data' => [
                    'rows'   => $rows, // raw rows for your blade
                    'totals' => $totals,
                ],
            ];
        } catch (\Throwable $e) {
            Log::error('ReportsService::balanceSheet error', ['msg' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Failed to build Balance Sheet',
                'error'   => $e->getMessage(),
                'data'    => ['rows' => collect(), 'totals' => []],
            ];
        }
    }

    public function ledger(int $partyId, int $groupId, ?string $from = null, ?string $to = null, $strCustomerName = null): array
    {
        try {
            $startDate = $this->parseToYmd($from);
            $endDate   = $this->parseToYmd($to);
            //[$startDate, $endDate] = $this->defaultIndianFyRange($startDate, $endDate);

            // EXEC dbo.GetLedgerMasters ?, ?, ?, ?

            $rows = collect(DB::select(
                'EXEC dbo.GetLedgerMasters ?, ?, ?, ?, ?',
                [$partyId, $groupId, $startDate, $endDate, $strCustomerName]
            ));
            // Optional post-processing: group by ledger and compute totals
            $byLedger = [];
            $grandDr = 0.0;
            $grandCr = 0.0;

            foreach ($rows as $r) {
                $lid  = $r->iLedgerId  ?? $r->LedgerId ?? null;
                $name = $r->LedgerName ?? $r->strLedgerName ?? 'Ledger';

                $dr = (float)($r->DrAmount ?? 0);
                $cr = (float)($r->CrAmount ?? 0);
                $grandDr += $dr;
                $grandCr += $cr;

                if (!isset($byLedger[$lid])) {
                    $byLedger[$lid] = [
                        'ledger_id'   => $lid,
                        'ledger_name' => $name,
                        'rows'        => [],
                        'total_dr'    => 0.0,
                        'total_cr'    => 0.0,
                        'closing'     => 0.0,
                    ];
                }

                $byLedger[$lid]['rows'][] = $r;
                $byLedger[$lid]['total_dr'] += $dr;
                $byLedger[$lid]['total_cr'] += $cr;
            }

            foreach ($byLedger as &$L) {
                $L['closing'] = $L['total_dr'] - $L['total_cr']; // DR positive, CR negative (adjust if needed)
                $L['total_dr'] = $this->fmt($L['total_dr']);
                $L['total_cr'] = $this->fmt($L['total_cr']);
                $L['closing']  = $this->fmt($L['closing']);
            }
            unset($L);

            return [
                'success' => true,
                'meta' => [
                    'from'    => $startDate,
                    'to'      => $endDate,
                    'groupId' => $groupId,
                ],
                'data' => [
                    'rows'       => $rows,
                    'by_ledger'  => array_values($byLedger),
                    'grand_dr'   => $this->fmt($grandDr),
                    'grand_cr'   => $this->fmt($grandCr),
                    'grand_diff' => $this->fmt($grandDr - $grandCr),
                ],
            ];
        } catch (\Throwable $e) {
            Log::error('ReportsService::ledger error', ['msg' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Failed to build Ledger',
                'error'   => $e->getMessage(),
                'data'    => [
                    'rows' => collect(),
                    'by_ledger' => [],
                    'grand_dr' => '0.00',
                    'grand_cr' => '0.00',
                    'grand_diff' => '0.00',
                ],
            ];
        }
    }

    // public function voucherHistory(string $partyguid, int $ledgerId, ?string $from = null, ?string $to = null): array
    // {
    //     try {
    //         $startDate = $this->parseToYmd($from);
    //         $endDate   = $this->parseToYmd($to);
    //         //[$startDate, $endDate] = $this->defaultIndianFyRange($startDate, $endDate);

    //         // EXEC dbo.GetVchHistoryByLedger ?, ?, ?, ?
    //         $rows = collect(DB::select(
    //             'EXEC dbo.GetVchHistoryByLedger ?, ?, ?, ?',
    //             [$partyguid, $ledgerId, $startDate, $endDate]
    //         ));
    //         // Calculate opening balance (balance before start date)
    //         $openingBalance = 0.0;
    //         if ($startDate) {
    //             $openingBalance = (float)DB::scalar("
    //             SELECT ISNULL(SUM(DrAmount - CrAmount), 0.0)
    //             FROM VchHistory
    //             WHERE PartyGUID = ?
    //               AND iLedgerId = ?
    //               AND TRY_CONVERT(DATE, strVchDate) < ?
    //         ", [$partyguid, $ledgerId, $startDate]);
    //         }
    //         // Calculate totals and closing balance
    //         $totalDr = 0.0;
    //         $totalCr = 0.0;
    //         foreach ($rows as $r) {
    //             $totalDr += (float)($r->DrAmount ?? 0);
    //             $totalCr += (float)($r->CrAmount ?? 0);
    //         }

    //         $closingBalance = $openingBalance + ($totalDr - $totalCr);

    //         return [
    //             'success' => true,
    //             'meta' => [
    //                 'from'     => $startDate,
    //                 'to'       => $endDate,
    //                 'ledgerId' => $ledgerId,
    //             ],
    //             'data' => [
    //                 'rows'     => $rows,
    //                 'total_dr' => $this->fmt($totalDr),
    //                 'total_cr' => $this->fmt($totalCr),
    //                 'diff'     => $this->fmt($totalDr - $totalCr),
    //                 'opening_balance' => $this->fmt($openingBalance),
    //                 'closing_balance' => $this->fmt($closingBalance),
    //             ],
    //         ];
    //     } catch (\Throwable $e) {
    //         Log::error('ReportsService::voucherHistory error', ['msg' => $e->getMessage()]);
    //         return [
    //             'success' => false,
    //             'message' => 'Failed to build Voucher History',
    //             'error'   => $e->getMessage(),
    //             'data'    => [
    //                 'rows' => collect(),
    //                 'total_dr' => '0.00',
    //                 'total_cr' => '0.00',
    //                 'diff' => '0.00',
    //             ],
    //         ];
    //     }
    // }

    // public function voucherHistory(string $partyguid, int $ledgerId, ?string $from = null, ?string $to = null): array
    // {
    //     try {
    //         $startDate = $this->parseToYmd($from);
    //         $endDate   = $this->parseToYmd($to);

    //         // EXEC dbo.GetVchHistoryByLedger ?, ?, ?, ?
    //         $rows = collect(DB::select(
    //             'EXEC dbo.GetVchHistoryByLedger ?, ?, ?, ?',
    //             [$partyguid, $ledgerId, $startDate, $endDate]
    //         ));

    //         // Calculate opening balance (balance before start date)
    //         $openingBalance = 0.0;
    //         if ($startDate) {
    //             $openingBalance = (float)DB::scalar("
    //             SELECT ISNULL(SUM(DrAmount - CrAmount), 0.0)
    //             FROM VchHistory
    //             WHERE PartyGUID = ?
    //               AND iLedgerId = ?
    //               AND TRY_CONVERT(DATE, strVchDate) < ?
    //         ", [$partyguid, $ledgerId, $startDate]);
    //         }

    //         // Calculate totals and running balances correctly
    //         $totalDr = 0.0;
    //         $totalCr = 0.0;
    //         $runningBalance = $openingBalance;
    //         $runningBalanceNew = 0;
    //         foreach ($rows as $r) {
    //             $drAmount = (float)($r->DrAmount ?? 0);
    //             $crAmount = (float)($r->CrAmount ?? 0);

    //             $totalDr += abs($drAmount); // Absolute values for display
    //             $totalCr += abs($crAmount); // Absolute values for display

    //             // Calculate running balance: Debit increases, Credit decreases
    //             $runningBalance += (((-1) * $drAmount) - $crAmount);
    //             //$runningBalanceNew += $r->decRunningBalance + ($drAmount) -  $crAmount;

    //             // Store the calculated running balance for display
    //             $r->calculatedRunningBalance = $runningBalance;
    //         }

    //         //$closingBalance = $runningBalance;
    //         $closingBalance = $runningBalance;

    //         return [
    //             'success' => true,
    //             'meta' => [
    //                 'from'     => $startDate,
    //                 'to'       => $endDate,
    //                 'ledgerId' => $ledgerId,
    //             ],
    //             'data' => [
    //                 'rows'            => $rows,
    //                 'total_dr'        => $this->fmt($totalDr),
    //                 'total_cr'        => $this->fmt($totalCr),
    //                 'diff'            => $this->fmt($totalDr - $totalCr),
    //                 'opening_balance' => $this->fmt($openingBalance),
    //                 'closing_balance' => $this->fmt($closingBalance),
    //                 'raw_total_dr'    => $totalDr,
    //                 'raw_total_cr'    => $totalCr,
    //                 'raw_opening'     => $openingBalance,
    //                 'raw_closing'     => $closingBalance,
    //             ],
    //         ];
    //     } catch (\Throwable $e) {
    //         Log::error('ReportsService::voucherHistory error', ['msg' => $e->getMessage()]);
    //         return [
    //             'success' => false,
    //             'message' => 'Failed to build Voucher History',
    //             'error'   => $e->getMessage(),
    //             'data'    => [
    //                 'rows' => collect(),
    //                 'total_dr' => '0.00',
    //                 'total_cr' => '0.00',
    //                 'diff' => '0.00',
    //                 'opening_balance' => '0.00',
    //                 'closing_balance' => '0.00',
    //             ],
    //         ];
    //     }
    // }

    public function voucherHistory(?string $partyguid = null, int $ledgerId, ?string $from = null, ?string $to = null): array
    {
        try {
            $startDate = $this->parseToYmd($from) ?: '1900-01-01';
            $endDate = $this->parseToYmd($to) ?: date('Y-m-d');

            // Call stored procedure
            $results = DB::select('
            EXEC GetVchHistoryByLedger
                @PartyGUID = ?,
                @LedgerId = ?,
                @StartDate = ?,
                @EndDate = ?
        ', [$partyguid, $ledgerId, $startDate, $endDate]);

            $rows = collect($results);

            // Calculate totals
            $totalDr = 0.0;
            $totalCr = 0.0;

            foreach ($rows as $r) {
                $drAmount = abs((float)($r->DrAmount ?? 0));
                $crAmount = abs((float)($r->CrAmount ?? 0));

                $totalDr += $drAmount;
                $totalCr += $crAmount;
            }

            return [
                'success' => true,
                'meta' => [
                    'from' => $startDate,
                    'to' => $endDate,
                    'ledgerId' => $ledgerId,
                ],
                'data' => [
                    'rows' => $rows,
                    'raw_total_dr' => $totalDr,
                    'raw_total_cr' => $totalCr,
                ],
            ];
        } catch (\Throwable $e) {
            Log::error('ReportsService::voucherHistory error', ['msg' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Failed to fetch voucher history',
                'error' => $e->getMessage(),
                'data' => [
                    'rows' => collect(),
                    'raw_total_dr' => 0.0,
                    'raw_total_cr' => 0.0,
                ],
            ];
        }
    }

    public function monthlyGraph(int $partyId, ?string $from, ?string $to, int $type = 1, array $opts = []): array
    {
        $tz = 'Asia/Kolkata';
        $startDate = $this->parseToYmd($from, $tz);
        $endDate   = $this->parseToYmd($to,   $tz);
        [$startDate, $endDate] = $this->defaultIndianFyRange($startDate, $endDate, $tz);

        $dateStyleDefault = 23;

        // --- Fetch selected type series ---
        switch ($type) {
            case 1: // Sales & Purchase
                $selected = DB::select(
                    'EXEC dbo.usp_VchMonthlyCashInOut @PartyId=?, @Start=?, @End=?, @DateStyle=?',
                    [$partyId, $startDate, $endDate, $dateStyleDefault]
                );
                break;
            case 2: // Creditors & Debtors
                $groups2 = $opts['groups'] ?? 'Sundry Creditors,Sundry Debtors';
                $selected = DB::select(
                    'EXEC dbo.usp_MonthlyCashFlow_CreditorsDebtors @PartyId=?, @Start=?, @End=?, @DateStyle=?, @GroupList=?',
                    [$partyId, $startDate, $endDate, $dateStyleDefault, $groups2]
                );
                break;
            case 3: // Receipt & Payment
                $outflowNegative = !empty($opts['outflow_negative']) ? 1 : 0;
                $groups3       = $opts['groups']        ?? 'Sundry Creditors,Sundry Debtors';
                $excludeTypes3 = $opts['exclude_types'] ?? 'Rcpt,Pymt';
                $selected = DB::select(
                    'EXEC dbo.usp_MonthlyRcptPay_CreditorsDebtors @PartyId=?, @Start=?, @End=?, @DateStyle=?, @OutflowNegative=?, @GroupList=?, @ExcludeVchTypes=?',
                    [$partyId, $startDate, $endDate, $dateStyleDefault, $outflowNegative, $groups3, $excludeTypes3]
                );
                break;
            case 4: // Cash & Bank Flow
            default:
                $dateStyle4 = array_key_exists('date_style', $opts) ? $opts['date_style'] : 23;
                if ($dateStyle4 === '' || strtolower((string)$dateStyle4) === 'null') $dateStyle4 = null;
                $groups4       = $opts['groups']        ?? 'Cash-in-Hand,Bank Accounts';
                $excludeTypes4 = $opts['exclude_types'] ?? null;
                $selected = DB::select(
                    'EXEC dbo.usp_MonthlyCashBankFlow @PartyId=?, @Start=?, @End=?, @DateStyle=?, @GroupList=?, @ExcludeVchTypes=?',
                    [$partyId, $startDate, $endDate, $dateStyle4, $groups4, $excludeTypes4]
                );
                break;
        }

        // Normalize arrays
        $months  = [];
        $cashIn  = [];
        $cashOut = [];
        foreach ($selected as $r) {
            $months[]  = (string)($r->label ?? '');
            $cashIn[]  = isset($r->cashIn)  ? (float)$r->cashIn  : 0.0;
            $cashOut[] = isset($r->cashOut) ? (float)$r->cashOut : 0.0;
        }
        $totalInSelected  = round(array_sum($cashIn), 2);
        $totalOutSelected = round(array_sum($cashOut), 2);

        // ---- All eight totals (same as API) ----
        $r1 = DB::select(
            'EXEC dbo.usp_VchMonthlyCashInOut @PartyId=?, @Start=?, @End=?, @DateStyle=?',
            [$partyId, $startDate, $endDate, $dateStyleDefault]
        );
        $r2 = DB::select(
            'EXEC dbo.usp_MonthlyCashFlow_CreditorsDebtors @PartyId=?, @Start=?, @End=?, @DateStyle=?, @GroupList=?',
            [$partyId, $startDate, $endDate, $dateStyleDefault, 'Sundry Creditors,Sundry Debtors']
        );
        $r3 = DB::select(
            'EXEC dbo.usp_MonthlyRcptPay_CreditorsDebtors @PartyId=?, @Start=?, @End=?, @DateStyle=?, @OutflowNegative=?, @GroupList=?, @ExcludeVchTypes=?',
            [$partyId, $startDate, $endDate, $dateStyleDefault, 0, 'Sundry Creditors,Sundry Debtors', 'Rcpt,Pymt']
        );
        $r4 = DB::select(
            'EXEC dbo.usp_MonthlyCashBankFlow @PartyId=?, @Start=?, @End=?, @DateStyle=?, @GroupList=?, @ExcludeVchTypes=?',
            [$partyId, $startDate, $endDate, 23, 'Cash-in-Hand,Bank Accounts', null]
        );

        $sumInOut = function ($rows) {
            $in = 0.0;
            $out = 0.0;
            foreach ($rows as $r) {
                $in  += isset($r->cashIn)  ? (float)$r->cashIn  : 0.0;
                $out += isset($r->cashOut) ? (float)$r->cashOut : 0.0;
            }
            return [round($in, 2), round($out, 2)];
        };
        [$in1, $out1] = $sumInOut($r1);
        [$in2, $out2] = $sumInOut($r2);
        [$in3, $out3] = $sumInOut($r3);
        //[$in4, $out4] = $sumInOut($r4);
        $groups4 = 'Cash-in-Hand,Bank Accounts';
        // Get all vouchers up to end_date for these groups
        $closingRows = DB::select(
            'EXEC dbo.usp_ClosingBalanceCashBank @PartyId=?, @EndDate=?, @GroupList=?',
            [$partyId, $endDate, $groups4]
        );
        $closingMap = collect($closingRows)->mapWithKeys(function ($r) {
            return [trim($r->strGroupName) => (float)$r->Closing];
        })->all();

        $closingCash = $closingMap['Cash-in-Hand']  ?? 0.0;
        $closingBank = $closingMap['Bank Accounts'] ?? 0.0;


        // Fetch group ids (optional; skip if not needed)
        $gid = function ($name) use ($partyId) {
            $row = DB::table('GroupMaster')->where('strGroupName', $name)->where('iPartyId', $partyId)->first();
            return $row?->iGroupId;
        };

        // FY label
        $s = Carbon::createFromFormat('Y-m-d', $startDate, $tz);
        $e = Carbon::createFromFormat('Y-m-d', $endDate,   $tz);
        $fyLabel = 'FY ' . $s->format('Y') . '-' . substr($e->format('Y'), -2);

        return [
            'type'   => $type,
            'range'  => ['from' => $startDate, 'to' => $endDate],
            'fy_label' => $fyLabel,
            'months' => $months,
            'cashIn' => $cashIn,
            'cashOut' => $cashOut,
            'totals' => ['totalIn' => $totalInSelected, 'totalOut' => $totalOutSelected],
            'allTotals' => [
                'totalSale'     => ["iGroupId" => $gid('Sales Accounts'),     "value" => $in1],
                'totalPurchase' => ["iGroupId" => $gid('Purchase Accounts'),  "value" => $out1],
                'totalCredit'   => ["iGroupId" => $gid('Sundry Creditors'),   "value" => $in2],
                'totalDebit'    => ["iGroupId" => $gid('Sundry Debtors'),     "value" => $out2],
                'totalReceipt'  => ["iGroupId" => $gid('Sundry Creditors'),   "value" => $in3],
                'totalPayment'  => ["iGroupId" => $gid('Sundry Debtors'),     "value" => $out3],
                'totalCash'     => ["iGroupId" => $gid('Cash-in-Hand'),       "value" => $closingCash],
                'totalBank'     => ["iGroupId" => $gid('Bank Accounts'),      "value" => $closingBank],
            ],
        ];
    }

    public function monthlyGraphs(int $partyId, ?string $from, ?string $to, array $opts = []): array
    {
        $tz = 'Asia/Kolkata';
        $startDate = $this->parseToYmd($from, $tz);
        $endDate   = $this->parseToYmd($to,   $tz);
        //[$startDate, $endDate] = $this->defaultIndianFyRange($startDate, $endDate, $tz);

        $dateStyleDefault = 23;

        // --- helpers to fetch one series ---
        $sumInOut = function ($rows) {
            $in = 0.0;
            $out = 0.0;
            foreach ($rows as $r) {
                $in  += isset($r->cashIn)  ? (float)$r->cashIn  : 0.0;
                $out += isset($r->cashOut) ? (float)$r->cashOut : 0.0;
            }
            return [round($in, 2), round($out, 2)];
        };
        $normalize = function ($rows) {
            $months = [];
            $in = [];
            $out = [];
            foreach ($rows as $r) {
                $months[] = (string)($r->label ?? '');
                $in[]     = isset($r->cashIn)  ? (float)$r->cashIn  : 0.0;
                $out[]    = isset($r->cashOut) ? (float)$r->cashOut : 0.0;
            }
            return [$months, $in, $out];
        };

        // --- fetch four monthly series ---
        $sp = DB::select(
            'EXEC dbo.usp_VchMonthlyCashInOut @PartyId=?, @Start=?, @End=?, @DateStyle=?',
            [$partyId, $startDate, $endDate, $dateStyleDefault]
        );
        $cd = DB::select(
            'EXEC dbo.usp_MonthlyCashFlow_CreditorsDebtors @PartyId=?, @Start=?, @End=?, @DateStyle=?, @GroupList=?',
            [$partyId, $startDate, $endDate, $dateStyleDefault, 'Sundry Creditors,Sundry Debtors']
        );
        $rp = DB::select(
            'EXEC dbo.usp_MonthlyRcptPay_CreditorsDebtors @PartyId=?, @Start=?, @End=?, @DateStyle=?, @OutflowNegative=?, @GroupList=?, @ExcludeVchTypes=?',
            [$partyId, $startDate, $endDate, $dateStyleDefault, 0, 'Sundry Creditors,Sundry Debtors', 'Rcpt,Pymt']
        );
        $cb = DB::select(
            'EXEC dbo.usp_MonthlyCashBankFlow @PartyId=?, @Start=?, @End=?, @DateStyle=?, @GroupList=?, @ExcludeVchTypes=?',
            [$partyId, $startDate, $endDate, 23, 'Cash-in-Hand,Bank Accounts', null]
        );

        // normalize & totals per chart
        [$m1, $i1, $o1] = $normalize($sp);
        [$m2, $i2, $o2] = $normalize($cd);
        [$m3, $i3, $o3] = $normalize($rp);
        [$m4, $i4, $o4] = $normalize($cb);

        [$tin1, $tout1] = $sumInOut($sp);
        [$tin2, $tout2] = $sumInOut($cd);
        [$tin3, $tout3] = $sumInOut($rp);
        //[$tin4, $tout4] = $sumInOut($cb);
        $groups4 = ['groups', 'Cash-in-Hand,Bank Accounts'];
        // Get all vouchers up to end_date for these groups
        $closingRows = DB::select(
            'EXEC dbo.usp_ClosingBalanceCashBank @PartyId=?, @EndDate=?, @GroupList=?',
            [$partyId, $endDate, $groups4]
        );
        // dd($closingRows);
        //$series[4] = $closingRows; // will hold closing values instead of monthly series
        $closingMap = collect($closingRows)->mapWithKeys(function ($r) {
            return [trim($r->strGroupName) => (float)$r->Closing];
        })->all();

        $closingCash = $closingMap['Cash-in-Hand']  ?? 0.0;
        $closingBank = $closingMap['Bank Accounts'] ?? 0.0;
        // 8 summary tiles (same computation once)
        $gid = function ($name) use ($partyId) {
            $row = DB::table('GroupMaster')->where('strGroupName', $name)->where('iPartyId', $partyId)->first();
            return $row?->iGroupId;
        };

        $s = Carbon::createFromFormat('Y-m-d', $startDate, $tz);
        $e = Carbon::createFromFormat('Y-m-d', $endDate,   $tz);
        $fyLabel = 'FY ' . $s->format('Y') . '-' . substr($e->format('Y'), -2);

        return [
            'range'   => ['from' => $startDate, 'to' => $endDate],
            'fy_label' => $fyLabel,
            'charts'  => [
                ['key' => 'sp', 'title' => 'Sales vs Purchase',     'months' => $m1, 'in' => $i1, 'out' => $o1, 'totals' => ['in' => $tin1, 'out' => $tout1]],
                ['key' => 'cd', 'title' => 'Creditors vs Debtors',  'months' => $m2, 'in' => $i2, 'out' => $o2, 'totals' => ['in' => $tin2, 'out' => $tout2]],
                ['key' => 'rp', 'title' => 'Receipt vs Payment',    'months' => $m3, 'in' => $i3, 'out' => $o3, 'totals' => ['in' => $tin3, 'out' => $tout3]],
                ['key' => 'cb', 'title' => 'Cash vs Bank',          'months' => $m4, 'in' => $i4, 'out' => $o4, 'totals' => ['in' => $closingCash, 'out' => $closingBank]],
            ],
            'allTotals' => [
                'totalSale'     => ["iGroupId" => $gid('Sales Accounts'),     "value" => $tin1],
                'totalPurchase' => ["iGroupId" => $gid('Purchase Accounts'),  "value" => $tout1],
                'totalCredit'   => ["iGroupId" => $gid('Sundry Creditors'),   "value" => $tin2],
                'totalDebit'    => ["iGroupId" => $gid('Sundry Debtors'),     "value" => $tout2],
                'totalReceipt'  => ["iGroupId" => $gid('Sundry Creditors'),   "value" => $tin3],
                'totalPayment'  => ["iGroupId" => $gid('Sundry Debtors'),     "value" => $tout3],
                'totalCash'     => ["iGroupId" => $gid('Cash-in-Hand'),       "value" => $closingCash],
                'totalBank'     => ["iGroupId" => $gid('Bank Accounts'),      "value" => $closingBank],
            ],
        ];
    }

    private function fmt(float $n): string
    {
        // Simple 2-decimals; if you need Indian grouping, replace with your formatter.
        return number_format($n, 2, '.', '');
    }

    // public function getOpeningBalance(?string $partyguid = null, int $ledgerId, ?string $asOfDate = null): array
    // {
    //     try {
    //         if (!$asOfDate) {
    //             return ['balance' => 0.0, 'side' => 'Dr'];
    //         }

    //         // Get balance up to the day before the from date
    //         $rows = collect(DB::select(
    //             'EXEC dbo.GetVoucherHistoryByLedger ?, ?, ?, ?',
    //             [$partyguid, $ledgerId, null, $asOfDate]
    //         ));

    //         $openingBalance = 0.0;
    //         foreach ($rows as $row) {
    //             $drAmount = (float)($row->DRAmount ?? $row->DrAmount ?? 0);
    //             $crAmount = (float)($row->CRAmount ?? $row->CrAmount ?? 0);

    //             // DRAmount in minus, CRAmount in plus
    //             $openingBalance += (-$drAmount + $crAmount);
    //         }

    //         return [
    //             'balance' => abs($openingBalance),
    //             'side' => $openingBalance >= 0 ? 'Dr' : 'Cr'
    //         ];
    //     } catch (\Throwable $e) {
    //         Log::error('ReportsService::getOpeningBalance error', ['msg' => $e->getMessage()]);
    //         return ['balance' => 0.0, 'side' => 'Dr'];
    //     }
    // }

    public function getOpeningBalance(?string $partyguid = null, int $ledgerId, ?string $asOfDate = null): array
    {
        try {
            if (!$asOfDate) {
                return ['balance' => 0.0, 'side' => 'Dr'];
            }

            // Get the running balance from the last transaction before the asOfDate
            $result = collect(DB::select('
            SELECT TOP 1 decRunningBalance
            FROM VchHistory
            WHERE iLedgerId = ?
                AND PartyGUID = ?
                AND strVchDate < ?
            ORDER BY strVchDate DESC, iVchId DESC
        ', [$ledgerId, $partyguid, $asOfDate]));

            $openingBalance = $result->first()->decRunningBalance ?? 0.0;

            return [
                'balance' => abs($openingBalance),
                'side' => $openingBalance >= 0 ? 'Dr' : 'Cr'
            ];
        } catch (\Throwable $e) {
            Log::error('ReportsService::getOpeningBalance error', ['msg' => $e->getMessage()]);
            return ['balance' => 0.0, 'side' => 'Dr'];
        }
    }
}
