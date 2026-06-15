<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\ReportsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Exports\PandLExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\BalanceSheetExport;
use App\Exports\LedgerExport;
use App\Exports\LedgerSummaryExport;
use App\Exports\VoucherHistoryExport;
use App\Models\Client;

class ReportsController extends Controller
{
    /**
     * Profit & Loss page
     * Expects your API endpoint: GET /api/reports/pl?from=YYYY-MM-DD&to=YYYY-MM-DD
     */
    public function pl(Request $r, \App\Services\ReportsService $svc)
    {
        $toDMY = fn($d) => $d ? \Carbon\Carbon::parse($d)->format('d-m-Y') : '';
        $resp = $svc->pandl($r->user()->id, $toDMY($r->input('from')), $toDMY($r->input('to')));
        $rangeSel = $r->input('range');
        $from = $r->input('from');
        $to = $r->input('to');
        $pl = data_get($resp, 'data', []);
		$from = $r->input('from') ? date('d-m-Y',strtotime($r->input('from'))) : '';
		$to = $r->input('to') ? date('d-m-Y',strtotime($r->input('to'))) : '';
        return view('reports.pl', compact('resp', 'from', 'to', 'pl', 'rangeSel'));
    }

    public function exportPdf(Request $r, ReportsService $svc)
    {
        if ($r->guid) {
            $user = Client::where('guid', $r->guid)->first();
            $partyId = $user->id;
        } else {
            $partyId = $r->user()->id;
        }
        $partyName = $r->user()->name;
        $from = $r->input('from');
        $to = $r->input('to');
        $toDMY = fn($d) => $d ? \Carbon\Carbon::parse($d)->format('d-m-Y') : '';

        $resp = $svc->pandl($partyId, $toDMY($from), $toDMY($to));
        $pl = data_get($resp, 'data', []);
        $from = $r->input('from');
        $to = $r->input('to');

        $pdf = Pdf::loadView('reports.pdf.pl-pdf', compact('pl', 'from', 'to', 'partyName'));
		
        $filename = 'profit-loss-report-' . ($from ?: 'start') . '-to-' . ($to ?: 'end') . '.pdf';

        return $pdf->download($filename);
    }

    public function exportExcel(Request $r, ReportsService $svc)
    {
        // $partyId = $r->user()->id;
        if ($r->guid) {
            $user = Client::where('guid', $r->guid)->first();
            $partyId = $user->id;
        } else {
            $partyId = $r->user()->id;
        }
        $from = $r->input('from');
        $to = $r->input('to');
        //$partyName = $r->user()->name;
        $filename = 'profit-loss-report-' . ($from ?: 'start') . '-to-' . ($to ?: 'end') . '.xlsx';

        //return Excel::download(new PandLExport($svc, $partyId, $from, $to,$partyName), $filename);
        return Excel::download(new PandLExport($svc, $partyId, $from, $to), $filename);
    }

    public function balanceSheet(Request $r, ReportsService $svc)
    {
        // Existing code...
        $rangeSel = $r->input('range');
        $from = $r->input('from');
        $to   = $r->input('to');
        $guid = Auth::user()->guid ?? '';
        $partyguid = $guid;
        $partyId   = $r->user()->id;

        $resp = $svc->balanceSheet($partyguid, $partyId, $from, $to);
        $data = data_get($resp, 'data', []);
		$from = $r->input('from') ? date('d-m-Y',strtotime($r->input('from'))) : '';
		$to = $r->input('to') ? date('d-m-Y',strtotime($r->input('to'))) : '';
        return view('reports.balance_sheet', compact('resp', 'from', 'to', 'data', 'partyguid', 'rangeSel'));
    }

    public function exportBalanceSheetExcel(Request $r, ReportsService $svc)
    {
        $from = $r->input('from');
        $to   = $r->input('to');
        if ($r->guid) {
            $user = Client::where('guid', $r->guid)->first();
            $guid = $r->guid;
            $partyguid = $guid;
            $partyId = $user->id;
        } else {
            $guid = Auth::user()->guid ?? '';
            $partyguid = $guid;
            $partyId   = $r->user()->id;
        }

        $resp = $svc->balanceSheet($partyguid, $partyId, $from, $to);
        $data = data_get($resp, 'data', []);

        $filename = 'balance_sheet_' . date('Y_m_d') . '.xlsx';

        return Excel::download(new BalanceSheetExport($data, $from, $to), $filename);
    }

