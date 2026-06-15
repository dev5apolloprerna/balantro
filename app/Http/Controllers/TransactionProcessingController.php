<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\BulkSalesUpload;
use App\Models\SalesTransaction;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Ledger;
use Illuminate\Support\Facades\DB;
use App\Models\Client;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Models\BulkPurchaseUpload;
use App\Models\PurchaseTransaction;
use App\Models\BulkBankUpload;
use App\Models\BankTransaction;
use App\Models\BulkCreditNoteUpload;
use App\Models\BulkDebitNoteUpload;
use App\Models\CreditNoteTransaction;
use App\Models\DebitNoteTransaction;
use App\Models\BulkJournalUpload;
use App\Models\JournalTransaction;
use App\Models\JournalTransactionItem;
use Illuminate\Support\Carbon;

class TransactionProcessingController extends Controller
{
    private function ensureFinancialYearInSession($years)
    {
        if (!$years || !count($years)) {
            return;
        }

        $currentYear = session('year');
        $isValidYear = $currentYear && collect($years)->contains('strYear', $currentYear);

        if ($isValidYear && session('year_from') && session('year_to')) {
            return;
        }

        if (!$isValidYear) {
            $today = Carbon::today();
            $yearStart = $today->month >= 4 ? $today->year : $today->year - 1;
            $yearEnd = $yearStart + 1;
            $currentYear = sprintf('%d-%04d', $yearStart, $yearEnd);
            $availableYear = collect($years)->firstWhere('strYear', $currentYear);
            if ($availableYear) {
                $currentYear = $availableYear->strYear;
            } else {
                $currentYear = $years[0]->strYear;
            }
        }

        session(['year' => $currentYear]);

        [$startYear, $endYear] = explode('-', $currentYear) + [1 => null];
        if ($endYear === null) {
            return;
        }

        session([
            'year_from' => $startYear . '-04-01',
            'year_to' => $endYear . '-03-31',
        ]);
    }

    private function getLedgerGstMappings($iPartyId, string $parent): array
    {
        return DB::table('LedgerMaster')
            ->select(
                'iLedgerId as id',
                'strCustomerName as name',
                'CGSTLedgerId as cgst_id',
                'SGSTLedgerId as sgst_id',
                'IGSTLedgerId as igst_id'
            )
            ->where('iPartyId', $iPartyId)
            ->where('strParents', $parent)
            ->where(function ($query) {
                $query->whereNotNull('CGSTLedgerId')
                    ->orWhereNotNull('SGSTLedgerId')
                    ->orWhereNotNull('IGSTLedgerId');
            })
            ->get()
            ->map(function ($ledger) {
                return [
                    'id' => (string) $ledger->id,
                    'name' => $ledger->name,
                    'cgst_id' => $ledger->cgst_id ? (string) $ledger->cgst_id : null,
                    'sgst_id' => $ledger->sgst_id ? (string) $ledger->sgst_id : null,
                    'igst_id' => $ledger->igst_id ? (string) $ledger->igst_id : null,
                ];
            })
            ->values()
            ->all();
    }

    public function processing_sales()
    {
        $iPartyId = session('iPartyId');
        $uploads = BulkSalesUpload::where('iPartyId', $iPartyId)->where('saved', '>', 0)->latest()->get();
        $clients = Client::orderBy('name')->get();
        $stockItems = DB::table('StockItemMaster')
                ->where('iPartyId', $iPartyId)
                ->orderBy('strItemName', 'asc')
                ->get();
        $years = DB::table('YearMaster')
            ->where('iPartyId', $iPartyId)
            ->orderBy('strYear', 'asc')
            ->get();
        $this->ensureFinancialYearInSession($years);
        return view('admin.transaction-processing.sales.index', compact('uploads', 'clients', 'stockItems', 'years'));
    }

