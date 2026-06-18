<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Ledger;
use Illuminate\Support\Facades\DB;
use App\Models\Client;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Models\BulkBankUpload;
use App\Models\BankTransaction;
use App\Support\VoucherValidation;

class BankUploadController extends Controller
{
    use VoucherValidation;


    private function bankTransactionsHaveYearColumn(): bool
    {
        return DB::getSchemaBuilder()->hasColumn('bank_transactions', 'strYear');
    }

    private function bankVoucherCombinationExists($voucherType, $partyName, $txnDate, ?int $ignoreId = null): bool
    {
        $iPartyId = session('iPartyId');
        $year = session('year');
        $voucherType = trim((string) $voucherType);
        //$voucherNumber = trim((string) $voucherNumber);
        $partyName = trim((string) $partyName);

        if (!$iPartyId || !$year || $voucherType === '' || $partyName === '' || !$txnDate) {
            return false;
        }

        $query = DB::table('bank_transactions')
            ->where('iPartyId', $iPartyId)
            ->where('status', 'saved')
            ->where('vch_type', $voucherType)
            ->where('ledger_name', $partyName)
            ->where('txn_date', $txnDate);

        if ($this->bankTransactionsHaveYearColumn()) {
            $query->where('strYear', $year);
        }

        if ($query->exists()) {
            return true;
        }

        $yearId = DB::table('YearMaster')
            ->where('iPartyId', $iPartyId)
            ->where('strYear', $year)
            ->value('iYearId');

        return DB::table('VchHistory')
            ->where('iPartyId', $iPartyId)
            ->where('vchType', $voucherType)
            ->where('trnAccount', $partyName)
            ->where('strVchDate', $this->historyDate($txnDate))
            ->where('iYearId', $yearId)
            ->exists();
    }

    public function index()
    {
        $iPartyId = session('iPartyId');
        $uploads = BulkBankUpload::where('iPartyId', $iPartyId)
            ->whereIn('status', ['Pending','Processing'])
            ->latest()
            ->get();
        $banks = DB::table('LedgerMaster')
            ->select('iLedgerId as id', 'strCustomerName as name')
            ->where('iPartyId', $iPartyId)
            ->where('strParents', 'Bank Accounts')
            ->get();
        $years = DB::table('YearMaster')
            ->where('iPartyId', $iPartyId)
            ->orderBy('strYear', 'asc')
            ->get();
        $clients = Client::orderBy('name')->get();
        return view('admin.bulkupload.bank.index', compact('uploads', 'clients', 'banks','years'));
    }