    public function exportBalanceSheetPDF(Request $r, ReportsService $svc)
    {
        $from = $r->input('from');
        $to   = $r->input('to');

        if ($r->guid) {
            $user = Client::where('guid', $r->guid)->first();
            $guid = $r->guid;
            $partyguid = $guid;
            $partyId = $user->id;
        } else {
            $guid = Auth::user()->guid ?? '';
            $partyguid = $guid;
            $partyId   = $r->user()->id;
        }
        // $guid = Auth::user()->guid ?? '';
        // $partyguid = $guid;
        // $partyId   = $r->user()->id;
        $partyName = $r->user()->name;
        $resp = $svc->balanceSheet($partyguid, $partyId, $from, $to);
        $data = data_get($resp, 'data', []);

        $pdf = PDF::loadView('reports.pdf.balance_sheet_pdf', [
            'data' => $data,
            'from' => $from,
            'to' => $to,
            'partyName' => $partyName
        ]);

        $filename = 'balance_sheet_' . date('Y_m_d') . '.pdf';

        return $pdf->download($filename);
    }

    public function ledger(Request $r, ReportsService $svc)
    {
        $rangeSel = $r->input('range');
        $from = $r->input('from');
        $to   = $r->input('to');
        $groupId = (int) $r->input('group_id'); // iGroupId
        $strCustomerName = $r->input('strCustomerName');
        $partyId = $r->user()->id;
        $guid = Auth::user()->guid;
        $partyguid = $guid; // provide via UI/session

        $resp = $svc->ledger($partyId, $groupId, $from, $to, $strCustomerName);
        $data = data_get($resp, 'data', []);
        $GroupMasters = DB::table('GroupMaster')
            ->where('PartyGUID', $partyguid)
            ->where('iPartyId', $partyId)
            ->get();
		$from = $r->input('from') ? date('d-m-Y',strtotime($r->input('from'))) : '';
		$to = $r->input('to') ? date('d-m-Y',strtotime($r->input('to'))) : '';
        return view('reports.ledger', compact('resp', 'from', 'to', 'strCustomerName', 'data', 'groupId', 'GroupMasters', 'rangeSel'));
    }

    public function voucherHistory(Request $r, ReportsService $svc)
    {
        $rangeSel = $r->input('range');
        $from = $r->input('from');
        $to   = $r->input('to');
        $ledgerId  = (int) $r->input('ledger_id');
        $guid = Auth::user()->guid;
        $partyguid = $guid;

        $resp = $svc->voucherHistory($partyguid, $ledgerId, $from, $to);
        $data = data_get($resp, 'data', []);

        // Get opening balance from the day before the from date
        $openingBalanceData = [];
        if ($from) {
            $previousDay = date('Y-m-d', strtotime($from . ' -1 day'));
            $openingBalanceData = $svc->getOpeningBalance($partyguid, $ledgerId, $previousDay);
        }
        $ledgerName = '';
        if ($ledgerId) {
            $ledger = DB::table('LedgerMaster')->where('iLedgerId', $ledgerId)->first();
            $ledgerName = $ledger->strCustomerName ?? '';
        }
		$from = $r->input('from') ? date('d-m-Y',strtotime($r->input('from'))) : '';
		$to = $r->input('to') ? date('d-m-Y',strtotime($r->input('to'))) : '';
        return view('reports.voucher_history', compact(
            'resp',
            'from',
            'to',
            'data',
            'ledgerId',
            'partyguid',
            'rangeSel',
            'openingBalanceData',
            'ledgerName'
        ));
    }

