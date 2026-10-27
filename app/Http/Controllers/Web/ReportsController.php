<?php

namespace App\Http\Controllers\Web;

use App\Support\DateRangeValidation;
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
use App\Exports\VoucherExport;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use App\Support\ReportCache;
use App\Support\Demo\ReportContext;

class ReportsController extends Controller
{

    private function getFinancialYears(int $userId)
    {
        // return DB::table('YearMaster')
        //     ->where('iPartyId', $userId)
        //     ->orderBy('iYearId', 'asc')
        //     ->get();
        return Cache::remember(ReportCache::key('reports', $userId, 'financial_years'), ReportCache::ttl(), function () use ($userId) {
            return DB::table('YearMaster')
                ->where('iPartyId', $userId)
                ->orderBy('iYearId', 'asc')
                ->orderBy('strYear', 'asc')
                ->limit(3)
                ->get();
        });
    }

    private function cachedPandl(ReportsService $svc, int $partyId, ?string $from, ?string $to): array
    {
        return Cache::remember(ReportCache::key('reports', $partyId, 'pandl:' . md5(($from ?? '') . '|' . ($to ?? ''))), ReportCache::ttl(), function () use ($svc, $partyId, $from, $to) {
            return $svc->pandl($partyId, $from, $to);
        });
    }

    private function cachedBalanceSheet(ReportsService $svc, ?string $partyguid, int $partyId, ?string $from, ?string $to): array
    {
        return Cache::remember(ReportCache::key('reports', $partyId, 'balance_sheet:' . md5(($partyguid ?? '') . '|' . ($from ?? '') . '|' . ($to ?? ''))), ReportCache::ttl(), function () use ($svc, $partyguid, $partyId, $from, $to) {
            return $svc->balanceSheet($partyguid, $partyId, $from, $to);
        });
    }

    private function cachedMonthlyGraph(ReportsService $svc, int $partyId, ?string $from, ?string $to, int $type, array $opts): array
    {
        return Cache::remember(ReportCache::key('reports', $partyId, 'monthly_graph:' . md5($type . '|' . ($from ?? '') . '|' . ($to ?? '') . '|' . json_encode($opts))), ReportCache::ttl(), function () use ($svc, $partyId, $from, $to, $type, $opts) {
            return $svc->monthlyGraph($partyId, $from, $to, $type, $opts);
        });
    }

    private function defaultFinancialYearRange($financialYears): string
    {
        $currentYear = $financialYears->firstWhere('isCurrentYear', 1);
        $defaultYear = $currentYear ?: $financialYears->first();

        return (string) ($defaultYear->iYearId ?? 'current_year');
    }

    private function financialYearDateRange(string $rangeSel, $financialYears): ?array
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

    private function legacyFinancialYearDateRange(string $rangeSel): ?array
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