    public function upload(Request $request)
    {
        $iPartyId = session('iPartyId');

        if (!$iPartyId) {
            return back()->with('error', 'Please select company first');
        }

        $request->validate([
            'bank_file' => 'required|mimes:xlsx,xls|max:30720'
        ]);

        $file = $request->file('bank_file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('bank_uploads', $fileName, 'public');

        $upload = BulkBankUpload::create([
            'iPartyId'  => $iPartyId,
            'file_name' => $fileName,
            'file_path' => $path,
            'bank_name' => $request->bank_name,
            'status'    => 'Processing'
        ]);

        $rows = Excel::toArray([], $file);
        $sheet = $rows[0] ?? [];

        if (empty($sheet)) {
            return back()->with('error', 'Empty file');
        }

        // 🔥 FIND HEADER
        $headerRowIndex = null;

        foreach ($sheet as $i => $row) {
            // $text = strtolower(implode(' ', $row));
            $text = strtolower(implode(' ', array_map(fn($value) => trim((string) $value), $row)));

            if (
                strpos($text, 'date') !== false &&
                (strpos($text, 'narration') !== false ||
                    strpos($text, 'description') !== false ||
                    strpos($text, 'particular') !== false)
            ) {
                $headerRowIndex = $i;
                break;
            }
        }

        // if ($headerRowIndex === null) {
        //     dd($sheet);
        // }
        if ($headerRowIndex === null) {
            $upload->update(['status' => 'Failed']);

            return back()->with('error', 'Could not find the bank statement header row. Please upload a file with Date and Particulars/Narration/Description columns.');
        }

        $header = array_map(function ($value) {
            $value = preg_replace('/\s+/', ' ', str_replace("\xc2\xa0", ' ', (string) $value));

        // $header = array_map(fn($v) => strtolower(trim($v)), $sheet[$headerRowIndex]);
            return strtolower(trim($value));
        }, $sheet[$headerRowIndex]);
        $find = function ($keys) use ($header) {
            foreach ($header as $i => $col) {
                foreach ($keys as $k) {
                    if (strpos($col, $k) !== false) return $i;
                }
            }
            return null;
        };

        $txnDateIndex   = $find(['date']);
        $valueDateIndex = $find(['value']);
        $narrationIndex = $find(['narration', 'description', 'particular']);
        $refIndex       = $find(['ref', 'cheque', 'utr']);
        $balanceIndex   = $find(['balance']);
        $debitIndex     = $find(['debit', 'withdrawal']);
        $creditIndex    = $find(['credit', 'deposit']);
        $amountIndex    = $find(['amount']);

        if ($txnDateIndex === null || $narrationIndex === null || ($debitIndex === null && $creditIndex === null && $amountIndex === null)) {
            $upload->update(['status' => 'Failed']);

            return back()->with('error', 'Invalid bank statement format. Required columns: Date, Particulars/Narration/Description, and Debit/Credit or Amount.');
        }

        // 🔧 DATE PARSER
        $parseDate = function ($val) {
            try {
                if ($val === null || trim((string) $val) === '') {
                    return null;
                }

                if (is_numeric($val)) {
                    return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val)->format('Y-m-d');
                }
                // return date('Y-m-d', strtotime($val));
                $timestamp = strtotime((string) $val);

                return $timestamp ? date('Y-m-d', $timestamp) : null;
            } catch (\Exception $e) {
                return null;
            }
        };

        // 🔥 NUMBER CLEANER (MAIN FIX)
        $clean = function ($val) {

            if ($val === null) return 0;

            // If value is array (due to Excel split)
            if (is_array($val)) {
                $val = implode('', $val);
            }

            // Remove unwanted chars
            $val = str_replace([' ', ','], '', $val);
            $val = preg_replace('/[^0-9.\-]/', '', $val);

            // Prevent overflow (VERY IMPORTANT)
            if (strlen($val) > 15) {
                return 0;
            }

            return is_numeric($val) ? (float)$val : 0;
        };

        $total = 0;
        $prevBalance = null;

        foreach ($sheet as $i => $row) {

            if ($i <= $headerRowIndex) continue;
            if (empty(array_filter($row))) continue;

            $txn_date   = $parseDate($row[$txnDateIndex] ?? null);
            $value_date = $parseDate($row[$valueDateIndex] ?? $row[$txnDateIndex] ?? null);

            //$narration = isset($row[$narrationIndex]) ? trim($row[$narrationIndex]) : '';
            $narration = '';

            if ($narrationIndex !== null) {
                // $narrationParts = array_slice($row, $narrationIndex, 5); // take next 5 cols
                $stopIndexes = array_filter([
                    $refIndex,
                    $debitIndex,
                    $creditIndex,
                    $amountIndex,
                    $balanceIndex,
                ], fn($index) => $index !== null && $index > $narrationIndex);
                $narrationLength = $stopIndexes
                    ? min($stopIndexes) - $narrationIndex
                    : 1;
                $narrationParts = array_slice($row, $narrationIndex, max(1, $narrationLength));
                $narration = implode(' ', array_filter($narrationParts));
                $narration = preg_replace('/\s+/', ' ', trim($narration));
            }
            //$ref_no    = isset($row[$refIndex]) ? trim($row[$refIndex]) : '';
            $ref_no = '';

            if ($refIndex !== null) {
                $ref_no = preg_replace('/[^A-Za-z0-9]/', '', $row[$refIndex] ?? '');
            }

            if (!$txn_date && !$narration) continue;

            $debit = 0;
            $credit = 0;
            $amount = 0;

            // ✅ PRIORITY 1: NARRATION (MOST IMPORTANT)
            if ($narration) {

                if (preg_match('/([\d,]+\.\d{2}|[\d,]+)\s*(CR|DR)/i', $narration, $match)) {

                    $amount = $clean($match[1]);

                    if (strtoupper($match[2]) == 'DR') {
                        $debit = $amount;
                    } else {
                        $credit = $amount;
                    }
                }
            }

            // ✅ PRIORITY 2: Debit/Credit columns (fallback only)
            if ($amount == 0 && ($debitIndex !== null || $creditIndex !== null)) {

                $debit  = $clean($row[$debitIndex] ?? 0);
                $credit = $clean($row[$creditIndex] ?? 0);
                $amount = $debit > 0 ? $debit : $credit;
            }

            if ($amount == 0) {
                continue;
            }

            // ✅ PRIORITY 3: Amount column
            if ($amount == 0 && $amountIndex !== null) {

                $amount = $clean($row[$amountIndex]);

                if (stripos($narration, 'dr') !== false) {
                    $debit = $amount;
                } else {
                    $credit = $amount;
                }
            }

            // ✅ PRIORITY 4: Balance diff (last fallback)
            if ($amount == 0 && $balanceIndex !== null) {

                $currentBalance = $clean($row[$balanceIndex]);

                if ($prevBalance !== null) {
                    $diff = $currentBalance - $prevBalance;

                    if ($diff > 0) $credit = abs($diff);
                    elseif ($diff < 0) $debit = abs($diff);
                }

                $prevBalance = $currentBalance;
                $amount = $debit > 0 ? $debit : $credit;
            }

            $balance = $clean($row[$balanceIndex] ?? 0);
            $txn_type = $debit > 0 ? 'Debit' : 'Credit';

            $unique_key = md5($txn_date . $amount . $narration . $ref_no);

            if (BankTransaction::where('iPartyId', $iPartyId)->where('unique_key', $unique_key)->exists()) {
                continue;
            }
            if ($amount > 999999999999 || $balance > 999999999999) {
                continue; // skip invalid rows
            }

            $transactionData = [
                'iPartyId'        => $iPartyId,
                'upload_id'       => $upload->id,
                'bank_name'       => $request->bank_name,
                'txn_date'        => $txn_date,
                'value_date'      => $value_date,
                'narration'       => $narration,
                'ref_no'          => $ref_no,
                'debit'           => $debit,
                'credit'          => $credit,
                'amount'          => $amount,
                'txn_type'        => $txn_type,
                'balance'         => $balance,
                'ledger_name'     => null,
                'unique_key'      => $unique_key,
                'is_reconciled'   => 0,
                'source'          => 'upload',
                'status'          => 'pending'
            ];
            if ($this->bankTransactionsHaveYearColumn()) {
                $transactionData['strYear'] = session('year');
            }

            BankTransaction::create($transactionData);
            $total++;
        }

        $upload->update([
            'total'   => $total,
            'pending' => $total,
            'status'  => 'Pending'
        ]);

        return back()->with('success', "Upload complete: $total rows inserted");
    }