    public function preview_processing_sales($id)
    {
        $iPartyId = session('iPartyId');
        if (!$iPartyId) {
            return redirect()->route('transaction_processing.processing_sales')
                ->with('error', 'Please select company first');
        }
        $rows = SalesTransaction::where('upload_id', $id)
            ->where('status', 'saved')
            ->where('iPartyId', $iPartyId)
            ->get();
        
        $vchTypes = DB::table('VchHistory')
            ->where('iPartyId', $iPartyId)
            ->where('vchType', 'Sales')
            ->distinct()
            ->pluck('vchType');

        $states = DB::table('state')
            ->pluck('stateName');

        $groups = DB::table('GroupMaster')
            ->where('iPartyId', $iPartyId)
            ->distinct()
            ->pluck('strGroupName');
        $parents = DB::table('LedgerMaster')
            ->select('strParents')
            ->where('iPartyId', $iPartyId)
            ->where('strParents','Sundry Debtors')
            ->distinct()
            ->get();
        $ledgers = Ledger::getAllDebtorsLedgers($iPartyId);

        $iGstLedgers = Ledger::getAlliGstLedgers($iPartyId);
        $cGstLedgers = Ledger::getAllcGstLedgers($iPartyId);
        $sGstLedgers = Ledger::getAllsGstLedgers($iPartyId);
        $iGstLedgers = Ledger::mergeLedgersByIds($iPartyId, $iGstLedgers, $rows->pluck('igst_id')->all());
        $cGstLedgers = Ledger::mergeLedgersByIds($iPartyId, $cGstLedgers, $rows->pluck('cgst_id')->all());
        $sGstLedgers = Ledger::mergeLedgersByIds($iPartyId, $sGstLedgers, $rows->pluck('sgst_id')->all());
        $salesLedgers = Ledger::getSalesLedgers($iPartyId);
        $salesGstMappings = $this->getLedgerGstMappings($iPartyId, 'Sales Accounts');
        $stockItems = DB::table('StockItemMaster')
                ->where('iPartyId', $iPartyId)
                ->orderBy('strItemName', 'asc')
                ->get();
        return view('admin.transaction-processing.sales.preview', compact('rows', 'ledgers', 'vchTypes', 'groups', 'states', 'parents','iGstLedgers',
            'cGstLedgers',
            'sGstLedgers','salesLedgers', 'stockItems', 'salesGstMappings'));
    }

    public function processing_purchase()
    {
        $iPartyId = session('iPartyId');
        $uploads = BulkPurchaseUpload::where('iPartyId', $iPartyId)
            ->where('saved','>',0)
            ->latest()
            ->get();
        
        $clients = Client::orderBy('name')->get();
        $years = DB::table('YearMaster')
            ->where('iPartyId', $iPartyId)
            ->orderBy('strYear', 'asc')
            ->get();
        $this->ensureFinancialYearInSession($years);
        return view('admin.transaction-processing.purchase.index', compact('uploads', 'clients', 'years'));
    }

    private function getPurchaseLedgerGstMappings($partyId): array
    {
        return DB::table('LedgerMaster')
            ->select(
                'iLedgerId as id',
                'strCustomerName as name',
                'CGSTLedgerId as cgst_id',
                'SGSTLedgerId as sgst_id',
                'IGSTLedgerId as igst_id'
            )
            ->where('iPartyId', $partyId)
            ->where('strParents', 'Purchase Accounts')
            ->where(function ($query) {
                $query->whereNotNull('CGSTLedgerId')
                    ->orWhereNotNull('SGSTLedgerId')
                    ->orWhereNotNull('IGSTLedgerId');
            })
            ->get()
            ->map(function ($ledger) {
                return [
                    'id' => (string) $ledger->id,
                    'name' => $ledger->name,
                    'cgst_id' => $ledger->cgst_id ? (string) $ledger->cgst_id : null,
                    'sgst_id' => $ledger->sgst_id ? (string) $ledger->sgst_id : null,
                    'igst_id' => $ledger->igst_id ? (string) $ledger->igst_id : null,
                ];
            })
            ->values()
            ->all();
    }