    private function prepareFinancialYearFilter(Request $r, int $userId): array
    {
        DateRangeValidation::validate($r, 'from', 'to');
        DateRangeValidation::validate($r, 'from_custom', 'to_custom');
        
        // Sample data keeps its own year/date selection so switching never
        // changes the client's real report filters.
        $sk = fn (string $key): string => ReportContext::sessionKey($key);
        $financialYears = $this->getFinancialYears($userId);
        $requestedRange = $r->input('range');
        $sessionYear = session($sk('year'));
        $sessionYearId = $sessionYear
            ? optional($financialYears->firstWhere('strYear', $sessionYear))->iYearId
            : null;
        $rangeSel = $requestedRange
            ?: session($sk('selectedReportRange'), $sessionYearId ?: $this->defaultFinancialYearRange($financialYears));

        if ($rangeSel !== 'custom' && $financialYears->isNotEmpty() && !$financialYears->firstWhere('iYearId', (int) $rangeSel)) {
            $rangeSel = $sessionYearId ?: $this->defaultFinancialYearRange($financialYears);
        }

        if ($rangeSel === 'custom') {
            $from = $r->input('from_custom') ?: $r->input('from') ?: ($requestedRange ? null : session($sk('selectedFrom')));
            $to = $r->input('to_custom') ?: $r->input('to') ?: ($requestedRange ? null : session($sk('selectedTo')));
        } else {
            $range = $this->financialYearDateRange((string) $rangeSel, $financialYears)
                ?: $this->legacyFinancialYearDateRange((string) $rangeSel);

            $from = $range['from'] ?? $r->input('from', session($sk('selectedFrom')));
            $to = $range['to'] ?? $r->input('to', session($sk('selectedTo')));
        }

        $selectedYearLabel = null;
        if ($rangeSel !== 'custom') {
            $selectedYearLabel = optional($financialYears->firstWhere('iYearId', (int) $rangeSel))->strYear;
        }

        session([
            // 'selectedRange' => $rangeSel,
            $sk('selectedReportRange') => $rangeSel,
            $sk('selectedFrom') => $from,
            $sk('selectedTo') => $to,
            $sk('year') => $selectedYearLabel ?: session($sk('year')),
            $sk('year_from') => $selectedYearLabel ? $from : session($sk('year_from')),
            $sk('year_to') => $selectedYearLabel ? $to : session($sk('year_to')),
        ]);

        return [$financialYears, $rangeSel, $from, $to];
    }
    /**
     * Profit & Loss page
     * Expects your API endpoint: GET /api/reports/pl?from=YYYY-MM-DD&to=YYYY-MM-DD
     */
    public function pl(Request $r, \App\Services\ReportsService $svc)
    {
        $toDMY = fn($d) => $d ? \Carbon\Carbon::parse($d)->format('d-m-Y') : '';
        [$financialYears, $rangeSel, $from, $to] = $this->prepareFinancialYearFilter($r, ReportContext::party($r->input('guid'))->id);
        // $resp = $svc->pandl($r->user()->id, $toDMY($from), $toDMY($to));
        $resp = $this->cachedPandl($svc, ReportContext::party($r->input('guid'))->id, $toDMY($from), $toDMY($to));
        $pl = data_get($resp, 'data', []);
        
        return view('reports.pl', compact('resp', 'from', 'to', 'pl', 'rangeSel', 'financialYears'));
    }

    public function exportPdf(Request $r, ReportsService $svc)
    {
        $userId = 0;
        // Company comes from the server: own books, an assigned client (staff,
        // via ?guid=) or the read-only sample company. Never from raw input.
        $party = ReportContext::party($r->input('guid'));
        $partyId = $party->id;
        $guid = $partyguid = $party->guid;
        $userId = $party->id;
        $partyName = ReportContext::party($r->input('guid'))->displayName();
        $from = $r->input('from');
        $to = $r->input('to');
        $toDMY = fn($d) => $d ? \Carbon\Carbon::parse($d)->format('d-m-Y') : '';

        // $resp = $svc->pandl($partyId, $toDMY($from), $toDMY($to));
        $resp = $this->cachedPandl($svc, $partyId, $toDMY($from), $toDMY($to));
        $pl = data_get($resp, 'data', []);
        $from = $r->input('from');
        $to = $r->input('to');
        $user = User::with('profile')->find($userId);
        
        $companyAddress = optional(optional($user)->profile)->address;
        $companyEmail = ReportContext::party($r->input('guid'))->email ?? '';
        $pdf = Pdf::loadView('reports.pdf.pl-pdf', compact('pl', 'from', 'to', 'partyName','companyAddress','companyEmail'));

        $filename = \App\Support\Demo\ReportContext::filePrefix() . 'profit-loss-report-' . ($from ?: 'start') . '-to-' . ($to ?: 'end') . '.pdf';

        return $pdf->download($filename);
    }

    public function exportExcel(Request $r, ReportsService $svc)
    {
        // $partyId = $r->user()->id;
        $userId = 0;
        // Company comes from the server: own books, an assigned client (staff,
        // via ?guid=) or the read-only sample company. Never from raw input.
        $party = ReportContext::party($r->input('guid'));
        $partyId = $party->id;
        $guid = $partyguid = $party->guid;
        $userId = $party->id;
        $from = $r->input('from');
        $to = $r->input('to');
        //$partyName = ReportContext::party($r->input('guid'))->displayName();
        $filename = \App\Support\Demo\ReportContext::filePrefix() . 'profit-loss-report-' . ($from ?: 'start') . '-to-' . ($to ?: 'end') . '.xlsx';
        $user = User::with('profile')->find($userId);
        
        $partyName = ReportContext::party($r->input('guid'))->displayName();
        $companyAddress = optional(optional($user)->profile)->address;
        $companyEmail = ReportContext::party($r->input('guid'))->email ?? '';
        //return Excel::download(new PandLExport($svc, $partyId, $from, $to,$partyName), $filename);
        return Excel::download(new PandLExport($svc, $partyId, $from, $to,$partyName,$companyAddress,$companyEmail), $filename);
    }

