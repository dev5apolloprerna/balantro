<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ReportsService
{
    private function looksCumulative(array $series): bool
    {
        $values = array_values(array_map('floatval', $series));
        if (count($values) < 2) {
            return false;
        }

        $hasDecrease = false;
        $hasIncrease = false;
        foreach ($values as $i => $v) {
            if ($i === 0) {
                continue;
            }
            if ($v < $values[$i - 1]) {
                $hasDecrease = true;
            }
            if ($v > $values[$i - 1]) {
                $hasIncrease = true;
            }
        }

        return $hasIncrease && !$hasDecrease;
    }

    private function toMonthlyFromCumulative(array $series): array
    {
        $values = array_values(array_map('floatval', $series));
        if (empty($values)) {
            return [];
        }

        $monthly = [];
        foreach ($values as $i => $value) {
            if ($i === 0) {
                $monthly[] = $value;
                continue;
            }
            $monthly[] = $value - $values[$i - 1];
        }
        return $monthly;
    }

    private function normalizeIfCumulative(array $series): array
    {
        return $this->looksCumulative($series)
            ? $this->toMonthlyFromCumulative($series)
            : $series;
    }

    private function isZeroSeries(array $series): bool
    {
        foreach ($series as $value) {
            if (abs((float)$value) > 0.000001) {
                return false;
            }
        }
        return true;
    }

    private function previousMonthSeriesFromCurrent(array $series): array
    {
        $values = array_values(array_map('floatval', $series));
        if (empty($values)) {
            return [];
        }

        $previous = [0.0];
        for ($i = 1; $i < count($values); $i++) {
            $previous[] = $values[$i - 1];
        }

        return $previous;
    }

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
            $toYmd = function (?string $d): ?string {
                if (!$d) return null;
                return Carbon::parse($d)->format('Y-m-d');
            };

            $startDate = $toYmd($from);
            $endDate   = $toYmd($to);

            if (!$startDate || !$endDate) {
                $today = Carbon::today();
                if ($today->month < 4) {
                    $fyStart = Carbon::create($today->year - 1, 4, 1);
                    $fyEnd   = Carbon::create($today->year, 3, 31);
                } else {
                    $fyStart = Carbon::create($today->year, 4, 1);
                    $fyEnd   = Carbon::create($today->year + 1, 3, 31);
                }
                $startDate = $startDate ?: $fyStart->format('Y-m-d');
                $endDate   = $endDate   ?: $fyEnd->format('Y-m-d');
            }

            $rows = collect(DB::select('EXEC dbo.GetBalanceSheetBase_new ?, ?, ?', [
                $partyId,
                $startDate,
                $endDate
            ]));
            
            if ($rows->isEmpty()) {
                return ['success' => true, 'data' => $this->emptyPandL()];
            }

            $data = [
                'cr' => [],
                'dr' => [],
                'IndirectIncomes' => [],
                'IndirectExpenses' => []
            ];

            $totalCr = $totalDr = $totII = $totIE = 0.0;
            $openingStock = 0.0;
            $closingStock = 0.0;
            // dd($rows);
            foreach ($rows as $r) {
                $dr = (float)($r->DrAmount ?? 0);
                $cr = (float)($r->CRAmount ?? 0);

                /* -------- STOCK ROWS -------- */
                // if ($r->strGroupName === 'Opening Stock') {
                //     $openingStock = $dr;
                //     continue;
                // }

                // if ($r->strGroupName === 'Closing Stock') {
                //     $closingStock = $cr;
                //     continue;
                // }
                /* -------- STOCK ROWS -------- */
                /* -------- STOCK ROWS -------- */
                /* -------- STOCK ROWS -------- */
 
                // if ($r->strGroupName === 'Opening Stock') {
                //     // Opening stock: Positive in DR, Negative in CR
                //     // From SP: DrAmount = 0, CRAmount = -1671012.04
                //     // So opening stock = DrAmount - CRAmount = 0 - (-1671012.04) = 1671012.04
                //     $val = $dr - $cr;
                //     $openingStock = $val;

                //     // Add to data array for blade
                //     $data['OpeningStock'] = $this->fmt($val);
                //     continue;
                // }

                if ($r->strGroupName === 'Opening Stock') {
                    $val = abs($dr + $cr);
                    $openingStock = $val;
                    $data['OpeningStock'] = $this->fmt($val);
                    continue;
                }

                // if ($r->strGroupName === 'Closing Stock') {
                //     // Closing stock: Positive in CR, Negative in DR
                //     // From SP: DrAmount = -6941387.19, CRAmount = 0
                //     // So closing stock = CRAmount - DrAmount = 0 - (-6941387.19) = 6941387.19
                //     $val = $cr - $dr;
                //     $closingStock = $val;

                //     // Add to data array for blade
                //     $data['ClosingStock'] = $this->fmt($val);
                //     continue;
                // }

                // if ($r->strGroupName === 'Closing Stock') {
                //     $val = abs($dr + $cr);
                //     $closingStock = $val;
                //     $data['ClosingStock'] = $this->fmt($val);
                //     continue;
                // }
                if ($r->strGroupName === 'Closing Stock') {
                    $closingStock = abs($dr + $cr);
                    $data['ClosingStock'] = $closingStock;
                    continue;
                }

                /* -------- DIRECT INCOME -------- */
                // if (in_array($r->strGroupName, ['Sales Accounts', 'Direct Incomes'])) {
                    
                //     $val = $cr - $dr;
                //     //$val = $dr - $cr;
                   
                //     $data['cr'][] = [
                //         'iPrimaryGroupId' => $r->iPrimaryGroupId,
                //         'strGroupName'    => $r->strGroupName,
                //         'decMainAmount'   => $this->fmt($val),
                //         'iPartyId'        => $r->iPartyId,
                //         'iYearId'         => $r->iYearId,
                //     ];
                //     $totalCr += $val;
                //     continue;
                // }
                // dd($r);
                if (in_array($r->strGroupName, ['Sales Accounts', 'Direct Incomes'])) {
                $val = $cr + $dr; // ✅ correct
                        // if(abs($dr) > abs($cr)){
                        // if(abs($cr) < abs($dr)){
                        //     $val= -1 * $val;
                        // }    
                if($r->strGroupName == "Sales Accounts"){
                        $GroupMasters = DB::table('GroupMaster')
                            ->where('strGroupName', 'Sales Accounts')
                            ->where('iPartyId', $r->iPartyId)
                            ->first();
                        if($dr > 0){ $val= -1 * $val; }
                        $data['cr'][] = [
                            'iPrimaryGroupId' => $GroupMasters->iGroupId,
                            'strGroupName'    => $r->strGroupName,
                            'decMainAmount'   => $this->fmt($val),
                            'iPartyId'        => $r->iPartyId,
                            'iYearId'         => $r->iYearId,
                        ];
                        
                    } else {
                        $GroupMasters = DB::table('GroupMaster')
                            ->where('strGroupName', 'Direct Incomes')
                            ->where('iPartyId', $r->iPartyId)
                            ->first();
                        if($dr > 0){ $val= -1 * $val; }
                        $data['cr'][] = [
                            'iPrimaryGroupId' => $GroupMasters->iGroupId,
                            'strGroupName'    => $r->strGroupName,
                            'decMainAmount'   => $this->fmt($val),
                            'iPartyId'        => $r->iPartyId,
                            'iYearId'         => $r->iYearId,
                        ];
                    }
                    $totalCr += $val;
                    continue;
                }

                /* -------- DIRECT EXPENSE -------- */
                // if (in_array($r->strGroupName, ['Purchase Accounts', 'Direct Expenses'])) {
                //     $pos = abs($dr + $cr);
                //     $data['dr'][] = [
                //         'iPrimaryGroupId' => $r->iPrimaryGroupId,
                //         'strGroupName'    => $r->strGroupName,
                //         'decMainAmount'   => $this->fmt($pos),
                //         'iPartyId'        => $r->iPartyId,
                //         'iYearId'         => $r->iYearId,
                //     ];
                //     $totalDr += $pos;
                //     continue;
                // }

                if (in_array($r->strGroupName, ['Purchase Accounts', 'Direct Expenses'])) {

                    $val = abs($dr + $cr);
                    if(abs($cr) > abs($dr)){
                        $val= -1 * $val;
                    }
                    if($r->strGroupName == "Purchase Accounts"){
                        $GroupMasters = DB::table('GroupMaster')
                            ->where('strGroupName', 'Purchase Accounts')
                            ->where('iPartyId', $r->iPartyId)
                            ->first();
                        $data['dr'][] = [
                            'iPrimaryGroupId' => $GroupMasters->iGroupId,
                            'strGroupName'    => $r->strGroupName,
                            'decMainAmount'   => $this->fmt($val),
                            'iPartyId'        => $r->iPartyId,
                            'iYearId'         => $r->iYearId,
                        ];
                    } else {
                        $GroupMasters = DB::table('GroupMaster')
                            ->where('strGroupName', 'Direct Expenses')
                            ->where('iPartyId', $r->iPartyId)
                            ->first();
                        $data['dr'][] = [
                            'iPrimaryGroupId' => $GroupMasters->iGroupId,
                            'strGroupName'    => $r->strGroupName,
                            'decMainAmount'   => $this->fmt($val),
                            'iPartyId'        => $r->iPartyId,
                            'iYearId'         => $r->iYearId,
                        ];
                    }   
                    $totalDr += $val;
                    continue;
                }

                /* -------- INDIRECT INCOME -------- */
                // if ($r->strGroupName === 'Indirect Incomes') {
                   
                //     $val = $cr - (-1 * $dr);
                //     $data['IndirectIncomes'][] = [
                //         'iPrimaryGroupId' => $r->iPrimaryGroupId,
                //         'strGroupName'    => $r->strGroupName,
                //         'decMainAmount'   => $this->fmt($val),
                //         'iPartyId'        => $r->iPartyId,
                //         'iYearId'         => $r->iYearId,
                //     ];
                //     $totII += $val;
                //     continue;
                // }

                if ($r->strGroupName === 'Indirect Incomes') {
                    $GroupMasters = DB::table('GroupMaster')
                            ->where('strGroupName', 'Indirect Incomes')
                            ->where('iPartyId', $r->iPartyId)
                            ->first();
                    $val = $cr + $dr;
                    if($dr > 0){ $val= -1 * $val; }
                    $data['IndirectIncomes'][] = [
                        'iPrimaryGroupId' => $GroupMasters->iGroupId,
                        'strGroupName'    => $r->strGroupName,
                        'decMainAmount'   => $this->fmt($val),
                        'iPartyId'        => $r->iPartyId,
                        'iYearId'         => $r->iYearId,
                    ];

                    $totII += $val;
                    continue;
                }

                /* -------- INDIRECT EXPENSE -------- */
                // if ($r->strGroupName === 'Indirect Expenses') {
                //     $pos = abs($dr + $cr);
                //     $data['IndirectExpenses'][] = [
                //         'iPrimaryGroupId' => $r->iPrimaryGroupId,
                //         'strGroupName'    => $r->strGroupName,
                //         'decMainAmount'   => $this->fmt($pos),
                //         'iPartyId'        => $r->iPartyId,
                //         'iYearId'         => $r->iYearId,
                //     ];
                //     $totIE += $pos;
                // }

                if ($r->strGroupName === 'Indirect Expenses') {

                    $val = abs($dr + $cr);
                    if(abs($cr) > abs($dr)){
                        $val= -1 * $val;
                    }
                    $GroupMasters = DB::table('GroupMaster')
                            ->where('strGroupName', 'Indirect Expenses')
                            ->where('iPartyId', $r->iPartyId)
                            ->first();
                    $data['IndirectExpenses'][] = [
                        'iPrimaryGroupId' => $GroupMasters->iGroupId,
                        'strGroupName'    => $r->strGroupName,
                        'decMainAmount'   => $this->fmt($val),
                        'iPartyId'        => $r->iPartyId,
                        'iYearId'         => $r->iYearId,
                    ];

                    $totIE += $val;
                }
            }

            /* -------- CALCULATIONS -------- */
            // $cogs  = $openingStock + $totalDr - $closingStock;
            /* -------- CALCULATIONS -------- */

            if ($closingStock > 0) {
                $cogs =
                    abs($openingStock)
                    + abs($totalDr)
                    + abs($closingStock);
            } else {
                $cogs =
                    abs($openingStock)
                    + abs($totalDr)
                    - abs($closingStock);
            }

            $gross = $totalCr - $cogs;
            $net   = $gross + $totII - $totIE;
            
            $gross = $totalCr - $cogs;
            $net   = $gross + $totII - $totIE;

            $data['OpeningStock'] = $this->fmt($openingStock);
            $data['ClosingStock'] = $this->fmt($closingStock);
            $data['COGS']         = $this->fmt($cogs);
            $data['totalCr']      = $this->fmt($totalCr);
            $data['totalDr']      = $this->fmt($totalDr);
            $data['GrossPandL']   = $this->fmt($gross);
            $data['NetPandL']     = $this->fmt($net);

            return ['success' => true, 'data' => $data];
        } catch (\Throwable $e) {
            Log::error('ReportsService::pandl error', ['msg' => $e->getMessage()]);
            return ['success' => false, 'data' => $this->emptyPandL()];
        }
    }

    /* -------- Helper -------- */
    private function emptyPandL(): array
    {
        return [
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
        ];
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
                    //'EXEC dbo.usp_VchMonthlyCashInOut @PartyId=?, @Start=?, @End=?, @DateStyle=?',
                    'EXEC dbo.usp_VchMonthlyCashInOut @PartyId=?, @Start=?, @End=?',
                    [$partyId, $startDate, $endDate]
                    //[$partyId, $startDate, $endDate, $dateStyleDefault]
                );
                break;
            // case 2: // Creditors & Debtors
            //     $groups2 = $opts['groups'] ?? 'Sundry Creditors,Sundry Debtors';
            //     $selected = DB::select(
            //         'EXEC dbo.usp_MonthlyCashFlow_CreditorsDebtors @PartyId=?, @Start=?, @End=?, @DateStyle=?, @GroupList=?',
            //         [$partyId, $startDate, $endDate, $dateStyleDefault, $groups2]
            //     );
            //     break;
            case 2: // Creditors & Debtors
                $groups2 = $opts['groups'] ?? 'Sundry Creditors,Sundry Debtors';


                // $selected = DB::select(
                // 'EXEC dbo.usp_MonthlyCashFlow_CreditorsDebtors
                // @PartyId=?, @Start=?, @End=?, @GroupList=?',
                // [$partyId, $startDate, $endDate, $groups2]
                // );
                 $selected = DB::select(
                'EXEC dbo.usp_MonthlyCashFlow_CreditorsDebtors
                @PartyId=?, @Start=?, @End=?',
                [$partyId, $startDate, $endDate]
                );
                break;
            case 3: // Receipt & Payment
                $outflowNegative = !empty($opts['outflow_negative']) ? 1 : 0;
                $groups3       = $opts['groups']        ?? 'Sundry Creditors,Sundry Debtors';
                $excludeTypes3 = $opts['exclude_types'] ?? 'Rcpt,Pymt';
                // $selected = DB::select(
                //     'EXEC dbo.usp_MonthlyRcptPay_CreditorsDebtors @PartyId=?, @Start=?, @End=?, @DateStyle=?, @OutflowNegative=?, @GroupList=?, @ExcludeVchTypes=?',
                //     [$partyId, $startDate, $endDate, $dateStyleDefault, $outflowNegative, $groups3, $excludeTypes3]
                // ); 
                $selected = DB::select(
                    'EXEC dbo.usp_MonthlyRcptPay_CreditorsDebtors @PartyId=?, @Start=?, @End=?, @OutflowNegative=?, @GroupList=?, @ExcludeVchTypes=?',
                    [$partyId, $startDate, $endDate, $outflowNegative, $groups3, $excludeTypes3]
                );
                break;
            case 4: 
                // $dateStyle4 = array_key_exists('date_style', $opts) ? $opts['date_style'] : 23;
                // if ($dateStyle4 === '' || strtolower((string)$dateStyle4) === 'null') $dateStyle4 = null;
                $dateStyle4 = 23;
                //$groups4       = $opts['groups']        ?? 'Cash-in-Hand,Bank Accounts';
                $groups4       = 'Cash-in-Hand,Bank Accounts,Bank OD A/c';
                $excludeTypes4 = $opts['exclude_types'] ?? null;
                $selected = DB::select(
                    'EXEC dbo.usp_MonthlyCashBankFlow @PartyId=?, @Start=?, @End=?, @DateStyle=?, @GroupList=?, @ExcludeVchTypes=?',
                    [$partyId, $startDate, $endDate, $dateStyle4, $groups4, $excludeTypes4]
                );
                
                break;
            case 5: // Income & Expense
                
                $selected = DB::select(
                    'EXEC dbo.usp_MonthlyIncomeExpenseChart @PartyId=?, @Start=?, @End=?, @MetricType=?',
                    [
                        $partyId,
                        $startDate,
                        $endDate,
                        $opts['metricType'] ?? 'Direct Income'
                    ]
                );
                
                break;            
            default:
                // $dateStyle4 = array_key_exists('date_style', $opts) ? $opts['date_style'] : 23;
                // if ($dateStyle4 === '' || strtolower((string)$dateStyle4) === 'null') $dateStyle4 = null;
                // $groups4       = $opts['groups']        ?? 'Cash-in-Hand,Bank Accounts';
                // $excludeTypes4 = $opts['exclude_types'] ?? null;
                // $selected = DB::select(
                //     'EXEC dbo.usp_MonthlyCashBankFlow @PartyId=?, @Start=?, @End=?, @DateStyle=?, @GroupList=?, @ExcludeVchTypes=?',
                //     [$partyId, $startDate, $endDate, $dateStyle4, $groups4, $excludeTypes4]
                // );
                break;
        }

        // DEBUG: Check available fields
        if (!empty($selected)) {
            $firstRow = (array)$selected[0];
            \Log::info('STORED PROCEDURE FIELDS:', array_keys($firstRow));
        }

        // --- Normalize arrays ---
        $closingBalance = [];
        $months  = $cashIn = $cashOut = [];
        $directIncome = [];
        $directExpense = [];
        $indirectIncome = [];
        $indirectExpense = [];
        $prevMonthIn = $prevMonthOut = [];
        $prevQuarterIn = $prevQuarterOut = [];
        $prevYearIn = $prevYearOut = [];
        $budgetIn = $budgetOut = [];
        $forecastIn = $forecastOut = [];
        $cashflowIn = $cashflowOut = [];
        $plIn = $plOut = [];
        
        $tempQuarterIn = [];
        $tempQuarterOut = [];
        foreach ($selected as $r) {
            $rowArray = (array)$r;

            $months[]       = (string)($r->label ?? '');
            $cashIn[]       = isset($r->cashIn)  ? (float)$r->cashIn  : 0.0;
            $cashOut[]      = isset($r->cashOut) ? (float)$r->cashOut : 0.0;
            
            $tempQuarterIn[]  = isset($r->cashIn)
                ? (float)$r->cashIn
                : 0.0;

            $tempQuarterOut[] = isset($r->cashOut)
                ? (float)$r->cashOut
                : 0.0;

            // Map previous year data - using exact field names from stored procedure
            $prevYearIn[]   = isset($r->prevYearIn)  ? (float)$r->prevYearIn  : 0.0;
            $prevYearOut[]  = isset($r->prevYearOut) ? (float)$r->prevYearOut : 0.0;

            // Map other comparison data
            $prevMonthIn[]   = isset($r->prevMonthIn)  ? (float)$r->prevMonthIn  : 0.0;
            $prevMonthOut[]  = isset($r->prevMonthOut) ? (float)$r->prevMonthOut : 0.0;
            // $prevQuarterIn[]  = isset($r->prevQuarterIn)  ? (float)$r->prevQuarterIn  : 0.0;
            // $prevQuarterOut[] = isset($r->prevQuarterOut) ? (float)$r->prevQuarterOut : 0.0;
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
            $closingBalance[] = isset($r->ClosingBalance) ? (float)$r->ClosingBalance : 0.0;
            
            $metricType = $opts['metricType'] ?? '';

            $directIncome[] =
                $metricType === 'Direct Incomes'
                    ? (float)($r->cashIn ?? 0)
                    : 0;

            $directExpense[] =
                $metricType === 'Direct Expenses'
                    ? (float)($r->cashIn ?? 0)
                    : 0;

            $indirectIncome[] =
                $metricType === 'Indirect Incomes'
                    ? (float)($r->cashIn ?? 0)
                    : 0;

            $indirectExpense[] =
                $metricType === 'Indirect Expenses'
                    ? (float)($r->cashIn ?? 0)
                    : 0;

            // DEBUG: Log each row's previous year data
            \Log::info("Row {$r->label}: prevYearIn = " . ($r->prevYearIn ?? 'NULL') . ", prevYearOut = " . ($r->prevYearOut ?? 'NULL'));
        }

        // Fallback: if SP does not return prevMonth fields, derive them from prior month cash values.
        $hasPrevMonthInData = count(array_filter($prevMonthIn, fn($v) => (float)$v !== 0.0)) > 0;
        $hasPrevMonthOutData = count(array_filter($prevMonthOut, fn($v) => (float)$v !== 0.0)) > 0;

        if (!$hasPrevMonthInData) {
            $prevMonthIn = array_merge([0.0], array_slice($cashIn, 0, max(count($cashIn) - 1, 0)));
        }

        if (!$hasPrevMonthOutData) {
            $prevMonthOut = array_merge([0.0], array_slice($cashOut, 0, max(count($cashOut) - 1, 0)));
        }

        // Some procedures return running/cumulative values (not month-wise values),
        // especially for Receipt vs Payment in yearly ranges.
        // Convert those series to actual month amounts when detected.
        // if (in_array($type, [1, 3], true)) {
        //     $cashIn = $this->normalizeIfCumulative($cashIn);
        //     $cashOut = $this->normalizeIfCumulative($cashOut);
        //     $prevMonthIn = $this->normalizeIfCumulative($prevMonthIn);
        //     $prevMonthOut = $this->normalizeIfCumulative($prevMonthOut);
        //     $prevQuarterIn = $this->normalizeIfCumulative($prevQuarterIn);
        //     $prevQuarterOut = $this->normalizeIfCumulative($prevQuarterOut);
        //     $prevYearIn = $this->normalizeIfCumulative($prevYearIn);
        //     $prevYearOut = $this->normalizeIfCumulative($prevYearOut);
        // }
        if (in_array($type, [1, 3], true)) {
            $cashIn = $this->normalizeIfCumulative($cashIn);
            $cashOut = $this->normalizeIfCumulative($cashOut);
            $prevMonthIn = $this->normalizeIfCumulative($prevMonthIn);
            $prevMonthOut = $this->normalizeIfCumulative($prevMonthOut);
            $prevQuarterIn = $this->normalizeIfCumulative($prevQuarterIn);
            $prevQuarterOut = $this->normalizeIfCumulative($prevQuarterOut);

            // DO NOT NORMALIZE PREVIOUS YEAR
            // SP already returns month-wise values
        }
        // Receipt/Payment charts should render both metrics as magnitudes (positive bars/lines)
        // even when the procedure returns outflow as negative for compare series.
        if ($type === 3) {
            $cashIn = array_map('abs', $cashIn);
            $cashOut = array_map('abs', $cashOut);
            $prevMonthIn = array_map('abs', $prevMonthIn);
            $prevMonthOut = array_map('abs', $prevMonthOut);
            $prevQuarterIn = array_map('abs', $prevQuarterIn);
            $prevQuarterOut = array_map('abs', $prevQuarterOut);
            $prevYearIn = array_map('abs', $prevYearIn);
            $prevYearOut = array_map('abs', $prevYearOut);
        }
        $quarterLabels = ['Apr-Jun', 'Jul-Sep', 'Oct-Dec', 'Jan-Mar'];

        $quarterIn = [];
        $quarterOut = [];

        /*
        |--------------------------------------------------------------------------
        | CURRENT QUARTER SUM
        |--------------------------------------------------------------------------
        */
        $useQuarterEndClosing = in_array($type, [2, 4], true);
        $buildQuarterSeries = function (array $series) use ($useQuarterEndClosing): array {
            $quarters = [];
            for ($i = 0; $i < count($series); $i += 3) {
                if ($useQuarterEndClosing) {
                    $quarters[] = (float)($series[$i + 2] ?? $series[$i + 1] ?? $series[$i] ?? 0);
                } else {
                    $quarters[] =
                        (float)($series[$i] ?? 0)
                        + (float)($series[$i + 1] ?? 0)
                        + (float)($series[$i + 2] ?? 0);
                }
            }
            return $quarters;
        };

        // for ($i = 0; $i < count($cashIn); $i += 3) {

        //     $quarterIn[] =
        //         ($cashIn[$i] ?? 0) +
        //         ($cashIn[$i + 1] ?? 0) +
        //         ($cashIn[$i + 2] ?? 0);

        //     $quarterOut[] =
        //         ($cashOut[$i] ?? 0) +
        //         ($cashOut[$i + 1] ?? 0) +
        //         ($cashOut[$i + 2] ?? 0);
        // }
        $quarterIn = $buildQuarterSeries($cashIn);
        $quarterOut = $buildQuarterSeries($cashOut);
        /*
        |--------------------------------------------------------------------------
        | PREVIOUS YEAR QUARTER SUM
        |--------------------------------------------------------------------------
        */

        // $prevYearQuarterIn = [];
        // $prevYearQuarterOut = [];

        // for ($i = 0; $i < count($prevYearIn); $i += 3) {

        //     $prevYearQuarterIn[] =
        //         ($prevYearIn[$i] ?? 0)
        //         + ($prevYearIn[$i + 1] ?? 0)
        //         + ($prevYearIn[$i + 2] ?? 0);

        //     $prevYearQuarterOut[] =
        //         ($prevYearOut[$i] ?? 0)
        //         + ($prevYearOut[$i + 1] ?? 0)
        //         + ($prevYearOut[$i + 2] ?? 0);
        // }

        /*
        |--------------------------------------------------------------------------
        | QUARTER COMPARE
        |--------------------------------------------------------------------------
        */

        /*
        |--------------------------------------------------------------------------
        | PREVIOUS YEAR QUARTER SUM
        |--------------------------------------------------------------------------
        */

        // $prevYearQuarterIn = [];
        // $prevYearQuarterOut = [];

        // for ($i = 0; $i < count($prevYearIn); $i += 3) {

        //     $prevYearQuarterIn[] =
        //         ($prevYearIn[$i] ?? 0)
        //         + ($prevYearIn[$i + 1] ?? 0)
        //         + ($prevYearIn[$i + 2] ?? 0);

        //     $prevYearQuarterOut[] =
        //         ($prevYearOut[$i] ?? 0)
        //         + ($prevYearOut[$i + 1] ?? 0)
        //         + ($prevYearOut[$i + 2] ?? 0);
        // }
        $prevYearQuarterIn = $buildQuarterSeries($prevYearIn);
        $prevYearQuarterOut = $buildQuarterSeries($prevYearOut);

        $prevQuarterInByQuarter = [];
        $prevQuarterOutByQuarter = [];

        for ($i = 0; $i < count($prevQuarterIn); $i += 3) {

            $prevQuarterInByQuarter[] =
                (float)($prevQuarterIn[$i] ?? 0)
                + (float)($prevQuarterIn[$i + 1] ?? 0)
                + (float)($prevQuarterIn[$i + 2] ?? 0);


            $prevQuarterOutByQuarter[] =
                (float)($prevQuarterOut[$i] ?? 0)
                + (float)($prevQuarterOut[$i + 1] ?? 0)
                + (float)($prevQuarterOut[$i + 2] ?? 0);
        }

        /*
        |--------------------------------------------------------------------------
        | QUARTER COMPARE
        |--------------------------------------------------------------------------
        |
        | Example:
        | Apr-Jun 26 compare with:
        | 1. Jan-Mar 26
        | 2. Apr-Jun 26
        | 3. Apr-Jun 25
        |
        */

        $quarterCompare = [];

        for ($i = 0; $i < count($quarterIn); $i++) {

            $currentQuarterIn =
                $quarterIn[$i] ?? 0;

            $currentQuarterOut =
                $quarterOut[$i] ?? 0;

            /*
            |--------------------------------------------------------------------------
            | Previous Quarter
            |--------------------------------------------------------------------------
            */

            // $previousQuarterIn = $prevQuarterInByQuarter[$i] ?? ($i > 0 ? ($quarterIn[$i - 1] ?? 0) : 0);
            // $previousQuarterIn = $prevQuarterInByQuarter[$i]
            //     ?? ($i > 0
            //         ? ($quarterIn[$i - 1] ?? 0)
            //         : ($prevYearQuarterIn[3] ?? 0));
            $previousQuarterInRaw = (float)($prevQuarterInByQuarter[$i] ?? 0);
            $previousQuarterInFallback = $i > 0
                ? (float)($quarterIn[$i - 1] ?? 0)
                : (float)($prevYearQuarterIn[3] ?? 0);
            $previousQuarterIn = abs($previousQuarterInRaw) > 0.000001
                ? $previousQuarterInRaw
                : $previousQuarterInFallback;
                // $i > 0
                //     ? ($quarterIn[$i - 1] ?? 0)
                //    : ($prevYearQuarterIn[3] ?? 0);

            // $previousQuarterOut = $prevQuarterOutByQuarter[$i] ?? ($i > 0 ? ($quarterOut[$i - 1] ?? 0) : 0);
            // $previousQuarterOut = $prevQuarterOutByQuarter[$i]
            //     ?? ($i > 0
            //         ? ($quarterOut[$i - 1] ?? 0)
            //         : ($prevYearQuarterOut[3] ?? 0));
            $previousQuarterOutRaw = (float)($prevQuarterOutByQuarter[$i] ?? 0);
            $previousQuarterOutFallback = $i > 0
                ? (float)($quarterOut[$i - 1] ?? 0)
                : (float)($prevYearQuarterOut[3] ?? 0);
            $previousQuarterOut = abs($previousQuarterOutRaw) > 0.000001
                ? $previousQuarterOutRaw
                : $previousQuarterOutFallback;
                // $i > 0
                //     ? ($quarterOut[$i - 1] ?? 0)
                //     : ($prevYearQuarterOut[3] ?? 0);

            /*
            |--------------------------------------------------------------------------
            | Previous Year Same Quarter
            |--------------------------------------------------------------------------
            */

            $previousYearQuarterInValue =
                $prevYearQuarterIn[$i] ?? 0;

            $previousYearQuarterOutValue =
                $prevYearQuarterOut[$i] ?? 0;

            /*
            |--------------------------------------------------------------------------
            | FINAL STRUCTURE
            |--------------------------------------------------------------------------
            */

            $quarterCompare[] = [

                'label' => $quarterLabels[$i] ?? '',

                'bars' => [

                    [
                        'label' => 'Previous Quarter',
                        'in'    => $previousQuarterIn,
                        'out'   => $previousQuarterOut,
                    ],

                    [
                        'label' => 'Current Quarter',
                        'in'    => $currentQuarterIn,
                        'out'   => $currentQuarterOut,
                    ],

                    [
                        'label' => 'Previous Year',
                        'in'    => $previousYearQuarterInValue,
                        'out'   => $previousYearQuarterOutValue,
                    ]
                ]
            ];
        }

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
            'quarterLabels' => $quarterLabels,
            'quarterIn' => $quarterIn,
            'quarterOut' => $quarterOut,
            'closingBalance' => $closingBalance,
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
            'quarterLabels' => $quarterLabels,
            'quarterIn' => $quarterIn,
            'quarterOut' => $quarterOut,
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
            'closingBalance' => $closingBalance,
            'directIncome'     => $directIncome,
            'directExpense'    => $directExpense,
            'indirectIncome'   => $indirectIncome,
            'indirectExpense'  => $indirectExpense,
            'quarterCompare' => $quarterCompare,
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

    public function voucherDetails($guid, $strGUID, $vchType)
    {
        return DB::select("
            EXEC dbo.GetVoucherDetails ?, ?, ?
        ", [$guid, $strGUID, $vchType]);

    }
}
