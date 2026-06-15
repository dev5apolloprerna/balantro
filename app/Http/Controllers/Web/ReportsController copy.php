<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\ReportsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        return view('reports.pl', compact('resp', 'from', 'to', 'pl', 'rangeSel'));
    }

    /**
     * Balance Sheet page
     * API: GET /api/reports/balance?as_on=YYYY-MM-DD
     */
    public function balanceSheet(Request $r, ReportsService $svc)
    {
        // inputs
        $rangeSel = $r->input('range');
        $from = $r->input('from'); // Y-m-d (preferred) or d-m-Y
        $to   = $r->input('to');
        $guid = Auth::user()->guid ?? '';
        $partyguid = $guid; // provide via UI/session
        $partyId   = $r->user()->id;

        $resp = $svc->balanceSheet($partyguid, $partyId, $from, $to);

        $data = data_get($resp, 'data', []);

        return view('reports.balance_sheet', compact('resp', 'from', 'to', 'data', 'partyguid', 'rangeSel'));
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

        return view('reports.ledger', compact('resp', 'from', 'to','strCustomerName', 'data', 'groupId', 'GroupMasters', 'rangeSel'));
    }

    public function voucherHistory(Request $r, ReportsService $svc)
    {
        $rangeSel = $r->input('range');
        $from = $r->input('from');
        $to   = $r->input('to');
        $ledgerId  = (int) $r->input('ledger_id'); // iledgerid
        //$partyguid = $r->input('partyguid');
        $guid = Auth::user()->guid;
        $partyguid = $guid; // provide via UI/session
        $resp = $svc->voucherHistory($partyguid, $ledgerId, $from, $to);
        $data = data_get($resp, 'data', []);

        return view('reports.voucher_history', compact('resp', 'from', 'to', 'data', 'ledgerId', 'partyguid', 'rangeSel'));
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
}
