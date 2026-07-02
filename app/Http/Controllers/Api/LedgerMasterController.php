<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\LedgerExport;
use App\Exports\LedgerSummaryExport;
use App\Exports\VoucherHistoryExport;
use App\Services\ReportsService;
use Illuminate\Support\Facades\Auth;

class LedgerMasterController extends Controller
{
    public function index(Request $request)
    {
        try {
            $partyguid = $request->header('partyguid');
            $validator = Validator::make([
                'partyguid' => $request->header('partyguid'),
                'start_date' => $request->header('start_date'),
                'end_date' => $request->header('end_date')
            ], [
                'partyguid' => 'required|uuid',
                'start_date' => 'nullable|date|date_format:Y-m-d',
                'end_date' => 'nullable|date|date_format:Y-m-d|after_or_equal:start_date'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => $validator->errors()
                ], 422);
            }

            // If validation passes, continue with your logic
            $partyguid = $request->header('partyguid');
            $startDate = $request->header('start_date');
            $endDate = $request->header('end_date');

            $results = DB::select(
                'EXEC CheckPartyGUIDCount ?',
                [$partyguid]
            );

            if (!$results) {
                return response()->json([
                    'success' => false,
                    'partyguid' => $partyguid,
                    'error' =>  'Invalid PartyGUID - not found in database'
                ], 422);
            }

            $LedgerMasters = DB::table('LedgerMaster')
                ->where('PartyGUID', $partyguid)
                //->where('iParentId', 0)
                //->where("strDispName", '!=', 'Cost of Sales :')
                ->get();

            if ($LedgerMasters->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No Ledger data found for this PartyGUID'
                ], 404);
            }

            $data = [];
            foreach ($LedgerMasters as $LedgerMaster) {

                $data[] = array(
                    "iLedgerId" => $LedgerMaster->iLedgerId,
                    "strCustomerName" => trim($LedgerMaster->strCustomerName),
                    "decOpBl" => $LedgerMaster->decOpBl,
                    "decClBl" => $LedgerMaster->decClBl,
                    "strGUID" => $LedgerMaster->strGUID,
                    "strParents" => $LedgerMaster->strParents,
                    "PartyGUID" => $LedgerMaster->PartyGUID,
                    "iPrimaryGroupId" => $LedgerMaster->iPrimaryGroupId,
                    "iSubGroupId" => $LedgerMaster->iSubGroupId,
                    "iSubToSubGroupId" => $LedgerMaster->iSubToSubGroupId
                );
            }

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve code master records',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function vch_history(Request $request)
    {
        try {
            $validator = Validator::make([
                'partyguid' => $request->header('partyguid'),
                'iledgerid' => $request->header('iledgerid'),
                'start_date' => $request->header('start_date'),
                'end_date' => $request->header('end_date')
            ], [
                'partyguid' => 'required|uuid',
                'iledgerid' => 'required',
                'start_date' => 'nullable|date|date_format:Y-m-d',
                'end_date' => 'nullable|date|date_format:Y-m-d|after_or_equal:start_date'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => $validator->errors()
                ], 422);
            }

            // If validation passes, continue with your logic
            $partyguid = $request->header('partyguid');
            $iledgerid = $request->header('iledgerid');
            $startDate = $request->header('start_date');
            $endDate = $request->header('end_date');

            $results = DB::select(
                'EXEC CheckPartyGUIDCount ?',
                [$partyguid]
            );

            if (!$results) {
                return response()->json([
                    'success' => false,
                    'partyguid' => $partyguid,
                    'error' =>  'Invalid PartyGUID - not found in database'
                ], 422);
            }

            $VchHistories = DB::table('VchHistory')
                ->where('PartyGUID', $partyguid)
                ->where('iLedgerId', $iledgerid)
                //->where("strDispName", '!=', 'Cost of Sales :')
                ->get();

            if ($VchHistories->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No Ledger data found for this PartyGUID'
                ], 404);
            }

            $data = [];
            foreach ($VchHistories as $VchHistory) {

                $data[] = array(
                    "iVchId" => $VchHistory->iVchId,
                    "iLedgerId" => $VchHistory->iLedgerId,
                    "trnAccount" => trim($VchHistory->trnAccount),
                    "vchType" => $VchHistory->vchType,
                    "DRAmount" => $VchHistory->DRAmount,
                    "CRAmount" => $VchHistory->CRAmount,
                    "vchNo" => $VchHistory->vchNo,
                    "strVchDate" => $VchHistory->strVchDate,
                    "decRunningBalance" => $VchHistory->decRunningBalance,
                    "PartyGUID" => $VchHistory->PartyGUID,
                    "iYearId" => $VchHistory->iYearId
                );
            }

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve voucher history records',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function group_master(Request $request)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['User not authenticated'], 401);
            }
            $partyId = auth('api')->id();
            $partyguid = $request->partyguid;
            $validator = Validator::make([
                'partyguid' => $request->partyguid
            ], [
                'partyguid' => 'required|uuid'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => $validator->errors()
                ], 422);
            }

            // If validation passes, continue with your logic
            $partyguid = $request->partyguid;
            $results = DB::select(
                'EXEC CheckPartyGUIDCount ?',
                [$partyguid]
            );

            if (!$results) {
                return response()->json([
                    'success' => false,
                    'partyguid' => $partyguid,
                    'error' =>  'Invalid PartyGUID - not found in database'
                ], 422);
            }

            $GroupMasters = DB::table('GroupMaster')
                ->where('PartyGUID', $partyguid)
                ->where('iPartyId', $partyId)
                //->where('iParentId', 0)
                //->where("strDispName", '!=', 'Cost of Sales :')
                ->get();

            if ($GroupMasters->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No Group data found for this Party'
                ], 404);
            }

            $data = [];
            foreach ($GroupMasters as $Group) {

                $data[] = array(
                    "iGroupId" => $Group->iGroupId,
                    "strGroupName" => trim($Group->strGroupName),
                    "iParentGroupId" => $Group->iParentGroupId,
                    "PartyGUID" => $Group->PartyGUID,
                    "iPartyId" => $Group->iPartyId
                );
            }

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve code master records',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function index_new(Request $request)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['User not authenticated'], 401);
            }
            $validator = Validator::make($request->all(), [
                'partyguid' => 'required|uuid',
                'iGroupId' => 'nullable|integer',
                'group_id' => 'nullable|integer',
                'strCustomerName' => 'nullable|string',
                'range' => 'nullable',
                'from' => 'nullable|date',
                'to' => 'nullable|date|after_or_equal:from',
                'from_custom' => 'nullable|date',
                'to_custom' => 'nullable|date|after_or_equal:from_custom',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => $validator->errors()
                ], 422);
            }

            $partyguid = $request->partyguid;
            $partyId = auth('api')->id();
            $results = DB::select('EXEC CheckPartyGUIDCount ?', [$partyguid]);
            if (!$results) {
                return response()->json([
                    'success' => false,
                    'partyguid' => $partyguid,
                    'error' => 'Invalid Party - not found in database'
                ], 422);
            }

            $groupId = (int) ($request->input('iGroupId', $request->input('group_id', 0)) ?: 0);
            $strCustomerName = $request->input('strCustomerName');
            [$financialYears, $rangeSel, $startDate, $endDate] = $this->resolveApiFinancialYearFilter($request, $partyId);

			$svc = app(ReportsService::class);
            $ledgerResult = $svc->ledger($partyId, $groupId, $startDate, $endDate, $strCustomerName);

            if (!$ledgerResult['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $ledgerResult['message'] ?? 'Failed to retrieve ledger data',
                    'error' => $ledgerResult['error'] ?? 'Unknown error',
                ], 500);
            }

            $LedgerMasters = collect(data_get($ledgerResult, 'data.rows', []));

            if ($LedgerMasters->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No Ledger data found for this PartyGUID',
                    'meta' => $this->apiFinancialYearMeta($financialYears, $rangeSel, $startDate, $endDate),
                ], 404);
            }

            $data = [];
            $groups = [];
            foreach ($LedgerMasters as $LedgerMaster) {
                $op = $this->num($LedgerMaster->decOpBl ?? 0);
                $dr = abs($this->num($LedgerMaster->decDr ?? 0));
                $cr = abs($this->num($LedgerMaster->decCr ?? 0));
                $cl = $this->num($LedgerMaster->decClBl ?? 0);

                // Match the web ledger report: hide rows where opening, debit, credit, and closing are all zero.
                if ($op == 0.0 && $dr == 0.0 && $cr == 0.0 && $cl == 0.0) {
                    continue;
                }

                $row = array(
                    "iLedgerId" => $LedgerMaster->iLedgerId,
                    "strCustomerName" => trim($LedgerMaster->strCustomerName),
                    "decOpBl" => $this->fmt($op),
                    "decOpBlRaw" => $op,
                    "decOpBlSide" => $this->balanceSideForLedger($op),
                    "decDr" => $this->fmt($dr),
                    "decDrRaw" => $dr,
                    "decCr" => $this->fmt($cr),
                    "decCrRaw" => $cr,
                    "decClBl" => $this->fmt($cl),
                    "decClBlRaw" => $cl,
                    "decClBlSide" => $this->balanceSideForLedger($cl),
                    "strGUID" => $LedgerMaster->strGUID,
                    "strParents" => $LedgerMaster->strParents,
                    "PartyGUID" => $LedgerMaster->PartyGUID,
                    "iPrimaryGroupId" => $LedgerMaster->iPrimaryGroupId,
                    "iSubGroupId" => $LedgerMaster->iSubGroupId,
                    "iSubToSubGroupId" => $LedgerMaster->iSubToSubGroupId,
					"decRunningBalance" => $this->fmt($LedgerMaster->decRunningBalance ?? 0)
                );
				$data[] = $row;
                $parent = trim((string) ($LedgerMaster->strParents ?? '')) ?: 'Ungrouped';
                if (!isset($groups[$parent])) {
                    $groups[$parent] = [
                        'parent' => $parent,
                        'rows' => [],
                        'total_opening_raw' => 0.0,
                        'total_debit_raw' => 0.0,
                        'total_credit_raw' => 0.0,
                        'total_closing_raw' => 0.0,
                    ];
                }
                $groups[$parent]['rows'][] = $row;
                $groups[$parent]['total_opening_raw'] += $op;
                $groups[$parent]['total_debit_raw'] += $dr;
                $groups[$parent]['total_credit_raw'] += $cr;
                $groups[$parent]['total_closing_raw'] += $cl;
            }

			$groupedData = collect($groups)->map(function ($group) {
                $group['total_opening'] = $this->fmt(abs($group['total_opening_raw']));
                $group['total_opening_side'] = $this->balanceSideForLedger($group['total_opening_raw']);
                $group['total_debit'] = $this->fmt($group['total_debit_raw']);
                $group['total_credit'] = $this->fmt($group['total_credit_raw']);
                $group['total_closing'] = $this->fmt(abs($group['total_closing_raw']));
                $group['total_closing_side'] = $this->balanceSideForLedger($group['total_closing_raw']);
                return $group;
            })->values();

            return response()->json([
                'success' => true,
                'data' => $data,
				'groups' => $groupedData,
                'meta' => array_merge(
                    $this->apiFinancialYearMeta($financialYears, $rangeSel, $startDate, $endDate),
                    ['groupId' => $groupId, 'strCustomerName' => $strCustomerName]
                ),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve code master records',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function vch_history_new(Request $request)
	{
		try {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['User not authenticated'], 401);
            }

			$validator = Validator::make($request->all(), [
                'partyguid' => 'required|uuid',
                'iledgerid' => 'required|integer',
                'range' => 'nullable',
                'from' => 'nullable|date',
                'to' => 'nullable|date|after_or_equal:from',
                'from_custom' => 'nullable|date',
                'to_custom' => 'nullable|date|after_or_equal:from_custom',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date',
            ]);
			if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => $validator->errors()
                ], 422);
            }

			$partyguid = $request->partyguid;
            $iledgerid = (int) $request->iledgerid;
            $partyId = auth('api')->id();

			$results = DB::select('EXEC CheckPartyGUIDCount ?', [$partyguid]);
            if (!$results) {
                return response()->json([
                    'success' => false,
                    'partyguid' => $partyguid,
                    'error' => 'Invalid PartyGUID - not found in database'
                ], 422);
            }

			[$financialYears, $rangeSel, $startDate, $endDate] = $this->resolveApiFinancialYearFilter($request, $partyId);
			$svc = app(ReportsService::class);
            $voucherHistoryResult = $svc->voucherHistory($partyguid, $iledgerid, $startDate, $endDate);
			if (!$voucherHistoryResult['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $voucherHistoryResult['message'] ?? 'Failed to retrieve voucher history',
                    'error' => $voucherHistoryResult['error'] ?? 'Unknown error'
                ], 500);
            }

			$VchHistories = collect(data_get($voucherHistoryResult, 'data.rows', []));

			$openingBalanceData = ['balance' => 0.0, 'side' => 'Dr'];
            if ($startDate) {
                $previousDay = date('Y-m-d', strtotime($startDate . ' -1 day'));
                $openingBalanceData = $svc->getOpeningBalance($partyguid, $iledgerid, $previousDay);
            }
			
			$openingSigned = ($openingBalanceData['side'] ?? 'Dr') === 'Cr'
                ? -abs($this->num($openingBalanceData['balance'] ?? 0))
                : abs($this->num($openingBalanceData['balance'] ?? 0));
            $previousBalance = $openingSigned;
            $totalDebit = 0.0;
            $totalCredit = 0.0;
            $data = [[
                'is_opening' => true,
                'iVchId' => 0,
                'iLedgerId' => $iledgerid,
                'trnAccount' => 'Balance B/F',
                'vchType' => 'Opening',
                'DRAmount' => $this->fmt(0),
                'DRAmountRaw' => 0.0,
                'CRAmount' => $this->fmt(0),
                'CRAmountRaw' => 0.0,
                'vchNo' => 'OPENING BALANCE',
                'strVchDate' => $startDate ? date('d-m-Y', strtotime($startDate)) : '',
                'opening_balance' => $this->fmt(abs($previousBalance)),
                'opening_balance_raw' => $previousBalance,
                'opening_balance_side' => $this->balanceSideForVoucher($previousBalance),
                'decRunningBalance' => $this->fmt(abs($previousBalance)),
                'decRunningBalanceRaw' => $previousBalance,
                'closing_balance_side' => $this->balanceSideForVoucher($previousBalance),
                'PartyGUID' => $partyguid,
                'iYearId' => 0,
            ]];
			
            foreach ($VchHistories as $VchHistory) {
				$dr = abs($this->num($VchHistory->DRAmount ?? $VchHistory->drAmount ?? $VchHistory->DrAmount ?? 0));
                $cr = abs($this->num($VchHistory->CRAmount ?? $VchHistory->crAmount ?? $VchHistory->CrAmount ?? 0));
                $currentOpening = $previousBalance;
                // Match the web report running balance formula.
                $currentClosing = $previousBalance - $dr + $cr;
                $totalDebit += $dr;
                $totalCredit += $cr;
                $data[] = array(
                    "iVchId" => $VchHistory->iVchId ?? $VchHistory->vchId ?? 0,
                    "iLedgerId" => $VchHistory->iLedgerId ?? $VchHistory->ledgerId ?? 0,
                    "trnAccount" => trim($VchHistory->trnAccount ?? $VchHistory->accountName ?? ''),
                    "vchType" => $VchHistory->vchType ?? $VchHistory->voucherType ?? '',
                    "DRAmount" => $this->fmt($dr),
                    "DRAmountRaw" => $dr,
                    "CRAmount" => $this->fmt($cr),
                    "CRAmountRaw" => $cr,
                    "vchNo" => $VchHistory->vchNo ?? $VchHistory->voucherNo ?? '',
                    "strVchDate" => $VchHistory->strVchDate ?? $VchHistory->vchDate ?? $VchHistory->transactionDate ?? '',
                    "opening_balance" => $this->fmt(abs($currentOpening)),
                    "opening_balance_raw" => $currentOpening,
                    "opening_balance_side" => $this->balanceSideForVoucher($currentOpening),
                    "decRunningBalance" => $this->fmt(abs($currentClosing)),
                    "decRunningBalanceRaw" => $currentClosing,
                    "closing_balance_side" => $this->balanceSideForVoucher($currentClosing),
                    "PartyGUID" => $VchHistory->PartyGUID ?? $partyguid,
                    "iYearId" => $VchHistory->iYearId ?? $VchHistory->yearId ?? 0
                );
				$previousBalance = $currentClosing;
            }

			$closingSigned = $previousBalance;
            $data[] = [
                'is_closing' => true,
                'iVchId' => 0,
                'iLedgerId' => $iledgerid,
                'trnAccount' => 'Balance C/F',
                'vchType' => 'Closing',
                'DRAmount' => $this->fmt(0),
                'DRAmountRaw' => 0.0,
                'CRAmount' => $this->fmt(0),
                'CRAmountRaw' => 0.0,
                'vchNo' => 'CLOSING BALANCE',
                'strVchDate' => $endDate ? date('d-m-Y', strtotime($endDate)) : date('d-m-Y'),
                'opening_balance' => $this->fmt(abs($closingSigned)),
                'opening_balance_raw' => $closingSigned,
                'opening_balance_side' => $this->balanceSideForVoucher($closingSigned),
                'decRunningBalance' => $this->fmt(abs($closingSigned)),
                'decRunningBalanceRaw' => $closingSigned,
                'closing_balance_side' => $this->balanceSideForVoucher($closingSigned),
                'PartyGUID' => $partyguid,
                'iYearId' => 0,
            ];

            return response()->json([
                'success' => true,
                'data' => $data,
				'summary' => [
                    'opening_balance' => $this->fmt(abs($openingSigned)),
                    'opening_balance_raw' => $openingSigned,
                    'opening_balance_side' => $this->balanceSideForVoucher($openingSigned),
                    'total_debit' => $this->fmt($totalDebit),
                    'total_debit_raw' => $totalDebit,
                    'total_credit' => $this->fmt($totalCredit),
                    'total_credit_raw' => $totalCredit,
                    'closing_balance' => $this->fmt(abs($closingSigned)),
                    'closing_balance_raw' => $closingSigned,
                    'closing_balance_side' => $this->balanceSideForVoucher($closingSigned),
                ],
                'meta' => array_merge($this->apiFinancialYearMeta($financialYears, $rangeSel, $startDate, $endDate), ['ledgerId' => $iledgerid]),
                'opening_balance' => $this->fmt(abs($openingSigned)),
                'opening_balance_side' => $this->balanceSideForVoucher($openingSigned),
                'total_debit' => $this->fmt($totalDebit),
                'total_credit' => $this->fmt($totalCredit),
                'closing_balance' => $this->fmt(abs($closingSigned)),
                'closing_balance_side' => $this->balanceSideForVoucher($closingSigned),
            ]);
        } catch (\Exception $e) {
            \Log::error('Voucher History Error: ' . $e->getMessage());
            \Log::error('Voucher History Trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve voucher history records',
                'error' => $e->getMessage()
            ], 500);
        }
    }

	private function resolveApiFinancialYearFilter(Request $request, int $partyId): array
    {
        $financialYears = DB::table('YearMaster')
            ->where('iPartyId', $partyId)
            ->orderBy('iYearId', 'desc')
            ->get();

        $rangeSel = $request->input('range') ?: $this->defaultApiFinancialYearRange($financialYears);

        if ($rangeSel !== 'custom' && $financialYears->isNotEmpty() && !$financialYears->firstWhere('iYearId', (int) $rangeSel)) {
            $rangeSel = $this->defaultApiFinancialYearRange($financialYears);
        }

        if ($rangeSel === 'custom') {
            $from = $request->input('from_custom') ?: $request->input('from') ?: $request->input('start_date');
            $to = $request->input('to_custom') ?: $request->input('to') ?: $request->input('end_date');
        } else {
            $range = $this->apiFinancialYearDateRange((string) $rangeSel, $financialYears)
                ?: $this->apiLegacyFinancialYearDateRange((string) $rangeSel);

            $from = $range['from'] ?? ($request->input('from') ?: $request->input('start_date'));
            $to = $range['to'] ?? ($request->input('to') ?: $request->input('end_date'));
        }

        return [$financialYears, $rangeSel, $this->normalizeApiDate($from), $this->normalizeApiDate($to)];
    }

    private function defaultApiFinancialYearRange($financialYears): string
    {
        $currentYear = $financialYears->firstWhere('isCurrentYear', 1);
        $defaultYear = $currentYear ?: $financialYears->first();

        return (string) ($defaultYear->iYearId ?? 'current_year');
    }

    private function apiFinancialYearDateRange(string $rangeSel, $financialYears): ?array
    {
        $selectedYear = $financialYears->firstWhere('iYearId', (int) $rangeSel);

        if (!$selectedYear || !preg_match('/^(\d{4})-(\d{4})$/', (string) $selectedYear->strYear, $matches)) {
            return null;
        }

        return [
            'from' => $matches[1] . '-04-01',
            'to' => $matches[2] . '-03-31',
        ];
    }

    private function apiLegacyFinancialYearDateRange(string $rangeSel): ?array
    {
        $today = now();

        if ($rangeSel === 'current_year') {
            $startYear = $today->month >= 4 ? $today->year : $today->year - 1;
        } elseif ($rangeSel === 'last_year') {
            $startYear = $today->month >= 4 ? $today->year - 1 : $today->year - 2;
        } else {
            return null;
        }

        return [
            'from' => date('Y-m-d', strtotime("$startYear-04-01")),
            'to' => date('Y-m-d', strtotime(($startYear + 1) . '-03-31')),
        ];
    }

    private function normalizeApiDate($date): ?string
    {
        return $date ? date('Y-m-d', strtotime((string) $date)) : null;
    }

    private function apiFinancialYearMeta($financialYears, $rangeSel, ?string $from, ?string $to): array
    {
        return [
            'range' => $rangeSel,
            'from' => $from,
            'to' => $to,
            'financialYears' => $financialYears->map(function ($year) {
                return [
                    'iYearId' => $year->iYearId,
                    'strYear' => $year->strYear,
                    'isCurrentYear' => $year->isCurrentYear ?? 0,
                ];
            })->values(),
        ];
    }

	private function num($v): float
    {
        if ($v === null || $v === '' || $v === 'null' || $v === 'NULL') {
            return 0.0;
        }

        $cleaned = preg_replace('/[^\d.-]/', '', trim((string) $v));

        return ($cleaned === '' || $cleaned === '-' || $cleaned === '.') ? 0.0 : (float) $cleaned;
    }

    private function balanceSideForLedger(float $amount): string
    {
        return $amount < 0 ? 'Dr' : 'Cr';
    }

    private function balanceSideForVoucher(float $amount): string
    {
        return $amount >= 0 ? 'Dr' : 'Cr';
    }

    private function fmt($v): string
	{
		// Handle null, empty strings, or non-numeric values
		if ($v === null || $v === '' || $v === 'null' || $v === 'NULL') {
			$v = 0;
		}

		// Convert to string and clean
		$strVal = (string)$v;
		$strVal = trim($strVal);

		// Remove commas and other non-numeric characters except decimal point and minus sign
		$cleaned = preg_replace('/[^\d.-]/', '', $strVal);

		// Handle empty result after cleaning
		if ($cleaned === '' || $cleaned === '-' || $cleaned === '.') {
			$cleaned = '0';
		}

		// Convert to float
		$num = (float)$cleaned;

		// Prefer intl NumberFormatter (en_IN) if the extension is available
		if (class_exists(\NumberFormatter::class)) {
			$nf = new \NumberFormatter('en_IN', \NumberFormatter::DECIMAL);
			$nf->setAttribute(\NumberFormatter::FRACTION_DIGITS, 2);
			$out = $nf->format($num);
			if ($out !== false) {
				return $out; // e.g., 20,00,000.00
			}
		}

		// Fallback: manual Indian grouping with 2 decimals
		$sign = $num < 0 ? '-' : '';
		$abs  = abs($num);
		$fixed = sprintf('%.2f', $abs);         // no grouping, just 2 decimals

		// Handle case where sprintf returns scientific notation for very small numbers
		if (strpos($fixed, 'E') !== false) {
			$fixed = number_format($abs, 2, '.', '');
		}

		$parts = explode('.', $fixed);
		$int = $parts[0];
		$dec = $parts[1] ?? '00';

		if (strlen($int) > 3) {
			$last3 = substr($int, -3);
			$rest  = substr($int, 0, -3);
			// insert commas every 2 digits in the rest
			$rest = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $rest);
			$int  = $rest . ',' . $last3;
		}

		return $sign . $int . '.' . $dec;       // e.g., 20,00,000.00
	}
	
	private function getLedgerData($partyId, $groupId, $startDate, $endDate, $strCustomerName = null)
    {
        $partyguid = request()->partyguid;
        
        try {
            $rows = collect(DB::select(
                'EXEC dbo.GetLedgerMasters ?, ?, ?, ?, ?',
                [$partyId, $groupId, $startDate, $endDate, $strCustomerName]
            ));

            if ($rows->isEmpty()) {
                return null;
            }

            // Organize data by ledger for summary export
            $byLedger = [];
            $grandDr = 0.0;
            $grandCr = 0.0;

            foreach ($rows as $r) {
                $lid  = $r->iLedgerId ?? null;
                $name = $r->strCustomerName ?? 'Ledger';

                $dr = (float)($r->DrAmount ?? 0);
                $cr = (float)($r->CrAmount ?? 0);
                $grandDr += $dr;
                $grandCr += $cr;

                if (!isset($byLedger[$lid])) {
                    $byLedger[$lid] = [
                        'ledger_id'   => $lid,
                        'ledger_name' => $name,
                        'total_dr'    => 0.0,
                        'total_cr'    => 0.0,
                        'closing'     => (float)($r->decRunningBalance ?? 0),
                    ];
                }

                $byLedger[$lid]['total_dr'] += $dr;
                $byLedger[$lid]['total_cr'] += $cr;
            }

            // Get group name
            $groupName = '';
            if ($groupId) {
                $group = DB::table('GroupMaster')
                    ->where('iGroupId', $groupId)
                    ->where('PartyGUID', $partyguid)
                    ->first();
                $groupName = $group->strGroupName ?? '';
            }

            return [
                'data' => [
                    'rows' => $rows,
                    'by_ledger' => array_values($byLedger),
                    'grand_dr' => $grandDr,
                    'grand_cr' => $grandCr,
                    'grand_diff' => $grandDr - $grandCr,
                ],
                'meta' => [
                    'group_name' => $groupName,
                    'customer_name' => $strCustomerName,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ]
            ];

        } catch (\Throwable $e) {
            \Log::error('Ledger Data Error: ' . $e->getMessage());
            return null;
        }
    }

    private function getVoucherHistoryData($partyguid, $ledgerId, $startDate, $endDate)
	{
		try {
			$rows = collect(DB::select(
				'EXEC dbo.GetVchHistoryByLedger ?, ?, ?, ?',
				[
					$partyguid,
					$ledgerId,
					$startDate,
					$endDate
				]
			));

			// Check if rows is empty and return appropriate structure
			if ($rows->isEmpty()) {
				return [
					'data' => [
						'processedRows' => [],
						'closingBalance' => 0,
						'closingSide' => 'Dr',
					],
					'meta' => [
						'ledger_name' => '',
						'start_date' => $startDate,
						'end_date' => $endDate,
						'opening_balance' => 0,
						'closing_balance' => 0,
						'total_dr' => 0,
						'total_cr' => 0,
					]
				];
			}

			// Calculate opening and closing balances
			$num = function ($v) {
				if ($v === null) return 0.0;
				$s = str_replace(',', '', (string)$v);
				return is_numeric($s) ? (float)$s : 0.0;
			};

			$first = $rows->first();
			$last  = $rows->last();

			$openingRaw = property_exists($first, 'OpeningBalance') ? 
				$num($first->OpeningBalance) : 
				($num($first->decRunningBalance) - ($num($first->DRAmount) - $num($first->CRAmount)));

			$closingRaw = property_exists($last, 'ClosingBalance') ? 
				$num($last->ClosingBalance) : 
				$num($last->decRunningBalance);

			// Calculate totals
			$totalDr = 0.0;
			$totalCr = 0.0;

			foreach ($rows as $r) {
				$totalDr += $num($r->DRAmount);
				$totalCr += $num($r->CRAmount);
			}

			// Get ledger name
			$ledgerName = '';
			if ($ledgerId) {
				$ledger = DB::table('LedgerMaster')->where('iLedgerId', $ledgerId)->first();
				$ledgerName = $ledger->strCustomerName ?? '';
			}

			// Process data for VoucherHistoryExport (same as web)
			$processedData = $this->processVoucherHistoryDataForExport($rows, $openingRaw, $startDate, $endDate);

			return [
				'data' => $processedData,
				'meta' => [
					'ledger_name' => $ledgerName,
					'start_date' => $startDate,
					'end_date' => $endDate,
					'opening_balance' => $openingRaw,
					'closing_balance' => $closingRaw,
					'total_dr' => $totalDr,
					'total_cr' => $totalCr,
				]
			];

		} catch (\Throwable $e) {
			\Log::error('Voucher History Data Error: ' . $e->getMessage());
			// Return empty structure instead of null
			return [
				'data' => [
					'processedRows' => [],
					'closingBalance' => 0,
					'closingSide' => 'Dr',
				],
				'meta' => [
					'ledger_name' => '',
					'start_date' => $startDate,
					'end_date' => $endDate,
					'opening_balance' => 0,
					'closing_balance' => 0,
					'total_dr' => 0,
					'total_cr' => 0,
				]
			];
		}
	}

    // Helper method to process voucher history data for export (same as web)
    private function processVoucherHistoryDataForExport($rows, $openingBalance, $from, $to)
    {
        $toFloat = function ($v) {
            if ($v === null || $v === '') return 0.0;
            return (float) str_replace(',', '', (string) $v);
        };

        $lastRunningBalance = 0.0;
        if ($rows->isNotEmpty()) {
            $lastRow = $rows->last();
            $lastRunningBalance = $toFloat($lastRow->decRunningBalance ?? 0);
        }

        $closingBalance = abs($lastRunningBalance);
        $closingSide = $lastRunningBalance >= 0 ? 'Dr' : 'Cr';

        $processedRows = [];
        $previousBalance = $openingBalance;

        // Add opening balance as first row
        $processedRows[] = (object) [
            'is_opening' => true,
            'strVchDate' => $from ? date('d-m-Y', strtotime($from . ' -1 day')) : '',
            'vchNo' => 'OPENING BALANCE',
            'vchType' => 'Opening',
            'trnAccount' => 'Balance B/F',
            'DRAmount' => 0,
            'CRAmount' => 0,
            'opening_balance' => $previousBalance,
            'decRunningBalance' => $previousBalance,
            'side' => $previousBalance >= 0 ? 'Dr' : 'Cr',
        ];

        // Process actual voucher rows
        foreach ($rows as $r) {
            $drRaw = $toFloat($r->DRAmount ?? 0);
            $crRaw = $toFloat($r->CRAmount ?? 0);
            $currentClosing = $toFloat($r->decRunningBalance ?? 0);
            $currentOpening = $previousBalance;

            $side = $currentClosing >= 0 ? 'Dr' : 'Cr';

            $processedRow = (object) array_merge((array) $r, [
                'is_opening' => false,
                'is_closing' => false,
                'opening_balance' => $currentOpening,
                'side' => $side,
            ]);

            $processedRows[] = $processedRow;
            $previousBalance = $currentClosing;
        }

        // Add closing balance as last row
        $processedRows[] = (object) [
            'is_closing' => true,
            'strVchDate' => $to ?: now()->format('d-m-Y'),
            'vchNo' => 'CLOSING BALANCE',
            'vchType' => 'Closing',
            'trnAccount' => 'Balance C/F',
            'DRAmount' => 0,
            'CRAmount' => 0,
            'decRunningBalance' => $lastRunningBalance,
            'side' => $closingSide,
        ];

        return [
            'processedRows' => $processedRows,
            'closingBalance' => $closingBalance,
            'closingSide' => $closingSide,
        ];
    }

    // LEDGER EXPORT METHODS - MODIFIED TO WORK WITH EXISTING EXPORT CLASSES
    // public function exportLedgerExcel(Request $request)
	// {
	// 	try {
	// 		$user = auth('api')->user();
	// 		if (!$user) {
	// 			return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
	// 		}

	// 		$validator = Validator::make($request->all(), [
	// 			'partyguid'  => 'required|uuid',
	// 			'iGroupId' => 'nullable|integer', // Changed to nullable to match web code
	// 			'start_date' => 'nullable|date|date_format:d-m-Y',
	// 			'end_date'   => 'nullable|date|date_format:d-m-Y|after_or_equal:start_date',
	// 			'strCustomerName' => 'nullable|string',
	// 		]);

	// 		if ($validator->fails()) {
	// 			return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
	// 		}

	// 		$partyguid = $request->partyguid;
	// 		$startDate = $request->start_date ? date('Y-m-d', strtotime($request->start_date)) : null;
	// 		$endDate   = $request->end_date ? date('Y-m-d', strtotime($request->end_date)) : null;

	// 		// Verify party GUID - FIXED: Check if count > 0
	// 		$exists = DB::select('EXEC CheckPartyGUIDCount ?', [$partyguid]);
	// 		if (!$exists || (isset($exists[0]) && $exists[0]->Count == 0)) {
	// 			return response()->json(['success' => false, 'message' => 'Invalid PartyGUID'], 422);
	// 		}

	// 		// Get party ID from partyguid instead of user ID
	// 		/*$party = DB::table('Client')->where('guid', $partyguid)->first();
	// 		if (!$party) {
	// 			return response()->json(['success' => false, 'message' => 'Party not found'], 422);
	// 		}*/
	// 		$partyId = $user->id;
	// 		$groupId = $request->iGroupId ?? 0;
	// 		$svc = app(ReportsService::class); 
	// 		$ledgerData = $svc->ledger($partyId, $groupId, $startDate, $endDate, $request->strCustomerName);
	// 		/*$ledgerData = $this->getLedgerData(
	// 			$partyId, 
	// 			$request->iGroupId, 
	// 			$startDate, 
	// 			$endDate, 
	// 			$request->strCustomerName
	// 		);*/

	// 		if (!$ledgerData || empty($ledgerData['data'])) {
	// 			return response()->json(['success' => false, 'message' => 'No Ledger data found'], 404);
	// 		}

	// 		// Get group name for display - ADDED THIS SECTION
	// 		$groupName = '';
	// 		if ($request->iGroupId) {
	// 			$group = DB::table('GroupMaster')
	// 				->where('iGroupId', $request->iGroupId)
	// 				->where('PartyGUID', $partyguid)
	// 				->first();
	// 			$groupName = $group->strGroupName ?? '';
	// 		}

	// 		$filename = 'ledger-report-' . ($request->start_date ?: 'start') . '-to-' . ($request->end_date ?: 'end') . '.xlsx';

	// 		// Use public disk
	// 		$disk = Storage::disk('public');
	// 		$directory = 'exports';

	// 		if (!$disk->exists($directory)) {
	// 			$disk->makeDirectory($directory, 0755, true);
	// 		}

	// 		$filePath = $directory . '/' . $filename;

	// 		// Store file using public disk - FIXED PARAMETERS
	// 		$exportResult = Excel::store(
	// 			new LedgerExport(
	// 				$ledgerData['data'], // data
	// 				$request->start_date, // from
	// 				$request->end_date,   // to
	// 				$groupName, // groupName - using the one we fetched
	// 				$request->strCustomerName // customerName
	// 			), 
	// 			$filePath, 
	// 			'public'
	// 		);

	// 		if (!$exportResult) {
	// 			throw new \Exception('Failed to store Excel file');
	// 		}

	// 		if (!$disk->exists($filePath)) {
	// 			throw new \Exception('Excel file was not created in storage');
	// 		}

	// 		// Get file URL
	// 		$fileUrl = asset('storage/' . $filePath);

	// 		return response()->json([
	// 			'success' => true,
	// 			'message' => 'Excel file generated successfully',
	// 			'download_url' => $fileUrl,
	// 			'filename' => $filename,
	// 			'file_size' => $disk->size($filePath),
	// 		], 200);

	// 	} catch (\Throwable $e) {
	// 		\Log::error('Ledger Excel Export Error: ' . $e->getMessage());

	// 		return response()->json([
	// 			'success' => false, 
	// 			'message' => 'Failed to generate Excel file', 
	// 			'error' => $e->getMessage()
	// 		], 500);
	// 	}
	// }
	private function prepareLedgerExportPayload(Request $request): array
    {
        $user = auth('api')->user();
        if (!$user) {
            return ['response' => response()->json(['success' => false, 'message' => 'Unauthenticated'], 401)];
        }
		$validator = Validator::make($request->all(), [
            'partyguid' => 'required|uuid',
            'iGroupId' => 'nullable|integer',
            'group_id' => 'nullable|integer',
            'strCustomerName' => 'nullable|string',
            'range' => 'nullable',
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
            'from_custom' => 'nullable|date',
            'to_custom' => 'nullable|date|after_or_equal:from_custom',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return ['response' => response()->json(['success' => false, 'errors' => $validator->errors()], 422)];
        }
		$partyguid = $request->partyguid;
        $exists = DB::select('EXEC CheckPartyGUIDCount ?', [$partyguid]);
        if (!$exists || (isset($exists[0]) && isset($exists[0]->Count) && (int) $exists[0]->Count === 0)) {
            return ['response' => response()->json(['success' => false, 'message' => 'Invalid PartyGUID'], 422)];
        }

		$partyId = $user->id;
        $groupId = (int) ($request->input('iGroupId', $request->input('group_id', 0)) ?: 0);
        $customerName = $request->input('strCustomerName');
        [, , $startDate, $endDate] = $this->resolveApiFinancialYearFilter($request, $partyId);

		$svc = app(ReportsService::class);
        $ledgerResult = $svc->ledger($partyId, $groupId, $startDate, $endDate, $customerName);
        $rows = collect(data_get($ledgerResult, 'data.rows', []))->filter(function ($r) {
            $op = $this->num($r->decOpBl ?? 0);
            $dr = $this->num($r->decDr ?? 0);
            $cr = $this->num($r->decCr ?? 0);
            $cl = $this->num($r->decClBl ?? 0);
			return !($op == 0.0 && $dr == 0.0 && $cr == 0.0 && $cl == 0.0);
        })->values();
		if (!$ledgerResult['success'] || $rows->isEmpty()) {
            return ['response' => response()->json(['success' => false, 'message' => 'No Ledger data found'], 404)];
        }
		$ledgerData = data_get($ledgerResult, 'data', []);
        $ledgerData['rows'] = $rows;
		$groupName = '';
        if ($groupId) {
            $group = DB::table('GroupMaster')
                ->where('iGroupId', $groupId)
                ->where('iPartyId', $partyId)
                ->first();
            $groupName = $group->strGroupName ?? '';
        }
        $profile = $user->profile ?? null;
		return [
            'data' => $ledgerData,
            'from' => $startDate,
            'to' => $endDate,
            'fromDisplay' => $startDate ? date('d-m-Y', strtotime($startDate)) : '',
            'toDisplay' => $endDate ? date('d-m-Y', strtotime($endDate)) : '',
            'groupName' => $groupName,
            'customerName' => $customerName,
            'partyName' => $user->name ?? '',
            'companyAddress' => $profile->address ?? '',
            'companyEmail' => $user->email ?? '',
        ];
    }

	private function ledgerExportFilename(string $prefix, Request $request, string $extension): string
    {
        $from = $request->input('start_date') ?: $request->input('from') ?: $request->input('from_custom') ?: 'start';
        $to = $request->input('end_date') ?: $request->input('to') ?: $request->input('to_custom') ?: 'end';
		return $prefix . '-' . str_replace('/', '-', $from) . '-to-' . str_replace('/', '-', $to) . '.' . $extension;
    }

	private function buildLedgerPdf(array $prepared)
    {
        return Pdf::setOptions([
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
        ])->loadView('reports.pdf.ledger_pdf', [
            'data' => $prepared['data'],
            'from' => $prepared['fromDisplay'],
            'to' => $prepared['toDisplay'],
            'groupName' => $prepared['groupName'],
            'customerName' => $prepared['customerName'],
            'partyName' => $prepared['partyName'],
            'companyAddress' => $prepared['companyAddress'],
            'companyEmail' => $prepared['companyEmail'],
        ]);
    }
    // LEDGER EXPORT METHODS - MODIFIED TO WORK WITH EXISTING EXPORT CLASSES
	public function exportLedgerExcel(Request $request)
	{
		try {
            $prepared = $this->prepareLedgerExportPayload($request);
            if (isset($prepared['response'])) {
                return $prepared['response'];
            }
			$filename = $this->ledgerExportFilename('ledger-report', $request, 'xlsx');
            $disk = Storage::disk('public');
            $directory = 'exports';

            if (!$disk->exists($directory)) {
                $disk->makeDirectory($directory, 0755, true);
            }

            $filePath = $directory . '/' . $filename;
            $exportResult = Excel::store(
                new LedgerExport(
                    $prepared['data'],
                    $prepared['fromDisplay'],
                    $prepared['toDisplay'],
                    $prepared['groupName'],
                    $prepared['customerName'],
                    $prepared['partyName'],
                    $prepared['companyAddress'],
                    $prepared['companyEmail']
                ),
                $filePath,
                'public'
            );

            if (!$exportResult || !$disk->exists($filePath)) {
                throw new \Exception('Excel file was not created in storage');
            }

            return response()->json([
                'success' => true,
                'message' => 'Excel file generated successfully',
                'download_url' => route('api.ledger.export.download', ['filename' => $filename]),
                'filename' => $filename,
                'file_size' => $disk->size($filePath),
            ], 200);

		} catch (\Throwable $e) {
			\Log::error('Ledger Excel Export Error: ' . $e->getMessage());

			return response()->json([
				'success' => false,
				'message' => 'Failed to generate Excel file',
				'error' => $e->getMessage()
			], 500);
		}
	}
				

    public function exportLedgerSummaryExcel(Request $request)
    {
        try {
            $user = auth('api')->user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
            }

            $validator = Validator::make($request->all(), [
                'partyguid'  => 'required|uuid',
                //'iGroupId' => 'required|integer',
                'start_date' => 'nullable|date|date_format:d-m-Y',
                'end_date'   => 'nullable|date|date_format:d-m-Y|after_or_equal:start_date',
                'strCustomerName' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $partyguid = $request->partyguid;
            $startDate = $request->start_date ? date('Y-m-d', strtotime($request->start_date)) : null;
            $endDate   = $request->end_date ? date('Y-m-d', strtotime($request->end_date)) : null;

            // Verify party GUID
            $exists = DB::select('EXEC CheckPartyGUIDCount ?', [$partyguid]);
            if (!$exists) {
                return response()->json(['success' => false, 'message' => 'Invalid PartyGUID'], 422);
            }

            $partyId = $user->id;
            $ledgerData = $this->getLedgerData(
                $partyId, 
                $request->iGroupId, 
                $startDate, 
                $endDate, 
                $request->strCustomerName
            );

            if (!$ledgerData) {
                return response()->json(['success' => false, 'message' => 'No Ledger data found'], 404);
            }

            $filename = 'ledger-summary-' . ($request->start_date ?: 'start') . '-to-' . ($request->end_date ?: 'end') . '.xlsx';

            $disk = Storage::disk('public');
            $directory = 'exports';
            
            if (!$disk->exists($directory)) {
                $disk->makeDirectory($directory, 0755, true);
            }

            $filePath = $directory . '/' . $filename;
            
            // Store file using public disk - PASS ALL REQUIRED PARAMETERS
            $exportResult = Excel::store(
                new LedgerSummaryExport(
                    $ledgerData['data'], // data
                    $request->start_date, // from
                    $request->end_date    // to
                ), 
                $filePath, 
                'public'
            );

            if (!$exportResult) {
                throw new \Exception('Failed to store Excel file');
            }

            if (!$disk->exists($filePath)) {
                throw new \Exception('Excel file was not created in storage');
            }

            $fileUrl = asset('storage/' . $filePath);

            return response()->json([
                'success' => true,
                'message' => 'Excel summary file generated successfully',
                'download_url' => $fileUrl,
                'filename' => $filename,
                'file_size' => $disk->size($filePath),
            ], 200);

        } catch (\Throwable $e) {
            \Log::error('Ledger Summary Excel Export Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false, 
                'message' => 'Failed to generate Excel summary file', 
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // DIRECT DOWNLOAD METHODS - MODIFIED TO WORK WITH EXISTING EXPORT CLASSES

    public function downloadLedgerExcel(Request $request)
    {
        try {
            $prepared = $this->prepareLedgerExportPayload($request);
            if (isset($prepared['response'])) {
                return $prepared['response'];
            }

            return Excel::download(
                new LedgerExport(
                     $prepared['data'],
                    $prepared['fromDisplay'],
                    $prepared['toDisplay'],
                    $prepared['groupName'],
                    $prepared['customerName'],
                    $prepared['partyName'],
                    $prepared['companyAddress'],
                    $prepared['companyEmail']
				),
                $this->ledgerExportFilename('ledger-report', $request, 'xlsx')
            );

        } catch (\Throwable $e) {
            \Log::error('Ledger Direct Excel Download Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate Excel file',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function downloadVoucherHistoryExcel(Request $request)
    {
        try {
            $user = auth('api')->user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
            }

            $validator = Validator::make($request->all(), [
                'partyguid'  => 'required|uuid',
                'iledgerid' => 'required|integer',
                'start_date' => 'nullable|date|date_format:d-m-Y',
                'end_date'   => 'nullable|date|date_format:d-m-Y|after_or_equal:start_date',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $partyguid = $request->partyguid;
            $startDate = $request->start_date ? date('Y-m-d', strtotime($request->start_date)) : null;
            $endDate   = $request->end_date ? date('Y-m-d', strtotime($request->end_date)) : null;

            $exists = DB::select('EXEC CheckPartyGUIDCount ?', [$partyguid]);
            if (!$exists) {
                return response()->json(['success' => false, 'message' => 'Invalid PartyGUID'], 422);
            }

            $voucherData = $this->getVoucherHistoryData(
                $partyguid, 
                $request->iledgerid, 
                $startDate, 
                $endDate
            );

            if (!$voucherData) {
                return response()->json(['success' => false, 'message' => 'No Voucher History data found'], 404);
            }

            $filename = 'voucher-history-' . ($request->start_date ?: 'start') . '-to-' . ($request->end_date ?: 'end') . '.xlsx';

            return Excel::download(
                new VoucherHistoryExport(
                    $voucherData['data'],
                    $request->start_date,
                    $request->end_date,
                    $request->iledgerid,
                    $voucherData['meta']['ledger_name'],
                    $voucherData['meta']['opening_balance'],
                    $voucherData['data']['closingBalance'],
                    $voucherData['meta']['total_dr'],
                    $voucherData['meta']['total_cr']
                ), 
                $filename
            );

        } catch (\Throwable $e) {
            \Log::error('Voucher History Direct Excel Download Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false, 
                'message' => 'Failed to generate Excel file', 
                'error' => $e->getMessage()
            ], 500);
        }
    }

	// LEDGER PDF EXPORT METHODS
	public function exportLedgerPdf(Request $request)
	{
		try {
			$prepared = $this->prepareLedgerExportPayload($request);
            if (isset($prepared['response'])) {
                return $prepared['response'];
            }

			$pdf = $this->buildLedgerPdf($prepared);
            $filename = $this->ledgerExportFilename('ledger-report', $request, 'pdf');
            $disk = Storage::disk('public');
            $directory = 'exports';

			if (!$disk->exists($directory)) {
                $disk->makeDirectory($directory, 0755, true);
            }

			$filePath = $directory . '/' . $filename;
            $disk->put($filePath, $pdf->output());

			if (!$disk->exists($filePath)) {
                throw new \Exception('PDF file was not created in storage');
            }
			
			return response()->json([
                'success' => true,
                'message' => 'PDF file generated successfully',
                'download_url' => route('api.ledger.export.download', ['filename' => $filename]),
                'filename' => $filename,
                'file_size' => $disk->size($filePath),
            ], 200);

		} catch (\Throwable $e) {
			\Log::error('Ledger PDF Export Error: ' . $e->getMessage());

			return response()->json([
				'success' => false,
				'message' => 'Failed to generate PDF file',
				'error' => $e->getMessage()
			], 500);
		}
	}

    // EXPORTED FILE DOWNLOAD ROUTE
	public function downloadExportedFile(string $filename)
	{
		if (!preg_match('/\A(?:ledger-report|voucher-history)-[A-Za-z0-9._-]+-to-[A-Za-z0-9._-]+\.(xlsx|pdf)\z/', $filename)) {
			abort(404);
		}

		$filePath = 'exports/' . $filename;
		$disk = Storage::disk('public');

		if (!$disk->exists($filePath)) {
			abort(404, 'Export file not found');
		}

		return $disk->download($filePath, $filename);
	}

	// DIRECT PDF DOWNLOAD METHODS
	public function downloadLedgerPdf(Request $request)
	{
		try {
			$prepared = $this->prepareLedgerExportPayload($request);
            if (isset($prepared['response'])) {
                return $prepared['response'];
            }

			return $this->buildLedgerPdf($prepared)
                ->download($this->ledgerExportFilename('ledger-report', $request, 'pdf'));

		} catch (\Throwable $e) {
			\Log::error('Ledger Direct PDF Download Error: ' . $e->getMessage());

			return response()->json([
				'success' => false,
				'message' => 'Failed to generate PDF file',
				'error' => $e->getMessage()
			], 500);
		}
	}

	// public function downloadVoucherHistoryPdf(Request $request)
	// {
	// 	try {
	// 		$user = auth('api')->user();
	// 		if (!$user) {
	// 			return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
	// 		}

	// 		$validator = Validator::make($request->all(), [
	// 			'partyguid'  => 'required|uuid',
	// 			'iledgerid' => 'required|integer',
	// 			'start_date' => 'nullable|date|date_format:d-m-Y',
	// 			'end_date'   => 'nullable|date|date_format:d-m-Y|after_or_equal:start_date',
	// 		]);

	// 		if ($validator->fails()) {
	// 			return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
	// 		}

	// 		$partyguid = $request->partyguid;
	// 		$startDate = $request->start_date ? date('Y-m-d', strtotime($request->start_date)) : null;
	// 		$endDate   = $request->end_date ? date('Y-m-d', strtotime($request->end_date)) : null;

	// 		$exists = DB::select('EXEC CheckPartyGUIDCount ?', [$partyguid]);
	// 		if (!$exists) {
	// 			return response()->json(['success' => false, 'message' => 'Invalid PartyGUID'], 422);
	// 		}

	// 		$voucherData = $this->getVoucherHistoryData(
	// 			$partyguid, 
	// 			$request->iledgerid, 
	// 			$startDate, 
	// 			$endDate
	// 		);

	// 		if (!$voucherData) {
	// 			return response()->json(['success' => false, 'message' => 'No Voucher History data found'], 404);
	// 		}

	// 		$filename = 'voucher-history-' . ($request->start_date ?: 'start') . '-to-' . ($request->end_date ?: 'end') . '.pdf';

	// 		$pdf = Pdf::setOptions(['isRemoteEnabled' => true])
	// 				->loadView('reports.pdf.voucher_history_pdf', [
	// 					'processedRows' => $voucherData['data']['processedRows'] ?? [],
	// 					'from' => $request->start_date,
	// 					'to' => $request->end_date,
	// 					'ledgerId' => $request->iledgerid,
	// 					'openingBalance' => $voucherData['meta']['opening_balance'] ?? 0,
	// 					'openingSide' => $voucherData['data']['closingSide'] ?? 'Dr',
	// 					'closingBalance' => $voucherData['data']['closingBalance'] ?? 0,
	// 					'closingSide' => $voucherData['data']['closingSide'] ?? 'Dr',
	// 					'totalDr' => $voucherData['meta']['total_dr'] ?? 0,
	// 					'totalCr' => $voucherData['meta']['total_cr'] ?? 0,
	// 				]);

	// 		return $pdf->download($filename);

	// 	} catch (\Throwable $e) {
	// 		\Log::error('Voucher History Direct PDF Download Error: ' . $e->getMessage());

	// 		return response()->json([
	// 			'success' => false, 
	// 			'message' => 'Failed to generate PDF file', 
	// 			'error' => $e->getMessage()
	// 		], 500);
	// 	}
	// }
	
	public function exportVoucherHistoryExcel(Request $request, ReportsService $svc)
	{
		try {
			$user = auth('api')->user();
			if (!$user) {
				return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
			}

			$validator = Validator::make($request->all(), [
				'partyguid'  => 'required|uuid',
				'iledgerid' => 'required|integer',
				'start_date' => 'nullable|date|date_format:d-m-Y',
				'end_date'   => 'nullable|date|date_format:d-m-Y|after_or_equal:start_date',
			]);

			if ($validator->fails()) {
				return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
			}

			$partyguid = $request->partyguid;
			$ledgerId = $request->iledgerid;

			// Verify party GUID
			$exists = DB::select('EXEC CheckPartyGUIDCount ?', [$partyguid]);
			if (!$exists) {
				return response()->json(['success' => false, 'message' => 'Invalid PartyGUID'], 422);
			}

			// Use the SAME service as web
			$resp = $svc->voucherHistory($partyguid, $ledgerId, $request->start_date, $request->end_date);
			$data = data_get($resp, 'data', []);

			// Get ledger name
			$ledgerName = '';
			if ($ledgerId) {
				$ledger = DB::table('LedgerMaster')->where('iLedgerId', $ledgerId)->first();
				$ledgerName = $ledger->strCustomerName ?? '';
			}
            $profile = $user->profile ?? null;
			$partyName = $user->name ?? '';
			$companyAddress = $profile->address ?? '';
			$companyEmail = $user->email ?? '';

			// Get opening balance - SAME logic as web
			$openingBalanceData = [];
			if ($request->start_date) {
				$previousDay = date('Y-m-d', strtotime($request->start_date . ' -1 day'));
				$openingBalanceData = $svc->getOpeningBalance($partyguid, $ledgerId, $previousDay);
			} else {
				$openingBalanceData = ['balance' => 0.0, 'side' => 'Dr'];
			}
			
			// Process data for export - SAME logic as web
			$processedData = $this->processVoucherHistoryData($data, $openingBalanceData, $request->start_date, $request->end_date);

			$filename = 'voucher-history-' . ($request->start_date ?: 'start') . '-to-' . ($request->end_date ?: 'end') . '.xlsx';

			$disk = Storage::disk('public');
			$directory = 'exports';

			if (!$disk->exists($directory)) {
				$disk->makeDirectory($directory, 0755, true);
			}

			$filePath = $directory . '/' . $filename;

			// Store file using public disk - use SAME parameters as web
			$exportResult = Excel::store(
				new VoucherHistoryExport(
					$processedData, // Use processed data from web method
					$request->start_date,
					$request->end_date,
					$ledgerId,					
					$openingBalanceData['balance'] ?? 0,
					$processedData['closingBalance'] ?? 0,
					$data['raw_total_dr'] ?? 0,
					$data['raw_total_cr'] ?? 0,
					$partyName,
					$companyAddress,
					$companyEmail
				), 
				$filePath, 
				'public'
			);

			if (!$exportResult) {
				throw new \Exception('Failed to store Excel file');
			}

			// $fileUrl = asset('storage/' . $filePath);
            if (!$disk->exists($filePath)) {
				throw new \Exception('Excel file was not created in storage');
			}
            $fileUrl = route('api.ledger.export.download', ['filename' => $filename]);

			$message = empty($processedData['processedRows']) ? 
				'Excel file generated successfully (No data found for the selected period)' : 
				'Excel file generated successfully';

			return response()->json([
				'success' => true,
				'message' => $message,
				'download_url' => $fileUrl,
				'filename' => $filename,
				'file_size' => $disk->size($filePath),
				'records_count' => count($processedData['processedRows'] ?? [])
			], 200);

		} catch (\Throwable $e) {
			\Log::error('Voucher History Excel Export Error: ' . $e->getMessage());

			return response()->json([
				'success' => false, 
				'message' => 'Failed to generate Excel file', 
				'error' => $e->getMessage()
			], 500);
		}
	}

	public function exportVoucherHistoryPdf(Request $request, ReportsService $svc)
	{
		try {
			$user = auth('api')->user();
			if (!$user) {
				return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
			}

			$validator = Validator::make($request->all(), [
				'partyguid'  => 'required|uuid',
				'iledgerid' => 'required|integer',
				'start_date' => 'nullable|date|date_format:d-m-Y',
				'end_date'   => 'nullable|date|date_format:d-m-Y|after_or_equal:start_date',
			]);

			if ($validator->fails()) {
				return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
			}

			$partyguid = $request->partyguid;
			$ledgerId = $request->iledgerid;

			// Verify party GUID
			$exists = DB::select('EXEC CheckPartyGUIDCount ?', [$partyguid]);
			if (!$exists) {
				return response()->json(['success' => false, 'message' => 'Invalid PartyGUID'], 422);
			}
			$profile = $user->profile ?? null;
			$partyName = $user->name ?? '';
			$companyAddress = $profile->address ?? '';
			$companyEmail = $user->email ?? '';
			// Use the SAME service as web
			$resp = $svc->voucherHistory($partyguid, $ledgerId, $request->start_date, $request->end_date);
			$data = data_get($resp, 'data', []);

			// Get opening balance - SAME logic as web
			$openingBalanceData = [];
			if ($request->start_date) {
				$previousDay = date('Y-m-d', strtotime($request->start_date . ' -1 day'));
				$openingBalanceData = $svc->getOpeningBalance($partyguid, $ledgerId, $previousDay);
			} else {
				$openingBalanceData = ['balance' => 0.0, 'side' => 'Dr'];
			}
			
			$ledgerName = '';
			if ($ledgerId) {
				$ledger = DB::table('LedgerMaster')->where('iLedgerId', $ledgerId)->first();
				$ledgerName = $ledger->strCustomerName ?? '';
			}
			
			// Process data for PDF - SAME logic as web
			$processedData = $this->processVoucherHistoryData($data, $openingBalanceData, $request->start_date, $request->end_date);

			$filename = 'voucher-history-' . ($request->start_date ?: 'start') . '-to-' . ($request->end_date ?: 'end') . '.pdf';

			// Generate PDF - use SAME parameters as web
			$pdf = Pdf::setOptions([
				'isRemoteEnabled' => true,
				'isHtml5ParserEnabled' => true,
				'isPhpEnabled' => true,
			])->loadView('reports.pdf.voucher_history_pdf', [
				'processedRows' => $processedData['processedRows'] ?? [],
				'from' => $request->start_date,
				'to' => $request->end_date,
				'ledgerId' => $ledgerId,
				'openingBalance' => $openingBalanceData['balance'] ?? 0,
				'openingSide' => $openingBalanceData['side'] ?? 'Dr',
				'closingBalance' => $processedData['closingBalance'] ?? 0,
				'closingSide' => $processedData['closingSide'] ?? 'Dr',
				'totalDr' => $data['raw_total_dr'] ?? 0,
				'totalCr' => $data['raw_total_cr'] ?? 0,
				'partyName' => $partyName,
				'ledgerName' => $ledgerName,
				'companyAddress' => $companyAddress,
				'companyEmail' => $companyEmail
			]);

			$disk = Storage::disk('public');
			$directory = 'exports';

			if (!$disk->exists($directory)) {
				$disk->makeDirectory($directory, 0755, true);
			}

			$filePath = $directory . '/' . $filename;

			$disk->put($filePath, $pdf->output());

			// $fileUrl = asset('storage/' . $filePath);
            if (!$disk->exists($filePath)) {
				throw new \Exception('PDF file was not created in storage');
			}

			$fileUrl = route('api.ledger.export.download', ['filename' => $filename]);

			return response()->json([
				'success' => true,
				'message' => 'PDF file generated successfully',
				'download_url' => $fileUrl,
				'filename' => $filename,
				'file_size' => $disk->size($filePath),
			], 200);

		} catch (\Throwable $e) {
			\Log::error('Voucher History PDF Export Error: ' . $e->getMessage());

			return response()->json([
				'success' => false, 
				'message' => 'Failed to generate PDF file', 
				'error' => $e->getMessage()
			], 500);
		}
	}
	
	private function processVoucherHistoryData($data, $openingBalanceData, $from, $to)
    {
        $rows = $data['rows'] ?? [];
        $toFloat = function ($v) {
            if ($v === null || $v === '') return 0.0;
            return (float) str_replace(',', '', (string) $v);
        };

        $openingBalance = $openingBalanceData['balance'] ?? 0.0;
        $openingSide = $openingBalanceData['side'] ?? 'Dr';

        // Get the last running balance for closing
        $lastRunningBalance = 0.0;
        if ($rows->isNotEmpty()) {
            $lastRow = $rows->last();
            $lastRunningBalance = $toFloat($lastRow->decRunningBalance ?? 0);
        }

        $closingBalance = abs($lastRunningBalance);
        $closingSide = $lastRunningBalance >= 0 ? 'Dr' : 'Cr';

        $processedRows = [];
        $previousBalance = $openingSide === 'Dr' ? $openingBalance : -$openingBalance;

        // Add opening balance as first row
        $processedRows[] = (object) [
            'is_opening' => true,
            'strVchDate' => $from ? date('d-m-Y', strtotime($from . ' -1 day')) : '',
            'vchNo' => 'OPENING BALANCE',
            'vchType' => 'Opening',
            'trnAccount' => 'Balance B/F',
            'DRAmount' => 0,
            'CRAmount' => 0,
            'opening_balance' => $previousBalance,
            'decRunningBalance' => $previousBalance,
            'side' => $openingSide,
        ];

        // Process actual voucher rows
        foreach ($rows as $r) {
            $drRaw = $toFloat($r->DRAmount ?? 0);
            $crRaw = $toFloat($r->CRAmount ?? 0);
            $currentClosing = $toFloat($r->decRunningBalance ?? 0);
            $currentOpening = $previousBalance;

            $side = $currentClosing >= 0 ? 'Dr' : 'Cr';

            $processedRow = (object) array_merge((array) $r, [
                'is_opening' => false,
                'is_closing' => false,
                'opening_balance' => $currentOpening,
                'side' => $side,
            ]);

            $processedRows[] = $processedRow;
            $previousBalance = $currentClosing;
        }

        // Add closing balance as last row
        $processedRows[] = (object) [
            'is_closing' => true,
            'strVchDate' => $to ?: now()->format('d-m-Y'),
            'vchNo' => 'CLOSING BALANCE',
            'vchType' => 'Closing',
            'trnAccount' => 'Balance C/F',
            'DRAmount' => 0,
            'CRAmount' => 0,
            'decRunningBalance' => $lastRunningBalance,
            'side' => $closingSide,
        ];

        return [
            'processedRows' => $processedRows,
            'closingBalance' => $closingBalance,
            'closingSide' => $closingSide,
        ];
    }
}