    public function preview_processing_purchase($id)
    {
        $iPartyId = session('iPartyId');
        if (!$iPartyId) {
            return redirect()->route('transaction_processing.processing_purchase')
                ->with('error', 'Please select company first');
        }

        $rows = PurchaseTransaction::where('upload_id', $id)
            ->where('status', 'saved')
            ->where('iPartyId', $iPartyId)
            ->get();
        $vchTypes = DB::table('VchHistory')
            ->where('iPartyId', $iPartyId)
            ->where('vchType', 'Purchase')
            ->distinct()
            ->pluck('vchType');
        $states = DB::table('state')
            ->pluck('stateName');
        $groups = DB::table('GroupMaster')
            ->where('iPartyId', $iPartyId)
            ->distinct()
            ->pluck('strGroupName');
        $parents = DB::table('LedgerMaster')
            ->select('strParents')
            ->where('iPartyId', $iPartyId)
            ->where('strParents','Sundry Creditors')
            ->distinct()
            ->get();
        $ledgers = Ledger::getAllCreditorsLedgers($iPartyId);

        $iGstLedgers = Ledger::getAlliGstLedgers($iPartyId);
        $cGstLedgers = Ledger::getAllcGstLedgers($iPartyId);
        $sGstLedgers = Ledger::getAllsGstLedgers($iPartyId);
        $purchaseLedgers = Ledger::getPurchaseLedgers($iPartyId);
        $stockItems = DB::table('StockItemMaster')
            ->where('iPartyId', $iPartyId)
            ->orderBy('strItemName', 'asc') // optional (recommended)
            ->get();
        $purchaseGstMappings = $this->getPurchaseLedgerGstMappings($iPartyId);
        return view('admin.transaction-processing.purchase.preview', compact(
            'rows',
            'ledgers',
            'vchTypes',
            'groups',
            'states',
            'parents',
            'iGstLedgers',
            'cGstLedgers',
            'sGstLedgers',
            'purchaseLedgers',
            'stockItems',
            'purchaseGstMappings'
        ));
    }

    public function processing_bank()
    {
        $iPartyId = session('iPartyId');
        $uploads = BulkBankUpload::where('iPartyId', $iPartyId)
            ->where('saved', '>', 0)
            ->latest()
            ->get();
        $banks = DB::table('LedgerMaster')
            ->select('iLedgerId as id', 'strCustomerName as name')
            ->where('iPartyId', $iPartyId)
            ->where('strParents', 'Bank Accounts')
            ->get();

        $clients = Client::orderBy('name')->get();
        $years = DB::table('YearMaster')
            ->where('iPartyId', $iPartyId)
            ->orderBy('strYear', 'asc')
            ->get();
        $this->ensureFinancialYearInSession($years);
        return view('admin.transaction-processing.bank.index', compact('uploads', 'clients', 'banks', 'years'));
    }

    public function preview_processing_bank($id)
    {
        $iPartyId = session('iPartyId');
        if (!$iPartyId) {
            return redirect()->route('transaction_processing.processing_bank')
                ->with('error', 'Please select company first');
        }

        $rows = BankTransaction::where('upload_id', $id)
            ->where('status', 'saved')
            ->where('iPartyId', $iPartyId)
            ->get();
        
        $vchTypes = DB::table('VchHistory')
            ->where('iPartyId', $iPartyId)
            ->whereIn('vchType', ['Contra', 'Payment', 'Receipt'])
            ->distinct()
            ->pluck('vchType');
        $states = DB::table('state')
            ->pluck('stateName');
        $groups = DB::table('GroupMaster')
            ->where('iPartyId', $iPartyId)
            ->distinct()
            ->pluck('strGroupName');
        $parents = DB::table('LedgerMaster')
            ->select('strParents')
            ->where('iPartyId', $iPartyId)
            ->where('strParents','Bank Accounts')
            ->distinct()
            ->get();
        $ledgers = Ledger::getAllBankLedgers($iPartyId);
        $allLedgers   = Ledger::getAllLedgers($iPartyId);
        $bankLedgers  = Ledger::getAllBankCashLedgers($iPartyId);
        
        return view('admin.transaction-processing.bank.preview', compact(
            'rows',
            'ledgers',
            'vchTypes',
            'groups',
            'states',
            'parents',
            'bankLedgers',
            'allLedgers'
        ));
    }

