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


    // public function pandl(int $partyId, ?string $from = null, ?string $to = null): array
    // {
    //     try {
    //         // Convert incoming dates to Y-m-d (accept Y-m-d or d-m-Y or empty)
    //         $toYmd = function (?string $d): ?string {
    //             if (!$d) return null;
    //             // Carbon can parse both formats safely
    //             return Carbon::parse($d)->format('Y-m-d');
    //         };
    //         $startDate = $toYmd($from);
    //         $endDate   = $toYmd($to);

    //         if (!$startDate || !$endDate) {
    //             // today
    //             $today = Carbon::today();

    //             // Determine the financial year range
    //             if ($today->month < 4) {
    //                 // Jan, Feb, Mar → financial year started last year
    //                 $fyStart = Carbon::create($today->year - 1, 4, 1);
    //                 $fyEnd   = Carbon::create($today->year, 3, 31);
    //             } else {
    //                 // Apr to Dec → financial year started this year
    //                 $fyStart = Carbon::create($today->year, 4, 1);
    //                 $fyEnd   = Carbon::create($today->year + 1, 3, 31);
    //             }

    //             // Only override if not provided
    //             $startDate = $startDate ?: $fyStart->format('Y-m-d');
    //             $endDate   = $endDate   ?: $fyEnd->format('Y-m-d');
    //         }

    //         // Call your proc
    //         $rows = collect(DB::select('EXEC dbo.GetBalanceSheetBase ?, ?, ?', [
    //             $partyId,
    //             $startDate,
    //             $endDate
    //         ]));
    //         // dd($rows);
    //         // If nothing, still return a valid array
    //         if ($rows->isEmpty()) {
    //             return [
    //                 'success' => true,
    //                 'data' => [
    //                     'cr' => [],
    //                     'dr' => [],
    //                     'IndirectIncomes' => [],
    //                     'IndirectExpenses' => [],
    //                     'totalCr' => '0.00',
    //                     'totalDr' => '0.00',
    //                     'GrossPandL' => '0.00',
    //                     'NetPandL' => '0.00',
    //                 ],
    //             ];
    //         }

    //         $data = ['cr' => [], 'dr' => [], 'IndirectIncomes' => [], 'IndirectExpenses' => []];
    //         $totalCr = 0.0;
    //         $totalDr = 0.0;
    //         $totII = 0.0;
    //         $totIE = 0.0;

    //         foreach ($rows as $r) {
    //             $dr = (float)($r->DrAmount ?? 0);
    //             $cr = (float)($r->CRAmount ?? 0);

    //             switch ($r->strGroupName) {
    //                 case 'Sales Accounts':
    //                 case 'Direct Incomes':
    //                     $val = $cr - $dr;
    //                     $data['cr'][] = [
    //                         'iPrimaryGroupId' => $r->iPrimaryGroupId ?? null,
    //                         'strGroupName'    => $r->strGroupName ?? '',
    //                         'decMainAmount'   => $this->fmt($val),
    //                         'iPartyId'        => $r->iPartyId ?? null,
    //                         'iYearId'         => $r->iYearId ?? null,
    //                     ];
    //                     $totalCr += $val;
    //                     break;

    //                 case 'Purchase Accounts':
    //                 case 'Direct Expenses':
    //                     $neg = (-1 * $dr) - $cr; // negative
    //                     $pos = -1 * $neg;        // make positive
    //                     $data['dr'][] = [
    //                         'iPrimaryGroupId' => $r->iPrimaryGroupId ?? null,
    //                         'strGroupName'    => $r->strGroupName ?? '',
    //                         'decMainAmount'   => $this->fmt($pos),
    //                         'iPartyId'        => $r->iPartyId ?? null,
    //                         'iYearId'         => $r->iYearId ?? null,
    //                     ];
    //                     $totalDr += $pos;
    //                     break;

    //                 case 'Indirect Incomes':
    //                     $val = $cr - $dr;
    //                     $data['IndirectIncomes'][] = [
    //                         'iPrimaryGroupId' => $r->iPrimaryGroupId ?? null,
    //                         'strGroupName'    => $r->strGroupName ?? '',
    //                         'decMainAmount'   => $this->fmt($val),
    //                         'iPartyId'        => $r->iPartyId ?? null,
    //                         'iYearId'         => $r->iYearId ?? null,
    //                     ];
    //                     $totII += $val;
    //                     break;

    //                 case 'Indirect Expenses':
    //                     $neg = (-1 * $dr) - $cr;
    //                     $pos = -1 * $neg;
    //                     $data['IndirectExpenses'][] = [
    //                         'iPrimaryGroupId' => $r->iPrimaryGroupId ?? null,
    //                         'strGroupName'    => $r->strGroupName ?? '',
    //                         'decMainAmount'   => $this->fmt($pos),
    //                         'iPartyId'        => $r->iPartyId ?? null,
    //                         'iYearId'         => $r->iYearId ?? null,
    //                     ];
    //                     $totIE += $pos;
    //                     break;
    //             }
    //         }

    //         $gross = $totalCr - $totalDr;
    //         $net   = $gross + $totII - $totIE;

    //         $data['totalCr']    = $this->fmt($totalCr);
    //         $data['totalDr']    = $this->fmt($totalDr);
    //         $data['GrossPandL'] = $this->fmt($gross);
    //         $data['NetPandL']   = $this->fmt($net);

    //         return ['success' => true, 'data' => $data];
    //     } catch (\Throwable $e) {
    //         Log::error('ReportsService::pandl error', ['msg' => $e->getMessage()]);
    //         // Always return an array, even on error
    //         return [
    //             'success' => false,
    //             'message' => 'Failed to build P&L',
    //             'error'   => $e->getMessage(),
    //             'data'    => [
    //                 'cr' => [],
    //                 'dr' => [],
    //                 'IndirectIncomes' => [],
    //                 'IndirectExpenses' => [],
    //                 'totalCr' => '0.00',
    //                 'totalDr' => '0.00',
    //                 'GrossPandL' => '0.00',
    //                 'NetPandL' => '0.00',
    //             ],
    //         ];
    //     }
    // }

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
                        'OpeningStock' => '0.00',
                        'ClosingStock' => '0.00',
                        'COGS' => '0.00',
                    ],
                ];
            }

            $data = ['cr' => [], 'dr' => [], 'IndirectIncomes' => [], 'IndirectExpenses' => []];
            $totalCr = 0.0;
            $totalDr = 0.0;
            $totII = 0.0;
            $totIE = 0.0;

            // Get stock values from first row (they should be the same for all rows)
            $openingStock = (float)($rows[0]->OpeningStock ?? 0);
            $closingStock = (float)($rows[0]->ClosingStock ?? 0);

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

            // Calculate Cost of Goods Sold (COGS)
            $cogs = $openingStock + $totalDr - $closingStock;

            // Calculate Gross Profit (Sales - COGS)
            $gross = $totalCr - $cogs;
            $net   = $gross + $totII - $totIE;

            $data['totalCr']       = $this->fmt($totalCr);
            $data['totalDr']       = $this->fmt($totalDr);
            $data['GrossPandL']    = $this->fmt($gross);
            $data['NetPandL']      = $this->fmt($net);
            $data['OpeningStock']  = $this->fmt($openingStock);
            $data['ClosingStock']  = $this->fmt($closingStock);
            $data['COGS']          = $this->fmt($cogs);

            return ['success' => true, 'data' => $data];
        } catch (\Throwable $e) {
            Log::error('ReportsService::pandl error', ['msg' => $e->getMessage()]);
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
                    'OpeningStock' => '0.00',
                    'ClosingStock' => '0.00',
                    'COGS' => '0.00',
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


    // public function monthlyGraph(int $partyId, ?string $from, ?string $to, int $type = 1, array $opts = []): array
    // {
    //     $tz = 'Asia/Kolkata';
    //     $startDate = $this->parseToYmd($from, $tz);
    //     $endDate   = $this->parseToYmd($to,   $tz);
    //     [$startDate, $endDate] = $this->defaultIndianFyRange($startDate, $endDate, $tz);

    //     $dateStyleDefault = 23;

    //     // --- Fetch selected type series ---
    //     switch ($type) {
    //         case 1: // Sales & Purchase
    //             $selected = DB::select(
    //                 'EXEC dbo.usp_VchMonthlyCashInOut @PartyId=?, @Start=?, @End=?, @DateStyle=?',
    //                 [$partyId, $startDate, $endDate, $dateStyleDefault]
    //             );
    //             break;
    //         case 2: // Creditors & Debtors
    //             $groups2 = $opts['groups'] ?? 'Sundry Creditors,Sundry Debtors';
    //             $selected = DB::select(
    //                 'EXEC dbo.usp_MonthlyCashFlow_CreditorsDebtors @PartyId=?, @Start=?, @End=?, @DateStyle=?, @GroupList=?',
    //                 [$partyId, $startDate, $endDate, $dateStyleDefault, $groups2]
    //             );
    //             break;
    //         case 3: // Receipt & Payment
    //             $outflowNegative = !empty($opts['outflow_negative']) ? 1 : 0;
    //             $groups3       = $opts['groups']        ?? 'Sundry Creditors,Sundry Debtors';
    //             $excludeTypes3 = $opts['exclude_types'] ?? 'Rcpt,Pymt';
    //             $selected = DB::select(
    //                 'EXEC dbo.usp_MonthlyRcptPay_CreditorsDebtors @PartyId=?, @Start=?, @End=?, @DateStyle=?, @OutflowNegative=?, @GroupList=?, @ExcludeVchTypes=?',
    //                 [$partyId, $startDate, $endDate, $dateStyleDefault, $outflowNegative, $groups3, $excludeTypes3]
    //             );
    //             break;
    //         case 4: // Cash & Bank Flow
    //         default:
    //             $dateStyle4 = array_key_exists('date_style', $opts) ? $opts['date_style'] : 23;
    //             if ($dateStyle4 === '' || strtolower((string)$dateStyle4) === 'null') $dateStyle4 = null;
    //             $groups4       = $opts['groups']        ?? 'Cash-in-Hand,Bank Accounts';
    //             $excludeTypes4 = $opts['exclude_types'] ?? null;
    //             $selected = DB::select(
    //                 'EXEC dbo.usp_MonthlyCashBankFlow @PartyId=?, @Start=?, @End=?, @DateStyle=?, @GroupList=?, @ExcludeVchTypes=?',
    //                 [$partyId, $startDate, $endDate, $dateStyle4, $groups4, $excludeTypes4]
    //             );
    //             break;
    //     }

    //     // Normalize arrays
    //     $months  = [];
    //     $cashIn  = [];
    //     $cashOut = [];
    //     foreach ($selected as $r) {
    //         $months[]  = (string)($r->label ?? '');
    //         $cashIn[]  = isset($r->cashIn)  ? (float)$r->cashIn  : 0.0;
    //         $cashOut[] = isset($r->cashOut) ? (float)$r->cashOut : 0.0;
    //     }
    //     $totalInSelected  = round(array_sum($cashIn), 2);
    //     $totalOutSelected = round(array_sum($cashOut), 2);

    //     // ---- All eight totals (same as API) ----
    //     $r1 = DB::select(
    //         'EXEC dbo.usp_VchMonthlyCashInOut @PartyId=?, @Start=?, @End=?, @DateStyle=?',
    //         [$partyId, $startDate, $endDate, $dateStyleDefault]
    //     );
    //     $r2 = DB::select(
    //         'EXEC dbo.usp_MonthlyCashFlow_CreditorsDebtors @PartyId=?, @Start=?, @End=?, @DateStyle=?, @GroupList=?',
    //         [$partyId, $startDate, $endDate, $dateStyleDefault, 'Sundry Creditors,Sundry Debtors']
    //     );
    //     $r3 = DB::select(
    //         'EXEC dbo.usp_MonthlyRcptPay_CreditorsDebtors @PartyId=?, @Start=?, @End=?, @DateStyle=?, @OutflowNegative=?, @GroupList=?, @ExcludeVchTypes=?',
    //         [$partyId, $startDate, $endDate, $dateStyleDefault, 0, 'Sundry Creditors,Sundry Debtors', 'Rcpt,Pymt']
    //     );
    //     $r4 = DB::select(
    //         'EXEC dbo.usp_MonthlyCashBankFlow @PartyId=?, @Start=?, @End=?, @DateStyle=?, @GroupList=?, @ExcludeVchTypes=?',
    //         [$partyId, $startDate, $endDate, 23, 'Cash-in-Hand,Bank Accounts', null]
    //     );

    //     $sumInOut = function ($rows) {
    //         $in = 0.0;
    //         $out = 0.0;
    //         foreach ($rows as $r) {
    //             $in  += isset($r->cashIn)  ? (float)$r->cashIn  : 0.0;
    //             $out += isset($r->cashOut) ? (float)$r->cashOut : 0.0;
    //         }
    //         return [round($in, 2), round($out, 2)];
    //     };
    //     [$in1, $out1] = $sumInOut($r1);
    //     [$in2, $out2] = $sumInOut($r2);
    //     [$in3, $out3] = $sumInOut($r3);

    //     $groups4 = 'Cash-in-Hand,Bank Accounts';
    //     $closingRows = DB::select(
    //         'EXEC dbo.usp_ClosingBalanceCashBank @PartyId=?, @EndDate=?, @GroupList=?',
    //         [$partyId, $endDate, $groups4]
    //     );
    //     $closingMap = collect($closingRows)->mapWithKeys(function ($r) {
    //         return [trim($r->strGroupName) => (float)$r->Closing];
    //     })->all();

    //     $closingCash = $closingMap['Cash-in-Hand']  ?? 0.0;
    //     $closingBank = $closingMap['Bank Accounts'] ?? 0.0;

    //     // Fetch group ids and names for all available groups
    //     $availableGroups = DB::table('GroupMaster')
    //         ->where('iPartyId', $partyId)
    //         ->whereIn('strGroupName', [
    //             'Sales Accounts',
    //             'Purchase Accounts',
    //             'Sundry Creditors',
    //             'Sundry Debtors',
    //             'Cash-in-Hand',
    //             'Bank Accounts'
    //         ])
    //         ->select('iGroupId', 'strGroupName')
    //         ->get()
    //         ->mapWithKeys(function ($group) {
    //             return [$group->strGroupName => $group->iGroupId];
    //         })
    //         ->all();

    //     // Get user's selected groups (from session or database)
    //     $selectedGroups = $this->getUserSelectedGroups(auth()->id(), $partyId);

    //     // FY label
    //     $s = Carbon::createFromFormat('Y-m-d', $startDate, $tz);
    //     $e = Carbon::createFromFormat('Y-m-d', $endDate,   $tz);
    //     $fyLabel = 'FY ' . $s->format('Y') . '-' . substr($e->format('Y'), -2);

    //     return [
    //         'type'   => $type,
    //         'range'  => ['from' => $startDate, 'to' => $endDate],
    //         'fy_label' => $fyLabel,
    //         'months' => $months,
    //         'cashIn' => $cashIn,
    //         'cashOut' => $cashOut,
    //         'totals' => ['totalIn' => $totalInSelected, 'totalOut' => $totalOutSelected],
    //         'allTotals' => [
    //             'totalSale'     => ["iGroupId" => $availableGroups['Sales Accounts'] ?? null,     "value" => $in1, "name" => "Sales"],
    //             'totalPurchase' => ["iGroupId" => $availableGroups['Purchase Accounts'] ?? null,  "value" => $out1, "name" => "Purchase"],
    //             'totalCredit'   => ["iGroupId" => $availableGroups['Sundry Creditors'] ?? null,   "value" => $in2, "name" => "Creditors"],
    //             'totalDebit'    => ["iGroupId" => $availableGroups['Sundry Debtors'] ?? null,     "value" => $out2, "name" => "Debtors"],
    //             'totalReceipt'  => ["iGroupId" => $availableGroups['Sundry Creditors'] ?? null,   "value" => $in3, "name" => "Receipt"],
    //             'totalPayment'  => ["iGroupId" => $availableGroups['Sundry Debtors'] ?? null,     "value" => $out3, "name" => "Payment"],
    //             'totalCash'     => ["iGroupId" => $availableGroups['Cash-in-Hand'] ?? null,       "value" => $closingCash, "name" => "Cash"],
    //             'totalBank'     => ["iGroupId" => $availableGroups['Bank Accounts'] ?? null,      "value" => $closingBank, "name" => "Bank"],
    //         ],
    //         'availableGroups' => $availableGroups,
    //         'selectedGroups' => $selectedGroups,
    //     ];
    // }

    // public function monthlyGraph(int $partyId, ?string $from, ?string $to, int $type = 1, array $opts = []): array
    // {
    //     $tz = 'Asia/Kolkata';
    //     $startDate = $this->parseToYmd($from, $tz);
    //     $endDate   = $this->parseToYmd($to,   $tz);
    //     [$startDate, $endDate] = $this->defaultIndianFyRange($startDate, $endDate, $tz);

    //     $dateStyleDefault = 23;

    //     // --- Fetch selected type series ---
    //     switch ($type) {
    //         case 1: // Sales & Purchase
    //             $selected = DB::select(
    //                 'EXEC dbo.usp_VchMonthlyCashInOut @PartyId=?, @Start=?, @End=?, @DateStyle=?',
    //                 [$partyId, $startDate, $endDate, $dateStyleDefault]
    //             );
    //             break;
    //         case 2: // Creditors & Debtors
    //             $groups2 = $opts['groups'] ?? 'Sundry Creditors,Sundry Debtors';
    //             $selected = DB::select(
    //                 'EXEC dbo.usp_MonthlyCashFlow_CreditorsDebtors @PartyId=?, @Start=?, @End=?, @DateStyle=?, @GroupList=?',
    //                 [$partyId, $startDate, $endDate, $dateStyleDefault, $groups2] // <- single string
    //             );
    //             break;
    //         case 3: // Receipt & Payment
    //             $outflowNegative = !empty($opts['outflow_negative']) ? 1 : 0;
    //             $groups3       = $opts['groups']        ?? 'Sundry Creditors,Sundry Debtors';
    //             $excludeTypes3 = $opts['exclude_types'] ?? 'Rcpt,Pymt';
    //             $selected = DB::select(
    //                 'EXEC dbo.usp_MonthlyRcptPay_CreditorsDebtors @PartyId=?, @Start=?, @End=?, @DateStyle=?, @OutflowNegative=?, @GroupList=?, @ExcludeVchTypes=?',
    //                 [$partyId, $startDate, $endDate, $dateStyleDefault, $outflowNegative, $groups3, $excludeTypes3]
    //             );
    //             break;
    //         case 4: // Cash & Bank Flow
    //         default:
    //             $dateStyle4 = array_key_exists('date_style', $opts) ? $opts['date_style'] : 23;
    //             if ($dateStyle4 === '' || strtolower((string)$dateStyle4) === 'null') $dateStyle4 = null;
    //             $groups4       = $opts['groups']        ?? 'Cash-in-Hand,Bank Accounts';
    //             $excludeTypes4 = $opts['exclude_types'] ?? null;
    //             $selected = DB::select(
    //                 'EXEC dbo.usp_MonthlyCashBankFlow @PartyId=?, @Start=?, @End=?, @DateStyle=?, @GroupList=?, @ExcludeVchTypes=?',
    //                 [$partyId, $startDate, $endDate, $dateStyle4, $groups4, $excludeTypes4]
    //             );
    //             break;
    //     }

    //     // --- Normalize arrays ---
    //     $months  = $cashIn = $cashOut = [];
    //     $prevMonthIn = $prevMonthOut = [];
    //     $prevQuarterIn = $prevQuarterOut = [];
    //     $prevYearIn = $prevYearOut = [];
    //     $budgetIn = $budgetOut = [];
    //     $forecastIn = $forecastOut = [];
    //     $cashflowIn = $cashflowOut = [];
    //     $plIn = $plOut = [];

    //     foreach ($selected as $r) {
    //         $months[]       = (string)($r->label ?? '');
    //         $cashIn[]       = isset($r->cashIn)  ? (float)$r->cashIn  : 0.0;
    //         $cashOut[]      = isset($r->cashOut) ? (float)$r->cashOut : 0.0;

    //         $prevMonthIn[]   = isset($r->prevMonthIn)  ? (float)$r->prevMonthIn  : 0.0;
    //         $prevMonthOut[]  = isset($r->prevMonthOut) ? (float)$r->prevMonthOut : 0.0;
    //         $prevQuarterIn[]  = isset($r->prevQuarterIn)  ? (float)$r->prevQuarterIn  : 0.0;
    //         $prevQuarterOut[] = isset($r->prevQuarterOut) ? (float)$r->prevQuarterOut : 0.0;
    //         $prevYearIn[]   = isset($r->prevYearIn)  ? (float)$r->prevYearIn  : 0.0;
    //         $prevYearOut[]  = isset($r->prevYearOut) ? (float)$r->prevYearOut : 0.0;

    //         $budgetIn[]     = isset($r->budgetIn)  ? (float)$r->budgetIn  : 0.0;
    //         $budgetOut[]    = isset($r->budgetOut) ? (float)$r->budgetOut : 0.0;
    //         $forecastIn[]   = isset($r->forecastIn)  ? (float)$r->forecastIn  : 0.0;
    //         $forecastOut[]  = isset($r->forecastOut) ? (float)$r->forecastOut : 0.0;
    //         $cashflowIn[]   = isset($r->cashflowIn)  ? (float)$r->cashflowIn  : 0.0;
    //         $cashflowOut[]  = isset($r->cashflowOut) ? (float)$r->cashflowOut : 0.0;
    //         $plIn[]         = isset($r->plIn)  ? (float)$r->plIn  : 0.0;
    //         $plOut[]        = isset($r->plOut) ? (float)$r->plOut : 0.0;
    //     }

    //     $totalInSelected  = round(array_sum($cashIn), 2);
    //     $totalOutSelected = round(array_sum($cashOut), 2);

    //     // --- Totals for previous periods ---
    //     $sumTotals = fn($arr) => round(array_sum($arr), 2);

    //     $totalsPrev = [
    //         'prevMonthIn'  => $sumTotals($prevMonthIn),
    //         'prevMonthOut' => $sumTotals($prevMonthOut),
    //         'prevQuarterIn'  => $sumTotals($prevQuarterIn),
    //         'prevQuarterOut' => $sumTotals($prevQuarterOut),
    //         'prevYearIn'  => $sumTotals($prevYearIn),
    //         'prevYearOut' => $sumTotals($prevYearOut),
    //         'budgetIn'    => $sumTotals($budgetIn),
    //         'budgetOut'   => $sumTotals($budgetOut),
    //         'forecastIn'  => $sumTotals($forecastIn),
    //         'forecastOut' => $sumTotals($forecastOut),
    //         'cashflowIn'  => $sumTotals($cashflowIn),
    //         'cashflowOut' => $sumTotals($cashflowOut),
    //         'plIn'        => $sumTotals($plIn),
    //         'plOut'       => $sumTotals($plOut),
    //     ];

    //     // --- Fetch closing balances ---
    //     $groups4 = 'Cash-in-Hand,Bank Accounts';
    //     $closingRows = DB::select(
    //         'EXEC dbo.usp_ClosingBalanceCashBank @PartyId=?, @EndDate=?, @GroupList=?',
    //         [$partyId, $endDate, $groups4]
    //     );
    //     $closingMap = collect($closingRows)->mapWithKeys(fn($r) => [trim($r->strGroupName) => (float)$r->Closing])->all();

    //     $closingCash = $closingMap['Cash-in-Hand']  ?? 0.0;
    //     $closingBank = $closingMap['Bank Accounts'] ?? 0.0;

    //     // --- Available groups ---
    //     $availableGroups = DB::table('GroupMaster')
    //         ->where('iPartyId', $partyId)
    //         ->whereIn('strGroupName', [
    //             'Sales Accounts',
    //             'Purchase Accounts',
    //             'Sundry Creditors',
    //             'Sundry Debtors',
    //             'Cash-in-Hand',
    //             'Bank Accounts'
    //         ])
    //         ->select('iGroupId', 'strGroupName')
    //         ->get()
    //         ->mapWithKeys(fn($group) => [$group->strGroupName => $group->iGroupId])
    //         ->all();

    //     $selectedGroups = $this->getUserSelectedGroups(auth()->id(), $partyId);

    //     // --- FY Label ---
    //     $s = Carbon::createFromFormat('Y-m-d', $startDate, $tz);
    //     $e = Carbon::createFromFormat('Y-m-d', $endDate,   $tz);
    //     $fyLabel = 'FY ' . $s->format('Y') . '-' . substr($e->format('Y'), -2);

    //     return [
    //         'type'          => $type,
    //         'range'         => ['from' => $startDate, 'to' => $endDate],
    //         'fy_label'      => $fyLabel,
    //         'months'        => $months,
    //         'cashIn'        => $cashIn,
    //         'cashOut'       => $cashOut,
    //         'totals'        => ['totalIn' => $totalInSelected, 'totalOut' => $totalOutSelected],
    //         'totalsPrev'    => $totalsPrev,
    //         'allTotals'     => [
    //             'totalSale'     => ["iGroupId" => $availableGroups['Sales Accounts'] ?? null,     "value" => $totalInSelected, "name" => "Sales"],
    //             'totalPurchase' => ["iGroupId" => $availableGroups['Purchase Accounts'] ?? null,  "value" => $totalOutSelected, "name" => "Purchase"],
    //             'totalCredit'   => ["iGroupId" => $availableGroups['Sundry Creditors'] ?? null, "value" => $totalsPrev['prevMonthIn'], "name" => "Creditors"],
    //             'totalDebit'    => ["iGroupId" => $availableGroups['Sundry Debtors'] ?? null,   "value" => $totalsPrev['prevMonthOut'], "name" => "Debtors"],
    //             'totalCash'     => ["iGroupId" => $availableGroups['Cash-in-Hand'] ?? null,     "value" => $closingCash, "name" => "Cash"],
    //             'totalBank'     => ["iGroupId" => $availableGroups['Bank Accounts'] ?? null,    "value" => $closingBank, "name" => "Bank"],
    //         ],
    //         'availableGroups' => $availableGroups,
    //         'selectedGroups'  => $selectedGroups,
    //     ];
    // }

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

        // DEBUG: Check available fields
        if (!empty($selected)) {
            $firstRow = (array)$selected[0];
            \Log::info('STORED PROCEDURE FIELDS:', array_keys($firstRow));
        }

        // --- Normalize arrays ---
        $months  = $cashIn = $cashOut = [];
        $prevMonthIn = $prevMonthOut = [];
        $prevQuarterIn = $prevQuarterOut = [];
        $prevYearIn = $prevYearOut = [];
        $budgetIn = $budgetOut = [];
        $forecastIn = $forecastOut = [];
        $cashflowIn = $cashflowOut = [];
        $plIn = $plOut = [];

        foreach ($selected as $r) {
            $rowArray = (array)$r;

            $months[]       = (string)($r->label ?? '');
            $cashIn[]       = isset($r->cashIn)  ? (float)$r->cashIn  : 0.0;
            $cashOut[]      = isset($r->cashOut) ? (float)$r->cashOut : 0.0;

            // Map previous year data - using exact field names from stored procedure
            $prevYearIn[]   = isset($r->prevYearIn)  ? (float)$r->prevYearIn  : 0.0;
            $prevYearOut[]  = isset($r->prevYearOut) ? (float)$r->prevYearOut : 0.0;

            // Map other comparison data
            $prevMonthIn[]   = isset($r->prevMonthIn)  ? (float)$r->prevMonthIn  : 0.0;
            $prevMonthOut[]  = isset($r->prevMonthOut) ? (float)$r->prevMonthOut : 0.0;
            $prevQuarterIn[]  = isset($r->prevQuarterIn)  ? (float)$r->prevQuarterIn  : 0.0;
            $prevQuarterOut[] = isset($r->prevQuarterOut) ? (float)$r->prevQuarterOut : 0.0;

            $budgetIn[]     = isset($r->budgetIn)  ? (float)$r->budgetIn  : 0.0;
            $budgetOut[]    = isset($r->budgetOut) ? (float)$r->budgetOut : 0.0;
            $forecastIn[]   = isset($r->forecastIn)  ? (float)$r->forecastIn  : 0.0;
            $forecastOut[]  = isset($r->forecastOut) ? (float)$r->forecastOut : 0.0;
            $cashflowIn[]   = isset($r->cashflowIn)  ? (float)$r->cashflowIn  : 0.0;
            $cashflowOut[]  = isset($r->cashflowOut) ? (float)$r->cashflowOut : 0.0;
            $plIn[]         = isset($r->plIn)  ? (float)$r->plIn  : 0.0;
            $plOut[]        = isset($r->plOut) ? (float)$r->plOut : 0.0;

            // DEBUG: Log each row's previous year data
            \Log::info("Row {$r->label}: prevYearIn = " . ($r->prevYearIn ?? 'NULL') . ", prevYearOut = " . ($r->prevYearOut ?? 'NULL'));
        }

        // DEBUG: Log final arrays
        \Log::info('FINAL ARRAYS:', [
            'months_count' => count($months),
            'prevYearIn_count' => count($prevYearIn),
            'prevYearOut_count' => count($prevYearOut),
            'prevYearIn_sum' => array_sum($prevYearIn),
            'prevYearOut_sum' => array_sum($prevYearOut)
        ]);

        $totalInSelected  = round(array_sum($cashIn), 2);
        $totalOutSelected = round(array_sum($cashOut), 2);

        // --- Totals for previous periods ---
        $sumTotals = fn($arr) => round(array_sum($arr), 2);

        $totalsPrev = [
            'prevMonthIn'  => $sumTotals($prevMonthIn),
            'prevMonthOut' => $sumTotals($prevMonthOut),
            'prevQuarterIn'  => $sumTotals($prevQuarterIn),
            'prevQuarterOut' => $sumTotals($prevQuarterOut),
            'prevYearIn'  => $sumTotals($prevYearIn),
            'prevYearOut' => $sumTotals($prevYearOut),
            'budgetIn'    => $sumTotals($budgetIn),
            'budgetOut'   => $sumTotals($budgetOut),
            'forecastIn'  => $sumTotals($forecastIn),
            'forecastOut' => $sumTotals($forecastOut),
            'cashflowIn'  => $sumTotals($cashflowIn),
            'cashflowOut' => $sumTotals($cashflowOut),
            'plIn'        => $sumTotals($plIn),
            'plOut'       => $sumTotals($plOut),
        ];

        // --- Fetch closing balances ---
        $groups4 = 'Cash-in-Hand,Bank Accounts';
        $closingRows = DB::select(
            'EXEC dbo.usp_ClosingBalanceCashBank @PartyId=?, @EndDate=?, @GroupList=?',
            [$partyId, $endDate, $groups4]
        );
        $closingMap = collect($closingRows)->mapWithKeys(fn($r) => [trim($r->strGroupName) => (float)$r->Closing])->all();

        $closingCash = $closingMap['Cash-in-Hand']  ?? 0.0;
        $closingBank = $closingMap['Bank Accounts'] ?? 0.0;

        // --- Available groups ---
        $availableGroups = DB::table('GroupMaster')
            ->where('iPartyId', $partyId)
            ->whereIn('strGroupName', [
                'Sales Accounts',
                'Purchase Accounts',
                'Sundry Creditors',
                'Sundry Debtors',
                'Cash-in-Hand',
                'Bank Accounts'
            ])
            ->select('iGroupId', 'strGroupName')
            ->get()
            ->mapWithKeys(fn($group) => [$group->strGroupName => $group->iGroupId])
            ->all();

        $selectedGroups = $this->getUserSelectedGroups(auth()->id(), $partyId);

        // --- FY Label ---
        $s = Carbon::createFromFormat('Y-m-d', $startDate, $tz);
        $e = Carbon::createFromFormat('Y-m-d', $endDate,   $tz);
        $fyLabel = 'FY ' . $s->format('Y') . '-' . substr($e->format('Y'), -2);

        return [
            'type'          => $type,
            'range'         => ['from' => $startDate, 'to' => $endDate],
            'fy_label'      => $fyLabel,
            'months'        => $months,
            'cashIn'        => $cashIn,
            'cashOut'       => $cashOut,
            'prevMonthIn'   => $prevMonthIn,
            'prevMonthOut'  => $prevMonthOut,
            'prevQuarterIn'  => $prevQuarterIn,
            'prevQuarterOut' => $prevQuarterOut,
            'prevYearIn'    => $prevYearIn,
            'prevYearOut'   => $prevYearOut,
            'budgetIn'      => $budgetIn,
            'budgetOut'     => $budgetOut,
            'forecastIn'    => $forecastIn,
            'forecastOut'   => $forecastOut,
            'cashflowIn'    => $cashflowIn,
            'cashflowOut'   => $cashflowOut,
            'plIn'          => $plIn,
            'plOut'         => $plOut,
            'totals'        => ['totalIn' => $totalInSelected, 'totalOut' => $totalOutSelected],
            'totalsPrev'    => $totalsPrev,
            'allTotals'     => [
                'totalSale'     => ["iGroupId" => $availableGroups['Sales Accounts'] ?? null,     "value" => $totalInSelected, "name" => "Sales"],
                'totalPurchase' => ["iGroupId" => $availableGroups['Purchase Accounts'] ?? null,  "value" => $totalOutSelected, "name" => "Purchase"],
                'totalCredit'   => ["iGroupId" => $availableGroups['Sundry Creditors'] ?? null, "value" => $totalsPrev['prevMonthIn'], "name" => "Creditors"],
                'totalDebit'    => ["iGroupId" => $availableGroups['Sundry Debtors'] ?? null,   "value" => $totalsPrev['prevMonthOut'], "name" => "Debtors"],
                'totalCash'     => ["iGroupId" => $availableGroups['Cash-in-Hand'] ?? null,     "value" => $closingCash, "name" => "Cash"],
                'totalBank'     => ["iGroupId" => $availableGroups['Bank Accounts'] ?? null,    "value" => $closingBank, "name" => "Bank"],
            ],
            'availableGroups' => $availableGroups,
            'selectedGroups'  => $selectedGroups,
        ];
    }


    // Helper method to get/set user's selected groups
    private function getUserSelectedGroups($userId, $partyId = null)
    {
        // First try to get from database
        $preferences = DB::table('user_card_preferences')
            ->where('user_id', $userId)
            ->where('party_id', $partyId ?? $userId)
            ->first();

        if ($preferences && $preferences->selected_groups) {
            $selectedGroups = json_decode($preferences->selected_groups, true);
            // Ensure all group IDs are integers
            $selectedGroups = array_map('intval', $selectedGroups);

            // Update session for consistency
            $sessionKey = "user_{$userId}_selected_groups";
            session([$sessionKey => $selectedGroups]);

            return $selectedGroups;
        }

        // Try session
        $sessionKey = "user_{$userId}_selected_groups";
        if (session()->has($sessionKey)) {
            return session($sessionKey);
        }

        // Default to all available financial groups
        $defaultGroups = DB::table('GroupMaster')
            ->where('iPartyId', $partyId ?? $userId)
            ->whereIn('strGroupName', [
                'Sales Accounts',
                'Purchase Accounts',
                'Sundry Creditors',
                'Sundry Debtors',
                'Cash-in-Hand',
                'Bank Accounts',
                'Direct Incomes',
                'Direct Expenses'
            ])
            ->pluck('iGroupId')
            ->map('intval')
            ->toArray();

        session([$sessionKey => $defaultGroups]);
        return $defaultGroups;
    }

    public function getGroupsClosingBalances(int $partyId, array $groupIds, ?string $from = null, ?string $to = null): array
    {
        $allGroups = $this->getAllGroupsWithBalances($partyId, $from, $to);

        return collect($allGroups)
            ->whereIn('iGroupId', $groupIds)
            ->map(function ($group) {
                return [
                    'iGroupId' => (int)$group->iGroupId,
                    'strGroupName' => $group->strGroupName,
                    'Closing' => (float)($group->Closing ?? 0),
                    'Opening' => (float)($group->Opening ?? 0),
                ];
            })
            ->values()
            ->toArray();
    }

    public function getAllGroupsWithBalances(int $partyId, ?string $from = null, ?string $to = null): array
    {
        $tz = 'Asia/Kolkata';
        $startDate = $this->parseToYmd($from, $tz);
        $endDate = $this->parseToYmd($to, $tz);

        // Use default range if not provided
        if (!$startDate || !$endDate) {
            [$startDate, $endDate] = $this->defaultIndianFyRange($startDate, $endDate, $tz);
        }

        //try {
        $results = DB::select(
            'EXEC dbo.usp_GetAllGroupsWithClosingBalances @PartyId=?, @StartDate=?, @EndDate=?',
            [$partyId, $startDate, $endDate]
        );

        \Log::info('Stored procedure results:', ['count' => count($results), 'partyId' => $partyId]);

        // If no results, try to get basic groups without balances
        if (empty($results)) {
            \Log::warning('Stored procedure returned no results, fetching basic groups');
            $results = DB::table('GroupMaster')
                ->where('iPartyId', $partyId)
                ->select('iGroupId', 'strGroupName')
                ->orderBy('strGroupName')
                ->get()
                ->map(function ($group) {
                    // Add zero balances
                    $group->Closing = 0;
                    $group->Opening = 0;
                    return $group;
                })
                ->toArray();
        }

        return $results;
        // } catch (\Exception $e) {
        //     \Log::error('Error in getAllGroupsWithBalances: ' . $e->getMessage());

        //     // Fallback: return basic groups with zero balances
        //     $results = DB::table('GroupMaster')
        //         ->where('iPartyId', $partyId)
        //         ->select('iGroupId', 'strGroupName')
        //         ->orderBy('strGroupName')
        //         ->get()
        //         ->map(function ($group) {
        //             $group->Closing = 0;
        //             $group->Opening = 0;
        //             return $group;
        //         })
        //         ->toArray();

        //     return $results;
        // }
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