    public function findColumn($header, $keywords)
    {
        foreach ($header as $index => $col) {
            foreach ($keywords as $keyword) {
                if (strpos($col, $keyword) !== false) {
                    return $index;
                }
            }
        }
        return null;
    }

    public function markSuspense(Request $request)
    {
        // $row = BankTransaction::find($request->id);
        // if (!$row) {
        $ids = $request->has('selected') ? (array) $request->selected : [$request->id];
        $ids = array_values(array_filter($ids));

        if (empty($ids)) {
             return response()->json([
                'status' => false,
                'message' => 'No rows selected'
            ]);
        }

        $rows = BankTransaction::whereIn('id', $ids)->get();

        if ($rows->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Not found'
            ]);
        }
        // $row->update([
        //     'is_suspense' => 1,
        //     //'status' => 'suspense', // optional
        //     'suspense_reason' => $request->remark
        // ]);
        $uploadIds = $rows->pluck('upload_id')->filter()->unique();

        foreach ($rows as $row) {
            $row->update([
                'is_suspense' => 1,
                'status' => 'suspense',
                'suspense_reason' => $request->remark
            ]);
        }

        foreach ($uploadIds as $uploadId) {
            $this->refreshUploadSummary($uploadId);
        }
        return response()->json([
            'status' => true,
            'message' => $rows->count() . ' row(s) marked as Suspense'
        ]);
    }

    private function refreshUploadSummary($uploadId)
    {
        $saved = BankTransaction::where('upload_id', $uploadId)
            ->where('status', 'saved')
            ->count();

        $pending = BankTransaction::where('upload_id', $uploadId)
            ->where('status', 'pending')
            ->count();

        $synced = BankTransaction::where('upload_id', $uploadId)
            ->where('status', 'synced')
            ->count();

        $total = BankTransaction::where('upload_id', $uploadId)
            ->count();

        $status = ($pending == 0) ? 'Completed' : 'Pending';

        BulkBankUpload::where('id', $uploadId)->update([
            'total' => $total,
            'saved' => $saved,
            'pending' => $pending,
            'synced' => $synced,
            'status' => $status
        ]);
    }

    // PREVIEW PAGE
    public function preview($id)
    {
        $iPartyId = session('iPartyId');
        if (!$iPartyId) {
            return redirect()->route('data_entry_operators.bulkuploadpurchase')
                ->with('error', 'Please select company first');
        }

        $rows = BankTransaction::where('upload_id', $id)
            ->whereIn('status', ['pending','suspense'])
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
            ->where('strParents', 'Bank Accounts')
            ->distinct()
            ->get();
        $ledgers = Ledger::getAllBankLedgers($iPartyId);
        $allLedgers   = Ledger::getAllLedgers($iPartyId);
        $bankLedgers  = Ledger::getAllBankCashLedgers($iPartyId);


        return view('admin.bulkupload.bank.preview', compact(
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

    public function save(Request $request)
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

            $txnDate = $request->txn_date[$id] ?? $row->txn_date;
            $ledgerName = $request->ledger[$id] ?? $row->ledger_name;
            $chequeNo = $request->cheque_no[$id] ?? $row->cheque_no;
            $refNo = $request->ref_no[$id] ?? $row->ref_no;
            $voucherType = $request->type[$id] ?? $row->vch_type;

            if ($this->bankVoucherCombinationExists($voucherType, $ledgerName, $txnDate, $row->id)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Duplicate voucher combination is not allowed.'
                ]);
            }

            $row->txn_date    = $txnDate;
            $row->value_date  = $request->value_date[$id] ?? $row->value_date;
            $row->narration   = $request->narration[$id] ?? $row->narration;
            $row->ledger_name = $ledgerName;
            // 🔥 NEW FIELDS
            $row->cheque_no   = $chequeNo;
            $row->ref_no      = $refNo;
            $row->cost_center = $request->cost_center[$id] ?? null;
            $row->vch_type    = $voucherType;
            if ($this->bankTransactionsHaveYearColumn()) {
                $row->strYear = session('year');
            }
            $row->status      = 'saved';

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
            $status = ($pending == 0) ? 'Completed' : 'Pending';
            BulkBankUpload::where('id', $uploadId)->update([
                'total'   => $total,
                'saved'   => $saved,
                'pending' => $pending,
                'status'  => $status
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Saved Successfully'
        ]);
    }

    public function delete($id)
    {
        $row = BankTransaction::find($id);

        if (!$row) {
            return response()->json([
                'status' => false,
                'message' => 'Record not found'
            ]);
        }

        $uploadId = $row->upload_id;

        // delete transaction
        $row->delete();

        // recalculate counts
        $saved = BankTransaction::where('upload_id', $uploadId)
            ->where('status', 'saved')
            ->count();

        $pending = BankTransaction::where('upload_id', $uploadId)
            ->where('status', 'pending')
            ->count();

        $synced = BankTransaction::where('upload_id', $uploadId)
            ->where('status', 'synced')
            ->count();

        $total = BankTransaction::where('upload_id', $uploadId)
            ->count();

        // update bulk upload summary
        BulkBankUpload::where('id', $uploadId)->update([
            'total' => $total,
            'saved' => $saved,
            'pending' => $pending,
            'synced' => $synced
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Record deleted successfully'
        ]);
    }

    public function update(Request $request)
    {
        // Validation
        $request->validate([
            'txn_date' => 'required|date',
            'value_date' => 'required|date',
            'type' => 'required',
            'amount' => 'required|numeric|min:0',
            'ledger' => 'required',
            'cheque_no' => 'nullable|string|max:100',
            'cost_center' => 'nullable|string|max:150'
        ]);
        $uploadId = 0;
        $row = BankTransaction::find($request->id);

        if (!$row) {
            return response()->json([
                'status' => false,
                'message' => 'Transaction not found'
            ]);
        }
        $uploadId = $row->upload_id;
        if ($this->bankVoucherCombinationExists($request->type, $request->ledger, $request->txn_date, $row->id)) {
            return response()->json([
                'status' => false,
                'message' => 'Duplicate voucher combination is not allowed.'
            ]);
        }

        $amount = $request->amount;
        $debit = 0;
        $credit = 0;
        $vch_type = $request->type;
        // 🔥 PAYMENT
        if ($request->type == 'Payment') {
            $debit = $amount;
            $credit = 0;
        }

        // 🔥 RECEIPT
        elseif ($request->type == 'Receipt') {
            $credit = $amount;
            $debit = 0;
        }

        // 🔥 CONTRA (TALLY STYLE - DOUBLE ENTRY)
        // elseif ($request->type == 'Contra') {

        //     // Required: from_ledger & to_ledger
        //     if (!$request->from_ledger || !$request->to_ledger) {
        //         return response()->json([
        //             'status' => false,
        //             'message' => 'From & To ledger required for Contra'
        //         ]);
        //     }

        //     // Delete old entry (optional)
        //     $row->delete();

        //     // Entry 1 → Debit (To Ledger)
        //     BankTransaction::create([
        //         'txn_date' => $request->txn_date,
        //         'value_date' => $request->value_date,
        //         'narration' => $request->narration,
        //         'ledger_name' => $request->to_ledger,
        //         'debit' => $amount,
        //         'credit' => 0,
        //         'vch_type' => $vch_type,
        //         // 🔥 NEW FIELDS
        //         'cheque_no'   => $request->cheque_no,
        //         'ref_no'      => $request->reference ?? $row->ref_no,
        //         'cost_center' => $request->cost_center,
        //         'status' => 'saved'
        //     ]);

        //     // Entry 2 → Credit (From Ledger)
        //     BankTransaction::create([
        //         'txn_date' => $request->txn_date,
        //         'value_date' => $request->value_date,
        //         'narration' => $request->narration,
        //         'ledger_name' => $request->from_ledger,
        //         'debit' => 0,
        //         'credit' => $amount,
        //         'vch_type' => $vch_type,
        //         // 🔥 NEW FIELDS
        //         'cheque_no'   => $request->cheque_no,
        //         'ref_no'      => $request->reference ?? $row->ref_no,
        //         'cost_center' => $request->cost_center,
        //         'status' => 'saved'
        //     ]);

        //     return response()->json([
        //         'status' => true,
        //         'message' => 'Contra entry created'
        //     ]);
        // }

        // 🔥 NORMAL UPDATE
        $updateData = [
            'txn_date' => $request->txn_date,
            'value_date' => $request->value_date,
            'narration' => $request->narration,
            'ledger_name' => $request->ledger,
            'debit' => $debit,
            'credit' => $credit,
            'vch_type' => $vch_type,
            // 🔥 NEW FIELDS
            'cheque_no'   => $request->cheque_no,
            'ref_no'      => $request->reference ?? $row->ref_no,
            'cost_center' => $request->cost_center,
            'status' => 'saved'
        ];

        if ($this->bankTransactionsHaveYearColumn()) {
            $updateData['strYear'] = session('year');
        }
        $row->update($updateData);

        $saved = BankTransaction::where('upload_id', $uploadId)
            ->where('status', 'saved')
            ->count();

        $pending = BankTransaction::where('upload_id', $uploadId)
            ->where('status', 'pending')
            ->count();

        $total = BankTransaction::where('upload_id', $uploadId)
            ->count();

        $status = ($pending == 0) ? 'Completed' : 'Pending';
        BulkBankUpload::where('id', $uploadId)->update([
            'total'   => $total,
            'saved'   => $saved,
            'pending' => $pending,
            'status'  => $status
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Updated successfully'
        ]);
    }

    public function changeUploadStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'status' => 'required|string'
        ]);

        $upload = BulkBankUpload::find($request->id);

        if (!$upload) {
            return response()->json([
                'status' => false,
                'message' => 'Upload not found'
            ]);
        }

        $upload->update([
            'status' => $request->status
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Status updated successfully'
        ]);
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;

        if (!$ids || count($ids) == 0) {
            return response()->json([
                'status' => false,
                'message' => 'No records selected'
            ]);
        }

        DB::beginTransaction();

        try {

            foreach ($ids as $id) {

                // 🔥 Delete transactions
                BankTransaction::where('upload_id', $id)->delete();

                // 🔥 Delete upload
                BulkBankUpload::where('id', $id)->delete();
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Bulk delete successful'
            ]);
        } catch (\Exception $e) {

            DB::rollback();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