    public function balanceSheet(Request $r, ReportsService $svc)
    {
        
        $party = ReportContext::party($r->input('guid'));
        // NULL, not '': the stored procedure's GUID parameter is a uniqueidentifier.
        $guid = $partyguid = $party->guid;
        $partyId   = $party->id;
        
        [$financialYears, $rangeSel, $from, $to] = $this->prepareFinancialYearFilter($r, $partyId);
        
        // $resp = $svc->balanceSheet($partyguid, $partyId, $from, $to);
        $resp = $this->cachedBalanceSheet($svc, $partyguid, $partyId, $from, $to);
        $data = data_get($resp, 'data', []);
                
        return view('reports.balance_sheet', compact('resp', 'from', 'to', 'data', 'partyguid', 'rangeSel', 'financialYears'));
    }

    public function exportBalanceSheetExcel(Request $r, ReportsService $svc)
    {
        $from = $r->input('from');
        $to   = $r->input('to');
        $userId = 0;
        // Company comes from the server: own books, an assigned client (staff,
        // via ?guid=) or the read-only sample company. Never from raw input.
        $party = ReportContext::party($r->input('guid'));
        $partyId = $party->id;
        $guid = $partyguid = $party->guid;
        $userId = $party->id;
        
        // $resp = $svc->balanceSheet($partyguid, $partyId, $from, $to);
        $resp = $this->cachedBalanceSheet($svc, $partyguid, $partyId, $from, $to);
        $data = data_get($resp, 'data', []);

        $filename = \App\Support\Demo\ReportContext::filePrefix() . 'balance_sheet_' . date('Y_m_d') . '.xlsx';
        $user = User::with('profile')->find($userId);
        
        $partyName = ReportContext::party($r->input('guid'))->displayName();
        $companyAddress = optional(optional($user)->profile)->address;
        $companyEmail = ReportContext::party($r->input('guid'))->email ?? '';
        return Excel::download(new BalanceSheetExport($data, $from, $to,$partyName,$companyAddress,$companyEmail), $filename);
    }

    public function exportBalanceSheetPDF(Request $r, ReportsService $svc)
    {
        $from = $r->input('from');
        $to   = $r->input('to');
        $userId = 0;
        // Company comes from the server: own books, an assigned client (staff,
        // via ?guid=) or the read-only sample company. Never from raw input.
        $party = ReportContext::party($r->input('guid'));
        $partyId = $party->id;
        $guid = $partyguid = $party->guid;
        $userId = $party->id;
        // $guid = Auth::user()->guid ?? '';
        // $partyguid = $guid;
        // $partyId   = $r->user()->id;
        $user = User::with('profile')->find($userId);
        
        $partyName = ReportContext::party($r->input('guid'))->displayName();
        $companyAddress = optional(optional($user)->profile)->address;
        $companyEmail = ReportContext::party($r->input('guid'))->email ?? '';
        // $resp = $svc->balanceSheet($partyguid, $partyId, $from, $to);
        $resp = $this->cachedBalanceSheet($svc, $partyguid, $partyId, $from, $to);
        $data = data_get($resp, 'data', []);

        $pdf = PDF::loadView('reports.pdf.balance_sheet_pdf', [
            'data' => $data,
            'from' => $from,
            'to' => $to,
            'partyName' => $partyName,
            'companyAddress' =>$companyAddress,
            'companyEmail' => $companyEmail
        ]);

        $filename = \App\Support\Demo\ReportContext::filePrefix() . 'balance_sheet_' . date('Y_m_d') . '.pdf';

        return $pdf->download($filename);
    }

    public function ledger(Request $r, ReportsService $svc)
    {
        $groupId = (int) $r->input('group_id'); // iGroupId
        $strCustomerName = $r->input('strCustomerName');
        $party = ReportContext::party($r->input('guid'));
        $partyId = $party->id;
        $guid = $party->guid;
        $partyguid = $guid; // provide via UI/session
        [$financialYears, $rangeSel, $from, $to] = $this->prepareFinancialYearFilter($r, $partyId);

        $resp = $svc->ledger($partyId, $groupId, $from, $to, $strCustomerName);
        $data = data_get($resp, 'data', []);
        $GroupMasters = DB::table('GroupMaster')
            //->where('PartyGUID', $partyguid)
            ->where('iPartyId', $partyId)
            ->get();
        
        return view('reports.ledger', compact('resp', 'from', 'to', 'strCustomerName', 'data', 'groupId', 'GroupMasters', 'rangeSel', 'financialYears'));
    }