    public function graph(Request $r, ReportsService $svc)
    {
        $user = $r->user();
        abort_unless($user, 401);

        // Inputs from the form (GET)
        $type = (int) $r->input('type', 1); // 1..4 like your API
        $from = $r->input('from');          // Y-m-d (date input)
        $to   = $r->input('to');            // Y-m-d
        // Optional flags similar to your API
        $opts = [
            'outflow_negative' => (bool)$r->input('outflow_negative', false),
            'groups'           => $r->input('groups'),         // optional override
            'exclude_types'    => $r->input('exclude_types'),  // optional override
            'date_style'       => $r->input('date_style'),     // for type 4
        ];

        $res = $svc->monthlyGraph($user->id, $from, $to, $type, $opts);

        // Unpack for Blade
        $months   = $res['months'];
        $cashIn   = $res['cashIn'];
        $cashOut  = $res['cashOut'];
        $totals   = $res['totals'];   // ['totalIn','totalOut']
        $allTotals = $res['allTotals']; // 8-box summary
        $range    = $res['range'];    // ['from','to'] in Y-m-d
        $labelFY  = $res['fy_label']; // e.g., "FY 2024-25"

        return view('reports.graph', compact(
            'type',
            'months',
            'cashIn',
            'cashOut',
            'totals',
            'allTotals',
            'range',
            'labelFY'
        ));
    }

    public function exportLedgerExcel(Request $r, ReportsService $svc)
    {
        $from = $r->input('from');
        $to = $r->input('to');
        $groupId = (int) $r->input('group_id');
        $strCustomerName = $r->input('strCustomerName');
        if ($r->guid) {
            $user = Client::where('guid', $r->guid)->first();
            $partyId = $user->id;
            $guid = $r->guid;
            $partyguid = $guid;
        } else {
            $partyId = $r->user()->id;
            $guid = Auth::user()->guid;
            $partyguid = $guid;
        }
        $resp = $svc->ledger($partyId, $groupId, $from, $to, $strCustomerName);
        $data = data_get($resp, 'data', []);

        // Get group name for display
        $groupName = '';
        if ($groupId) {
            $group = DB::table('GroupMaster')
                ->where('iGroupId', $groupId)
                ->where('PartyGUID', $partyguid)
                ->first();
            $groupName = $group->strGroupName ?? '';
        }

        $filename = 'ledger_report_' . date('Y_m_d') . '.xlsx';

        return Excel::download(new LedgerExport($data, $from, $to, $groupName, $strCustomerName), $filename);
    }

    public function exportLedgerSummaryExcel(Request $r, ReportsService $svc)
    {
        $from = $r->input('from');
        $to = $r->input('to');
        $groupId = (int) $r->input('group_id');
        $strCustomerName = $r->input('strCustomerName');
        $partyId = $r->user()->id;
        $guid = Auth::user()->guid;
        $partyguid = $guid;

        $resp = $svc->ledger($partyId, $groupId, $from, $to, $strCustomerName);
        $data = data_get($resp, 'data', []);

        $filename = 'ledger_summary_' . date('Y_m_d') . '.xlsx';

        return Excel::download(new LedgerSummaryExport($data, $from, $to), $filename);
    }

    public function exportLedgerPDF(Request $r, ReportsService $svc)
    {

        $from = $r->input('from');
        $to = $r->input('to');
        $groupId = (int) $r->input('group_id');
        $strCustomerName = $r->input('strCustomerName');

        if ($r->guid) {
            $user = Client::where('guid', $r->guid)->first();
            $partyId = $user->id;
            $guid = $r->guid;
            $partyguid = $guid;
        } else {
            $partyId = $r->user()->id;
            $guid = Auth::user()->guid;
            $partyguid = $guid;
        }

        $resp = $svc->ledger($partyId, $groupId, $from, $to, $strCustomerName);
        $data = data_get($resp, 'data', []);

        // Get group name for display
        $groupName = '';
        if ($groupId) {
            $group = DB::table('GroupMaster')
                ->where('iGroupId', $groupId)
                ->where('PartyGUID', $partyguid)
                ->first();
            $groupName = $group->strGroupName ?? '';
        }
        $partyName = $r->user()->name;
        $pdf = PDF::loadView('reports.pdf.ledger_pdf', [
            'data' => $data,
            'from' => $from,
            'to' => $to,
            'groupName' => $groupName,
            'customerName' => $strCustomerName,
            'partyName' => $partyName
        ]);

        $filename = 'ledger_report_' . date('Y_m_d') . '.pdf';

        return $pdf->download($filename);
    }