    // SAVE PURCHASE
    public function purchase_sumbit(Request $request)
    {
        $iPartyId = session('iPartyId');
        if (!$iPartyId) {
            return redirect()->route('transaction_processing.processing_purchase')
                ->with('error', 'Please select company first');
        }

        $uploadId = null;
        foreach ($request->selected as $key => $id) {
            $row = PurchaseTransaction::find($id);
            if (!$row) continue;
            $uploadId = $row->upload_id;
            $row->update([
                'invoice_no' => $request->invoice_no[$id],
                'date' => $request->date[$id],
                //'party_name' => $request->party_name[$id] ?? $request->ledger[$id],
                'party_name' => $request->party_name[$id]  ?: ($request->party_ledger[$id] ?? $request->ledger[$id]),
                'place_of_supply' => $request->place_of_supply[$id],
                'purchase_ledger' => $request->ledger[$id],
                'status' => 'submitted',
                'vchType' => $request->voucher_type[$id]
            ]);
        }

        if ($uploadId) {
            $saved = PurchaseTransaction::where('upload_id', $uploadId)
                ->where('status', 'saved')
                ->count();
            $pending = PurchaseTransaction::where('upload_id', $uploadId)
                ->where('status', 'pending')
                ->count();
            $total = PurchaseTransaction::where('upload_id', $uploadId)
                ->count();
            BulkPurchaseUpload::where('id', $uploadId)->update([
                'total' => $total,
                'saved' => $saved,
                'pending' => $pending
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Sumbit Successfully'
        ]);
    }

    public function sales_sumbit(Request $request)
    {
        $iPartyId = session('iPartyId');
        if (!$iPartyId) {
            return redirect()->route('transaction_processing.processing_sales')
                ->with('error', 'Please select company first');
        }
        $uploadId = null;
        foreach ($request->selected as $key => $id) {
            $row = SalesTransaction::find($id);
            if (!$row) continue;

            $uploadId = $row->upload_id;
            $row->update([
                'invoice_no' => $request->invoice_no[$id],
                'date' => $request->date[$id],
                //'party_name' => $request->party_name[$id] ?? $request->ledger[$id],
                'party_name' => $request->party_name[$id] ?: ($request->ledger[$id] ?? null),
                'place_of_supply' => $request->place_of_supply[$id],
                'sales_ledger' => $request->ledger[$id],
                'status' => 'submitted',
                'vchType' => $request->voucher_type[$id]
            ]);
        }
        if ($uploadId) {
            $saved = SalesTransaction::where('upload_id', $uploadId)
                ->where('status', 'saved')
                ->count();
            $pending = SalesTransaction::where('upload_id', $uploadId)
                ->where('status', 'pending')
                ->count();
            $total = SalesTransaction::where('upload_id', $uploadId)
                ->count();
            BulkSalesUpload::where('id', $uploadId)->update([
                'total' => $total,
                'saved' => $saved,
                'pending' => $pending
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Saved Successfully'
        ]);
        // return back()->with('success', 'Records Updated');
    }

    public function bank_sumbit(Request $request)
    {
        if (!$request->has('selected')) {
            return response()->json([
                'status' => false,
                'message' => 'No rows selected'
            ]);
        }

        $uploadId = null;

        foreach ($request->selected as $id) {

            $row = BankTransaction::find($id);
            if (!$row) continue;

            // capture upload id
            $uploadId = $row->upload_id;

            $row->txn_date    = $request->txn_date[$id] ?? $row->txn_date;
            $row->value_date  = $request->value_date[$id] ?? $row->value_date;
            $row->narration   = $request->narration[$id] ?? $row->narration;
            $row->ledger_name = $request->ledger[$id] ?? $row->ledger_name;
            $row->status      = 'submitted';
            $row->save();
        }

        /*
        |-----------------------------------------
        | Update Bulk Upload Summary
        |-----------------------------------------
        */

        if ($uploadId) {

            $saved = BankTransaction::where('upload_id', $uploadId)
                ->where('status', 'saved')
                ->count();

            $pending = BankTransaction::where('upload_id', $uploadId)
                ->where('status', 'pending')
                ->count();

            $total = BankTransaction::where('upload_id', $uploadId)
                ->count();

            BulkBankUpload::where('id', $uploadId)->update([
                'total'   => $total,
                'saved'   => $saved,
                'pending' => $pending
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Saved Successfully'
        ]);
    }

    public function processing_credit_note()
    {
        $iPartyId = session('iPartyId');
        $uploads = BulkCreditNoteUpload::where('iPartyId', $iPartyId)->where('saved', '>', 0)->latest()->get();
        $clients = Client::orderBy('name')->get();
        $years = DB::table('YearMaster')
            ->where('iPartyId', $iPartyId)
            ->orderBy('strYear', 'asc')
            ->get();
        $this->ensureFinancialYearInSession($years);
        return view('admin.transaction-processing.credit_note.index', compact('uploads', 'clients', 'years'));
    }

    public function preview_processing_credit_note($id)
    {
        $iPartyId = session('iPartyId');
        if (!$iPartyId) {
            return redirect()->route('transaction_processing.processing_sales')
                ->with('error', 'Please select company first');
        }
        $rows = CreditNoteTransaction::where('upload_id', $id)
            ->where('status', 'saved')
            ->where('iPartyId', $iPartyId)
            ->get();
        
        $vchTypes = DB::table('VchHistory')
            ->where('iPartyId', $iPartyId)
            ->where('vchType','Credit Note')
            ->distinct()
            ->pluck('vchType');

        $states = DB::table('state')
            ->pluck('stateName');

        $groups = DB::table('GroupMaster')
            ->where('iPartyId', $iPartyId)
            ->distinct()
            ->pluck('strGroupName');
        $parents = DB::table('LedgerMaster')
            ->select('strParents')
            ->where('iPartyId', $iPartyId)
            ->where('strParents','Sundry Debtors')
            ->distinct()
            ->get();
        $ledgers = Ledger::getAllDebtorsLedgers($iPartyId);

        $iGstLedgers = Ledger::getAlliGstLedgers($iPartyId);
        $cGstLedgers = Ledger::getAllcGstLedgers($iPartyId);
        $sGstLedgers = Ledger::getAllsGstLedgers($iPartyId);
        $stockItems = DB::table('StockItemMaster')
            ->where('iPartyId', $iPartyId)
            ->orderBy('strItemName', 'asc') // optional (recommended)
            ->get();
        $salesLedgers = Ledger::getSalesLedgers($iPartyId);
        $salesGstMappings = $this->getLedgerGstMappings($iPartyId, 'Sales Accounts');
        return view('admin.transaction-processing.credit_note.preview', compact('rows', 'ledgers', 'vchTypes', 'groups', 'states', 'parents','iGstLedgers',
            'cGstLedgers',
            'sGstLedgers', 'stockItems','salesLedgers', 'salesGstMappings'));
    }

    public function credit_note_sumbit(Request $request)
    {
        $iPartyId = session('iPartyId');
        if (!$iPartyId) {
            return redirect()->route('transaction_processing.processing_sales')
                ->with('error', 'Please select company first');
        }
        $uploadId = null;
        foreach ($request->selected as $key => $id) {
            $row = CreditNoteTransaction::find($id);
            if (!$row) continue;

            $uploadId = $row->upload_id;
            $row->update([
                'note_no' => $request->invoice_no[$id] ?? $row->note_no,
                'note_date' => $request->date[$id] ?? $row->note_date,
                //'party_name' => $request->party_name[$id] ?? $request->ledger[$id],
                'party_name' => $request->party_name[$id]
                    ?? $request->party_ledger[$id]
                    ?? $request->ledger[$id]
                    ?? $row->party_name,
                'place_of_supply' => $request->place_of_supply[$id] ?? $row->place_of_supply,
                'sales_ledger' => $request->ledger[$id]  ?? $row->sales_ledger,
                'status' => 'submitted',
                'vchType' => $request->voucher_type[$id] ?? $row->vch_type
            ]);
        }

        if ($uploadId) {
            $saved = CreditNoteTransaction::where('upload_id', $uploadId)
                ->where('status', 'saved')
                ->count();
            $pending = CreditNoteTransaction::where('upload_id', $uploadId)
                ->where('status', 'pending')
                ->count();
            $total = CreditNoteTransaction::where('upload_id', $uploadId)
                ->count();
            BulkCreditNoteUpload::where('id', $uploadId)->update([
                'total' => $total,
                'saved' => $saved,
                'pending' => $pending
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Saved Successfully'
        ]);
        // return back()->with('success', 'Records Updated');
    }

    public function processing_debit_note()
    {
        $iPartyId = session('iPartyId');
        $uploads = BulkDebitNoteUpload::where('iPartyId', $iPartyId)
            ->where('saved','>',0)
            ->latest()
            ->get();
        
        $clients = Client::orderBy('name')->get();
        $years = DB::table('YearMaster')
            ->where('iPartyId', $iPartyId)
            ->orderBy('strYear', 'asc')
            ->get();
        $this->ensureFinancialYearInSession($years);
        return view('admin.transaction-processing.debit_note.index', compact('uploads', 'clients', 'years'));
    }

    public function preview_processing_debit_note($id)
    {
        $iPartyId = session('iPartyId');
        if (!$iPartyId) {
            return redirect()->route('transaction_processing.processing_purchase')
                ->with('error', 'Please select company first');
        }

        $rows = DebitNoteTransaction::where('upload_id', $id)
            ->where('status', 'saved')
            ->where('iPartyId', $iPartyId)
            ->get();
        
        $vchTypes = DB::table('VchHistory')
            ->where('iPartyId', $iPartyId)
            ->where('vchType','Debit Note')
            ->distinct()
            ->pluck('vchType');
        $states = DB::table('state')
            ->pluck('stateName');
        $groups = DB::table('GroupMaster')
            ->where('iPartyId', $iPartyId)
            ->distinct()
            ->pluck('strGroupName');
        $parents = DB::table('LedgerMaster')
            ->select('strParents')
            ->where('iPartyId', $iPartyId)
            ->where('strParents','Sundry Creditors')
            ->distinct()
            ->get();
        $ledgers = Ledger::getAllCreditorsLedgers($iPartyId);

        $iGstLedgers = Ledger::getAlliGstLedgers($iPartyId);
        $cGstLedgers = Ledger::getAllcGstLedgers($iPartyId);
        $sGstLedgers = Ledger::getAllsGstLedgers($iPartyId);

        $purchaseLedgers = Ledger::getPurchaseLedgers($iPartyId);
        $purchaseGstMappings = $this->getLedgerGstMappings($iPartyId, 'Purchase Accounts');
        $stockItems = DB::table('StockItemMaster')
            ->where('iPartyId', $iPartyId)
            ->orderBy('strItemName', 'asc')
            ->get();
        return view('admin.transaction-processing.debit_note.preview', compact(
            'rows',
            'ledgers',
            'vchTypes',
            'groups',
            'states',
            'parents',
            'iGstLedgers',
            'cGstLedgers',
            'sGstLedgers',
            'stockItems',
            'purchaseLedgers',
            'purchaseGstMappings'
        ));
    }

    public function debit_note_sumbit(Request $request)
    {
        $iPartyId = session('iPartyId');
        if (!$iPartyId) {
            return redirect()->route('transaction_processing.processing_purchase')
                ->with('error', 'Please select company first');
        }

        $uploadId = null;
        foreach ($request->selected as $key => $id) {
            $row = DebitNoteTransaction::find($id);
            if (!$row) continue;
            $uploadId = $row->upload_id;
            $row->update([
                'note_no' => $request->invoice_no[$id] ?? $row->note_no,
                'note_date' => $request->date[$id] ?? $row->note_date,
                'party_name' => $request->party_name[$id]
                    ?? $request->party_ledger[$id]
                    ?? $request->ledger[$id]
                    ?? $row->party_name,
                'place_of_supply' => $request->place_of_supply[$id] ?? $row->place_of_supply,
                'purchase_ledger' => $request->ledger[$id] ?? $row->purchase_ledger,
                'vchType' => $request->voucher_type[$id] ?? $row->vch_type,
                'status' => 'submitted',
            ]);
        }

        if ($uploadId) {
            $saved = DebitNoteTransaction::where('upload_id', $uploadId)
                ->where('status', 'saved')
                ->count();
            $pending = DebitNoteTransaction::where('upload_id', $uploadId)
                ->where('status', 'pending')
                ->count();
            $total = DebitNoteTransaction::where('upload_id', $uploadId)
                ->count();
            BulkDebitNoteUpload::where('id', $uploadId)->update([
                'total' => $total,
                'saved' => $saved,
                'pending' => $pending
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Sumbit Successfully'
        ]);
    }

    public function processing_journal()
    {
        $iPartyId = session('iPartyId');
        $uploads = BulkJournalUpload::where('iPartyId', $iPartyId)
            ->where('saved','>',0)
            ->latest()
            ->get();
        
        $clients = Client::orderBy('name')->get();
        $years = DB::table('YearMaster')
            ->where('iPartyId', $iPartyId)
            ->orderBy('strYear', 'asc')
            ->get();
        $this->ensureFinancialYearInSession($years);
        return view('admin.transaction-processing.journal.index', compact('uploads', 'clients', 'years'));
    }

    public function preview_processing_journal($id)
    {
        $iPartyId = session('iPartyId');
        if (!$iPartyId) {
            return redirect()->route('transaction_processing.processing_purchase')
                ->with('error', 'Please select company first');
        }

        $rows = JournalTransaction::where('upload_id', $id)
            ->where('status', 'saved')
            ->where('iPartyId', $iPartyId)
            ->get();
        $ledgers = Ledger::getAllLedgers($iPartyId);
        return view('admin.transaction-processing.journal.preview', compact(
            'rows',
            'ledgers'
        ));
    }

    public function journal_sumbit(Request $request)
    {
        $iPartyId = session('iPartyId');
        if (!$iPartyId) {
            return redirect()->route('transaction_processing.processing_purchase')
                ->with('error', 'Please select company first');
        }

        $uploadId = null;
        foreach ($request->selected as $key => $id) {
            $row = DebitNoteTransaction::find($id);
            if (!$row) continue;
            $uploadId = $row->upload_id;
            $row->update([
                'note_no' => $request->invoice_no[$id] ?? $row->note_no,
                'note_date' => $request->date[$id] ?? $row->note_date,
                'party_name' => $request->party_name[$id]
                    ?? $request->party_ledger[$id]
                    ?? $request->ledger[$id]
                    ?? $row->party_name,
                'place_of_supply' => $request->place_of_supply[$id] ?? $row->place_of_supply,
                'purchase_ledger' => $request->ledger[$id] ?? $row->purchase_ledger,
                'vchType' => $request->voucher_type[$id] ?? $row->vch_type,
                'status' => 'submitted',
            ]);
        }

        if ($uploadId) {
            $saved = DebitNoteTransaction::where('upload_id', $uploadId)
                ->where('status', 'saved')
                ->count();
            $pending = DebitNoteTransaction::where('upload_id', $uploadId)
                ->where('status', 'pending')
                ->count();
            $total = DebitNoteTransaction::where('upload_id', $uploadId)
                ->count();
            BulkDebitNoteUpload::where('id', $uploadId)->update([
                'total' => $total,
                'saved' => $saved,
                'pending' => $pending
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Sumbit Successfully'
        ]);
    }

}