    public function voucherHistory(Request $r, ReportsService $svc)
    {
        $ledgerId  = (int) $r->input('ledger_id');
        $guid = ReportContext::party($r->input('guid'))->guid;
        $partyguid = $guid;
        [$financialYears, $rangeSel, $from, $to] = $this->prepareFinancialYearFilter($r, ReportContext::party($r->input('guid'))->id);
        
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
        return view('reports.voucher_history', compact(
            'resp',
            'from',
            'to',
            'data',
            'ledgerId',
            'partyguid',
            'rangeSel',
            'openingBalanceData',
            'ledgerName',
            'financialYears'
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

        // $res = $svc->monthlyGraph($user->id, $from, $to, $type, $opts);
        $res = $this->cachedMonthlyGraph($svc, ReportContext::party($r->input('guid'))->id, $from, $to, $type, $opts);

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
        $userId = 0;
        // Company comes from the server: own books, an assigned client (staff,
        // via ?guid=) or the read-only sample company. Never from raw input.
        $party = ReportContext::party($r->input('guid'));
        $partyId = $party->id;
        $guid = $partyguid = $party->guid;
        $userId = $party->id;
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

        $filename = \App\Support\Demo\ReportContext::filePrefix() . 'ledger_report_' . date('Y_m_d') . '.xlsx';
        $user = User::with('profile')->find($userId);
        
        $partyName = ReportContext::party($r->input('guid'))->displayName();
        $companyAddress = optional(optional($user)->profile)->address;
        $companyEmail = ReportContext::party($r->input('guid'))->email ?? '';
        return Excel::download(new LedgerExport($data, $from, $to, $groupName, $strCustomerName,$partyName,$companyAddress,$companyEmail), $filename);
    }

    public function exportLedgerSummaryExcel(Request $r, ReportsService $svc)
    {
        $from = $r->input('from');
        $to = $r->input('to');
        $groupId = (int) $r->input('group_id');
        $strCustomerName = $r->input('strCustomerName');
        $party = ReportContext::party($r->input('guid'));
        $partyId = $party->id;
        $guid = $party->guid;
        $partyguid = $guid;

        $resp = $svc->ledger($partyId, $groupId, $from, $to, $strCustomerName);
        $data = data_get($resp, 'data', []);

        $filename = \App\Support\Demo\ReportContext::filePrefix() . 'ledger_summary_' . date('Y_m_d') . '.xlsx';

        return Excel::download(new LedgerSummaryExport($data, $from, $to), $filename);
    }

    public function exportLedgerPDF(Request $r, ReportsService $svc)
    {

        $from = $r->input('from');
        $to = $r->input('to');
        $groupId = (int) $r->input('group_id');
        $strCustomerName = $r->input('strCustomerName');
        $userId = 0;
        // Company comes from the server: own books, an assigned client (staff,
        // via ?guid=) or the read-only sample company. Never from raw input.
        $party = ReportContext::party($r->input('guid'));
        $partyId = $party->id;
        $guid = $partyguid = $party->guid;
        $userId = $party->id;
        
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
        
        $user = User::with('profile')->find($userId);
        
        $partyName = ReportContext::party($r->input('guid'))->displayName();
        $companyAddress = optional(optional($user)->profile)->address;
        $companyEmail = ReportContext::party($r->input('guid'))->email ?? '';
        $pdf = PDF::loadView('reports.pdf.ledger_pdf', [
            'data' => $data,
            'from' => $from,
            'to' => $to,
            'groupName' => $groupName,
            'customerName' => $strCustomerName,
            'partyName' => $partyName,
            'companyAddress' => $companyAddress,
            'companyEmail' => $companyEmail
        ]);

        $filename = \App\Support\Demo\ReportContext::filePrefix() . 'ledger_report_' . date('Y_m_d') . '.pdf';

        return $pdf->download($filename);
    }

    public function exportVoucherHistoryExcel(Request $r, ReportsService $svc)
    {
        $from = $r->input('from');
        $to = $r->input('to');
        $ledgerId = (int) $r->input('ledger_id');
        $userId = 0;
        // Company comes from the server: own books, an assigned client (staff,
        // via ?guid=) or the read-only sample company. Never from raw input.
        $party = ReportContext::party($r->input('guid'));
        $partyId = $party->id;
        $guid = $partyguid = $party->guid;
        $userId = $party->id;

        $resp = $svc->voucherHistory($partyguid, $ledgerId, $from, $to);
        $data = data_get($resp, 'data', []);

        // Get opening balance
        $openingBalanceData = [];
        if ($from) {
            $previousDay = date('Y-m-d', strtotime($from));
            $openingBalanceData = $svc->getOpeningBalance($partyguid, $ledgerId, $previousDay);
        } else {
            $openingBalanceData = ['balance' => 0.0, 'side' => 'Dr'];
        }

        // Process data for export (same logic as blade file)
        $processedData = $this->processVoucherHistoryData($data, $openingBalanceData, $from, $to);
        // $processedData = $data;
        $ledgerName = '';
        if ($ledgerId) {
            $ledger = DB::table('LedgerMaster')->where('iLedgerId', $ledgerId)->first();
            $ledgerName = $ledger->strCustomerName ?? '';
        }
        $filename = \App\Support\Demo\ReportContext::filePrefix() . 'Ledger_history_' . date('Y_m_d') . '.xlsx';
        $toFloat = function ($v) {
            if ($v === null || $v === '') {
                return 0.0;
            }
            return (float) str_replace(',', '', (string) $v);
        };
        $user = User::with('profile')->find($userId);
        
        $partyName = ReportContext::party($r->input('guid'))->displayName();
        $companyAddress = optional(optional($user)->profile)->address;
        $companyEmail = ReportContext::party($r->input('guid'))->email ?? '';
        return Excel::download(new VoucherHistoryExport(
            $processedData,
            $from,
            $to,
            $ledgerId,
            $openingBalanceData['balance'] ?? 0,
            $processedData['closingBalance'] ?? 0,
            // $data[0]['TotalDr'] ?? 0,
            // $data[0]['TotalCr'] ?? 0,
            $toFloat(collect($data['rows'] ?? [])->first()->TotalDr ?? 0), 
            $toFloat(collect($data['rows'] ?? [])->first()->TotalCr ?? 0),
            $ledgerName,
            $partyName,
            $companyAddress,
            $companyEmail
        ), $filename);
    }

    public function exportVoucherHistoryPDF(Request $r, ReportsService $svc)
    {
        $from = $r->input('from');
        $to = $r->input('to');
        $ledgerId = (int) $r->input('ledger_id');
        $userId = 0;
        // Company comes from the server: own books, an assigned client (staff,
        // via ?guid=) or the read-only sample company. Never from raw input.
        $party = ReportContext::party($r->input('guid'));
        $partyId = $party->id;
        $guid = $partyguid = $party->guid;
        $userId = $party->id;

        $resp = $svc->voucherHistory($partyguid, $ledgerId, $from, $to);
        $data = data_get($resp, 'data', []);
        
        // Get opening balance
        $openingBalanceData = [];
        // $from ? date('d-m-Y', strtotime($from)) : '',
        if ($from) {
            $previousDay = date('d-m-Y', strtotime($from)); // date('Y-m-d', strtotime($from . ' -1 day'));
            $openingBalanceData = $svc->getOpeningBalance($partyguid, $ledgerId, $previousDay);
        } else {
            $openingBalanceData = ['balance' => 0.0, 'side' => 'Dr'];
        }
        $partyName = ReportContext::party($r->input('guid'))->displayName();
        // Process data for PDF (same logic as blade file)
        $processedData = $this->processVoucherHistoryData($data, $openingBalanceData, $from, $to);
        
        $ledgerName = '';
        if ($ledgerId) {
            $ledger = DB::table('LedgerMaster')->where('iLedgerId', $ledgerId)->first();
            $ledgerName = $ledger->strCustomerName ?? '';
        }
        $toFloat = function ($v) {
            if ($v === null || $v === '') {
                return 0.0;
            }
            return (float) str_replace(',', '', (string) $v);
        };
        $user = User::with('profile')->find($userId);
        
        //$partyName = ReportContext::party($r->input('guid'))->displayName();
        $companyAddress = optional(optional($user)->profile)->address;
        $companyEmail = ReportContext::party($r->input('guid'))->email ?? '';
        $pdf = PDF::loadView('reports.pdf.voucher_history_pdf', [
            'processedRows' => $processedData['processedRows'] ?? [],
            'from' => $from,
            'to' => $to,
            'ledgerId' => $ledgerId,
            'openingBalance' => $openingBalanceData['balance'] ?? 0,
            'openingSide' => $openingBalanceData['side'] ?? 'Dr',
            'closingBalance' => $processedData['closingBalance'] ?? 0,
            'closingSide' => $processedData['closingSide'] ?? 'Dr',
            // 'totalDr' => $data['raw_total_dr'] ?? 0,
            // 'totalCr' => $data['raw_total_cr'] ?? 0,
            'totalDr' => $toFloat(collect($data['rows'] ?? [])->first()->TotalDr ?? 0), 
            'totalCr' => $toFloat(collect($data['rows'] ?? [])->first()->TotalCr ?? 0),
            'partyName' => $partyName,
            'ledgerName' => $ledgerName,
            'companyAddress' => $companyAddress,
            'companyEmail' => $companyEmail
        ]);

        $filename = \App\Support\Demo\ReportContext::filePrefix() . 'Ledger_history_' . date('Y_m_d') . '.pdf';

        return $pdf->download($filename);
    }

    private function processVoucherHistoryData($data, $openingBalanceData, $from, $to)
    {
        $rows = collect($data['rows'] ?? []);
        $toFloat = function ($v) {
            if ($v === null || $v === '') {
                return 0.0;
            }
            return (float) str_replace(',', '', (string) $v);
        };

        $openingBalance = $toFloat($rows->first()->OpeningBalance ?? 0);
        $openingSide = $openingBalance >= 0 ? 'Dr' : 'Cr';
        $openingBalance = abs($openingBalance);
        $processedRows = [];

        $previousBalance = $openingSide === 'Dr'
            ? $openingBalance
            : -$openingBalance;

        // Opening Row
        $processedRows[] = (object)[
            'is_opening'       => true,
            'strVchDate'       => $from
                ? date('d-m-Y', strtotime($from))
                : '',
            'vchNo'            => 'OPENING BALANCE',
            'vchType'          => 'Opening',
            'trnAccount'       => 'Balance B/F',
            'DRAmount'         => 0,
            'CRAmount'         => 0,
            'opening_balance'  => $previousBalance,
            'decRunningBalance'=> $previousBalance,
            'side'             => $openingSide,
        ];

        foreach ($rows as $r) {

            $drRaw = $toFloat($r->DRAmount ?? 0);

            $crRaw = $toFloat($r->CRAmount ?? 0);

            $currentOpening = $previousBalance;

            // ✅ SAME AS BLADE FILE
            $currentClosing =
                $previousBalance
                - abs($drRaw)
                + abs($crRaw);

            $side = $currentClosing >= 0 ? 'Dr' : 'Cr';

            $processedRows[] = (object) array_merge((array) $r, [
                'is_opening'        => false,
                'is_closing'        => false,
                'opening_balance'   => $currentOpening,
                'decRunningBalance' => $currentClosing,
                'side'              => $side,
            ]);

            $previousBalance = $currentClosing;
        }

        $lastRunningBalance = $previousBalance;

        $closingBalance = abs($lastRunningBalance);

        $closingSide = $lastRunningBalance >= 0 ? 'Dr' : 'Cr';

        // Closing Row
        $processedRows[] = (object)[
            'is_closing'       => true,
            'strVchDate'       => $to
                ? date('d-m-Y', strtotime($to))
                : now()->format('d-m-Y'),
            'vchNo'            => 'CLOSING BALANCE',
            'vchType'          => 'Closing',
            'trnAccount'       => 'Balance C/F',
            'DRAmount'         => 0,
            'CRAmount'         => 0,
            'opening_balance'  => $lastRunningBalance,
            'decRunningBalance'=> $lastRunningBalance,
            'side'             => $closingSide,
        ];

        return [
            'processedRows' => $processedRows,
            'closingBalance'=> $closingBalance,
            'closingSide'   => $closingSide,
        ];
    }


    public function viewVoucher($strGUID, $vchType)
    {
        $guid = ReportContext::party()->guid;
        $svc = new ReportsService();
        $resp = $svc->voucherDetails($guid, $strGUID, $vchType);
        
        // $voucher = DB::select(
        //     "EXEC GetVoucherDetails ?, ?, ?",
        //     [$guid, $vchNo, $vchType]
        // );
        //$voucher = collect(data_get($resp, 'data.rows', []));
        $voucher = collect($resp);
        $display = $svc->prepareVoucherDisplay($voucher);
        $accountLedger = $display['accountLedger'];
        $particulars = $display['particulars'];
        $displayTotal = $display['total'];
        $displaySide = $display['displaySide'];
        
        if ($voucher->isEmpty()) {
            abort(404);
        }
        // ✅ HEADER
        $header = $voucher->first();
        $display = $svc->prepareVoucherDisplay($voucher);
        $accountLedger = $display['accountLedger'];
        $particulars = $display['particulars'];
        $displayTotal = $display['total'];
        $displaySide = $display['displaySide'];
        
        // ✅ TOTALS
        $totalDr = $voucher->sum(function ($r) {
            return (float) ($r->DRAmount ?? 0);
        });

        $totalCr = $voucher->sum(function ($r) {
            return (float) ($r->CRAmount ?? 0);
        });

        return view('reports.voucher_view', compact(
            'voucher',
            'header',
            'totalDr',
            'totalCr',
            'totalCr',
            'accountLedger',
            'particulars',
            'displayTotal',
            'displaySide'
        ));
    }

    public function exportVoucherPDF($vchNo, $vchType,$guid = null)
    {
        // Route {guid} is checked against the caller's own / assigned companies.
        $guid = ReportContext::party($guid)->guid;
        $svc = new ReportsService();
        $resp = $svc->voucherDetails($guid, $vchNo, $vchType);
        //$voucher = $resp['data']['rows'] ?? collect();
        $voucher = collect($resp);

        if ($voucher->isEmpty()) {
            abort(404);
        }

        $header = $voucher->first();

        $totalDr = $voucher->sum(fn($x) => abs((float)$x->DRAmount));
        $totalCr = $voucher->sum(fn($x) => abs((float)$x->CRAmount));
        $display = $svc->prepareVoucherDisplay($voucher);
        $accountLedger = $display['accountLedger'];
        $particulars = $display['particulars'];
        $displayTotal = $display['total'];
        $displaySide = $display['displaySide'];

        $pdf = PDF::loadView(
            'reports.pdf.voucher_pdf',
            compact(
                'voucher',
                'header',
                'totalDr',
                'totalCr',
                'accountLedger',
                'particulars',
                'displayTotal',
                'displaySide'
            )
        );
        $safeVchNo = str_replace(['/', '\\'], '-', $vchNo);

        return $pdf->download(
            'voucher_' . $safeVchNo  . '.pdf'
        );
    }

    public function exportVoucherExcel($vchNo, $vchType,$guid = null)
    {
        // Route {guid} is checked against the caller's own / assigned companies.
        $guid = ReportContext::party($guid)->guid;
        $svc = new ReportsService();
        $resp = $svc->voucherDetails($guid, $vchNo, $vchType);
        //$voucher = $resp['data']['rows'] ?? collect();
        $voucher = collect($resp);
        $header = $voucher->first();
        $totalDr = $voucher->sum(fn($x) => abs((float)$x->DRAmount));
        $totalCr = $voucher->sum(fn($x) => abs((float)$x->CRAmount));
        // $total = abs($totalDr ?: $totalCr);
        $display = $svc->prepareVoucherDisplay($voucher);
        $accountLedger = $display['accountLedger'];
        $particulars = $display['particulars'];
        $displayTotal = $display['total'];
        $displaySide = $display['displaySide'];
        $safeVchNo = str_replace(['/', '\\'], '-', $vchNo);

        return Excel::download(
            new VoucherExport(
                $voucher,
                $header,
                $displayTotal,
                $accountLedger,
                $particulars,
                $displaySide
            ),
            'voucher_'.$safeVchNo.'.xlsx'
        );
    }


    // public function printBalanceSheet(Request $request,ReportsService $svc)
    // {
    //     // Get filters (same as your main report)
    //     $from  = $request->from;
    //     $to    = $request->to;
    //     $range = $request->range;
    //     $guid = Auth::user()->guid ?? '';
    //     $partyguid = $guid;
    //     $partyId   = Auth::user()->id;

    //     // $resp = $svc->balanceSheet($partyguid, $partyId, $from, $to);
    //     // 👉 IMPORTANT: Call SAME logic as your balance sheet report
    //     // (Reuse your existing function / service / query)

    //     $data = $svc->balanceSheet($partyguid, $partyId, $from, $to);

    //     return view('reports.balance_sheet_print', [
    //         'data' => $data,
    //         'from' => $from,
    //         'to'   => $to
    //     ]);
    // }
}