    public function exportVoucherHistoryExcel(Request $r, ReportsService $svc)
    {
        $from = $r->input('from');
        $to = $r->input('to');
        $ledgerId = (int) $r->input('ledger_id');

        if ($r->guid) {
            //$user = Client::where('guid', $r->guid)->first();
            $guid = $r->guid;
            $partyguid = $guid;
        } else {
            $guid = Auth::user()->guid;
            $partyguid = $guid;
        }

        $resp = $svc->voucherHistory($partyguid, $ledgerId, $from, $to);
        $data = data_get($resp, 'data', []);

        // Get opening balance
        $openingBalanceData = [];
        if ($from) {
            $previousDay = date('Y-m-d', strtotime($from . ' -1 day'));
            $openingBalanceData = $svc->getOpeningBalance($partyguid, $ledgerId, $previousDay);
        } else {
            $openingBalanceData = ['balance' => 0.0, 'side' => 'Dr'];
        }

        // Process data for export (same logic as blade file)
        $processedData = $this->processVoucherHistoryData($data, $openingBalanceData, $from, $to);
        $ledgerName = '';
        if ($ledgerId) {
            $ledger = DB::table('LedgerMaster')->where('iLedgerId', $ledgerId)->first();
            $ledgerName = $ledger->strCustomerName ?? '';
        }
        $filename = 'voucher_history_' . date('Y_m_d') . '.xlsx';

        return Excel::download(new VoucherHistoryExport(
            $processedData,
            $from,
            $to,
            $ledgerId,
            $openingBalanceData['balance'] ?? 0,
            $processedData['closingBalance'] ?? 0,
            $data['raw_total_dr'] ?? 0,
            $data['raw_total_cr'] ?? 0,
            $ledgerName
        ), $filename);
    }

    public function exportVoucherHistoryPDF(Request $r, ReportsService $svc)
    {
        $from = $r->input('from');
        $to = $r->input('to');
        $ledgerId = (int) $r->input('ledger_id');
        if ($r->guid) {
            $guid = $r->guid;
            $partyguid = $guid;
        } else {
            $guid = Auth::user()->guid;
            $partyguid = $guid;
        }

        $resp = $svc->voucherHistory($partyguid, $ledgerId, $from, $to);
        $data = data_get($resp, 'data', []);

        // Get opening balance
        $openingBalanceData = [];
        if ($from) {
            $previousDay = date('Y-m-d', strtotime($from . ' -1 day'));
            $openingBalanceData = $svc->getOpeningBalance($partyguid, $ledgerId, $previousDay);
        } else {
            $openingBalanceData = ['balance' => 0.0, 'side' => 'Dr'];
        }
        $partyName = $r->user()->name;
        // Process data for PDF (same logic as blade file)
        $processedData = $this->processVoucherHistoryData($data, $openingBalanceData, $from, $to);
        $ledgerName = '';
        if ($ledgerId) {
            $ledger = DB::table('LedgerMaster')->where('iLedgerId', $ledgerId)->first();
            $ledgerName = $ledger->strCustomerName ?? '';
        }
        $pdf = PDF::loadView('reports.pdf.voucher_history_pdf', [
            'processedRows' => $processedData['processedRows'] ?? [],
            'from' => $from,
            'to' => $to,
            'ledgerId' => $ledgerId,
            'openingBalance' => $openingBalanceData['balance'] ?? 0,
            'openingSide' => $openingBalanceData['side'] ?? 'Dr',
            'closingBalance' => $processedData['closingBalance'] ?? 0,
            'closingSide' => $processedData['closingSide'] ?? 'Dr',
            'totalDr' => $data['raw_total_dr'] ?? 0,
            'totalCr' => $data['raw_total_cr'] ?? 0,
            'partyName' => $partyName,
            'ledgerName' => $ledgerName
        ]);

        $filename = 'voucher_history_' . date('Y_m_d') . '.pdf';

        return $pdf->download($filename);
    }

    // Helper method to process voucher history data
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
