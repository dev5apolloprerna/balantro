<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BulkJournalUpload;
use App\Models\JournalTransaction;
use App\Models\JournalTransactionItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Client;
use App\Models\Ledger;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;


class JournalController extends Controller
{
    public function index()
    {
        $iPartyId = session('iPartyId');
        if (!$iPartyId) {
            return redirect()->back()->with('error', 'Please select company first');
        }
        $uploads = BulkJournalUpload::where('iPartyId', $iPartyId)
            ->whereColumn('total', '<>', 'saved') // 🔥 main condition
            ->orderBy('id', 'desc')
            ->get();
        $years = DB::table('YearMaster')
            ->where('iPartyId', $iPartyId)
            ->orderBy('strYear', 'asc')
            ->limit(3)
            ->get();
        $clients = Client::orderBy('name')->get();
        $ledgers = Ledger::getAllLedgers($iPartyId);
        return view('admin.bulkupload.journal.index', compact('uploads', 'clients','ledgers','years'));
    }

    // ─────────────────────────────────────────────
    // 1. UPLOAD EXCEL
    // ─────────────────────────────────────────────
    public function upload(Request $request)
    {
        $iPartyId = session('iPartyId');

        if (!$iPartyId) {
            return redirect()->back()->with('error', 'Please select company first');
        }

        $request->validate([
            'file' => 'required|mimes:xlsx,csv'
        ]);

        $file = $request->file('file');
        $path = $file->store('uploads/journal');

        $upload = BulkJournalUpload::create([
            'iPartyId' => $iPartyId,
            'batch_id' => Str::uuid(),
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'status' => 'processing',
            'type' => 'Excel'
        ]);

        // 👉 Read Excel (use maatwebsite/excel)
        $rows = \Excel::toArray([], $file)[0];

        $grouped = collect($rows)->skip(1)->groupBy(function ($row) {
            return trim($row[0]); // Journal No
        });
        $totalInvoices = 0;
        foreach ($grouped as $journalNo => $entries) {

            $totalDebit = 0;
            $totalCredit = 0;

            foreach ($entries as $row) {
                $amount = (float)$row[4];
                if (strtolower($row[3]) == 'dr') {
                    $totalDebit += $amount;
                } else {
                    $totalCredit += $amount;
                }
            }
            $dateValue = $entries[0][1];

            if (is_numeric($dateValue)) {
                // Excel serial number
                $date = ExcelDate::excelToDateTimeObject($dateValue)->format('Y-m-d');
            } else {
                // String format (like 21-05-2026)
                $date = Carbon::createFromFormat('d-m-Y', $dateValue)->format('Y-m-d');
            }
            $transaction = JournalTransaction::create([
                'iPartyId' => session('iPartyId'),
                'upload_id' => $upload->id,
                'journal_no' => $journalNo,
                //'date' => Carbon::parse($entries[0][1])->format('Y-m-d'),
                'date' => $date,
                'narration' => $entries[0][5] ?? null,
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'status' => 'pending'
            ]);

            foreach ($entries as $row) {

                $amount = (float)$row[4];
                $drcr = strtolower($row[3]);
                $ledger = Ledger::getLedgerByName($iPartyId, $row[2]);

                JournalTransactionItem::create([
                    'transaction_id' => $transaction->id,
                    // 'ledger_name' => $row[2],
                    'ledger_id' => $ledger->id ?? null,
                    'ledger_name' => $ledger->name ?? $row[2],
                    'dr_cr' => ucfirst($drcr),
                    'debit' => $drcr == 'dr' ? $amount : 0,
                    'credit' => $drcr == 'cr' ? $amount : 0,
                    'narration' => $row[5] ?? null
                ]);
            }
            $totalInvoices++;
        }

        $upload->update([
            'status' => $totalInvoices > 0 ? 'pending' : 'completed',
            'total'   => $totalInvoices,
            'pending' => $totalInvoices,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Journal uploaded successfully'
        ]);
    }

    // ─────────────────────────────────────────────
    // 2. PREVIEW (LIST PAGE)
    // ─────────────────────────────────────────────
    public function preview($uploadId)
    {
         $iPartyId = session('iPartyId'); // same as sales

        if (!$iPartyId) {
            return redirect()->route('dn.upload')
                ->with('error', 'Please select company first');
        }

        $rows = JournalTransaction::with('items')
            ->where('upload_id', $uploadId)
            ->where('status', 'pending')
            ->paginate(50);

        
        $ledgers = Ledger::getAllLedgers($iPartyId);
        
        return view('admin.bulkupload.journal.preview', compact('rows','ledgers'));
    }

    // ─────────────────────────────────────────────
    // 3. SHOW (EDIT MODAL)
    // ─────────────────────────────────────────────
    public function show($id)
    {
        $txn = JournalTransaction::with('items')->findOrFail($id);

        return response()->json([
            'id' => $txn->id,
            'journal_no' => $txn->journal_no,
            'date' => $txn->date,
            'narration' => $txn->narration,
            'total_debit' => $txn->total_debit,
            'total_credit' => $txn->total_credit,
            'status' => $txn->status,
            'pending_issues' => $this->getJournalPendingIssues($txn),
            //'items' => $txn->items->map(function ($item) {
            'items' => $txn->items->map(function ($item) use ($txn) {
                $ledgerId = $item->ledger_id;

                if (!$ledgerId && $item->ledger_name) {
                    $ledger = Ledger::getLedgerByName($txn->iPartyId, $item->ledger_name);
                    $ledgerId = $ledger->id ?? null;
                }
                return [
                    'id' => $item->id,
                    'ledger_name' => $item->ledger_name,
                    'ledger_id' => $ledgerId, // 'ledger_id' => $item->ledger_id,
                    'dr_cr' => $item->dr_cr,
                    'debit' => $item->debit,
                    'credit' => $item->credit,
                    'narration' => $item->narration
                ];
            })
        ]);
    }

    private function getJournalPendingIssues(JournalTransaction $transaction): array
    {
        if (strtolower((string) $transaction->status) !== 'pending') {
            return [];
        }

        $transaction->loadMissing('items');
        $issues = [];
        $date = $transaction->date instanceof \DateTimeInterface
            ? $transaction->date->format('Y-m-d')
            : $transaction->date;

        if (blank($transaction->journal_no)) {
            $issues[] = [
                'field' => 'journal_no',
                'message' => 'Journal number is required.',
            ];
        }

        if (!$date || (session('year_from') && session('year_to') && ($date < session('year_from') || $date > session('year_to')))) {
            $issues[] = [
                'field' => 'date',
                'message' => 'Journal date is outside the selected financial year.',
            ];
        }

        if ($transaction->items->isEmpty()) {
            $issues[] = [
                'field' => 'ledger',
                'message' => 'At least one journal ledger row is required.',
            ];
        }

        $hasLedgerIssue = false;
        $hasAmountIssue = false;
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($transaction->items as $item) {
            $debit = (float) ($item->debit ?? 0);
            $credit = (float) ($item->credit ?? 0);
            $totalDebit += $debit;
            $totalCredit += $credit;

            $ledger = $item->ledger_id
                ? Ledger::getLedgerById($transaction->iPartyId, $item->ledger_id)
                : ($item->ledger_name ? Ledger::getLedgerByName($transaction->iPartyId, $item->ledger_name) : null);

            if (!$ledger) {
                $hasLedgerIssue = true;
            }

            if (($debit <= 0 && $credit <= 0) || ($debit > 0 && $credit > 0)) {
                $hasAmountIssue = true;
            }
        }

        if ($hasLedgerIssue) {
            $issues[] = [
                'field' => 'ledger',
                'message' => 'One or more journal ledgers are missing or not matched with ledger master.',
            ];
        }

        if ($hasAmountIssue) {
            $issues[] = [
                'field' => 'amount',
                'message' => 'Each journal row must have either debit or credit amount.',
            ];
        }

        if (abs($totalDebit - $totalCredit) > 0.01) {
            $issues[] = [
                'field' => 'amount',
                'message' => 'Total debit and total credit must be equal.',
            ];
        }

        return $issues;
    }

    public function rematch($id)
    {
        $iPartyId = session('iPartyId');
        if (!$iPartyId) {
            return redirect()->route('journal.index')
                ->with('alert', 'Please select company first');
        }

        $upload = BulkJournalUpload::where('id', $id)
            ->where('iPartyId', $iPartyId)
            ->first();

        if (!$upload) {
            return back()->with('alert', 'Upload not found');
        }

        $matched = 0;
        $stillPending = 0;
        $totalPending = 0;

        try {
            DB::transaction(function () use ($upload, $iPartyId, &$matched, &$stillPending, &$totalPending) {
                $transactions = JournalTransaction::with('items')
                    ->where('upload_id', $upload->id)
                    ->where('iPartyId', $iPartyId)
                    ->where('status', 'pending')
                    ->get();

                $totalPending = $transactions->count();

                foreach ($transactions as $transaction) {
                    if ($this->rematchPendingJournalTransaction($transaction)) {
                        $transaction->status = 'saved';
                        $matched++;
                    } else {
                        $transaction->status = 'pending';
                        $stillPending++;
                    }

                    $transaction->save();
                }

                $this->refreshJournalUploadCounts($upload->id);
            });
        } catch (\Throwable $exception) {
            \Log::error('Journal re-match failed', [
                'upload_id' => $upload->id,
                'iPartyId' => $iPartyId,
                'error' => $exception->getMessage(),
            ]);

            return back()->with('alert', 'Re-Match failed. Please try again or contact support if the issue continues.');
        }

        if ($totalPending === 0) {
            return back()->with('notice', 'No pending journal entries were found for re-match.');
        }

        if ($matched === 0) {
            return back()->with(
                'alert',
                "Re-Match completed, but no pending entries could be saved. {$stillPending} pending entr"
                    . ($stillPending === 1 ? 'y requires' : 'ies require')
                    . ' ledger, date, or amount updates before trying again.'
            );
        }

        $message = "Re-Match completed successfully. {$matched} pending entr"
            . ($matched === 1 ? 'y was' : 'ies were')
            . " saved; {$stillPending} remain pending.";

        return back()->with('notice', $message);
    }

    private function rematchPendingJournalTransaction(JournalTransaction $transaction): bool
    {
        $totalDebit = 0.0;
        $totalCredit = 0.0;

        foreach ($transaction->items as $item) {
            $ledger = $item->ledger_id
                ? Ledger::getLedgerById($transaction->iPartyId, $item->ledger_id)
                : ($item->ledger_name ? Ledger::getLedgerByName($transaction->iPartyId, $item->ledger_name) : null);

            $debit = (float) ($item->debit ?? 0);
            $credit = (float) ($item->credit ?? 0);

            $item->fill([
                'ledger_id' => $ledger?->id ?? $item->ledger_id,
                'ledger_name' => $ledger?->name ?? $item->ledger_name,
                'dr_cr' => $debit > 0 ? 'Dr' : ($credit > 0 ? 'Cr' : $item->dr_cr),
                'debit' => $debit,
                'credit' => $credit,
            ])->save();

            $totalDebit += $debit;
            $totalCredit += $credit;
        }

        $transaction->fill([
            'total_debit' => round($totalDebit, 2),
            'total_credit' => round($totalCredit, 2),
        ]);

        return empty($this->getJournalPendingIssues($transaction));
    }

    private function refreshJournalUploadCounts(int $uploadId): void
    {
        $saved = JournalTransaction::where('upload_id', $uploadId)->where('status', 'saved')->count();
        $pending = JournalTransaction::where('upload_id', $uploadId)->where('status', 'pending')->count();
        $total = JournalTransaction::where('upload_id', $uploadId)->count();

        BulkJournalUpload::where('id', $uploadId)->update([
            'total' => $total,
            'saved' => $saved,
            'pending' => $pending,
            'status' => $pending > 0 ? 'pending' : 'completed',
        ]);
    }

    // ─────────────────────────────────────────────
    // 4. UPDATE (EDIT SAVE)
    // ─────────────────────────────────────────────
    public function update(Request $request)
    {
        DB::beginTransaction();
        try {

            $txn = JournalTransaction::findOrFail($request->id);

            $totalDebit = 0;
            $totalCredit = 0;

            foreach ($request->items as $item) {
                $totalDebit += $item['debit'];
                $totalCredit += $item['credit'];
            }

            // 🔥 VALIDATION
            if ($totalDebit != $totalCredit) {
                return response()->json([
                    'status' => false,
                    'message' => 'Journal not balanced'
                ]);
            }

            $txn->update([
                'journal_no' => $request->journal_no,
                'date' => $request->date,
                'narration' => $request->narration,
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'status' => 'saved'
            ]);

            // Delete old items
            JournalTransactionItem::where('transaction_id', $txn->id)->delete();

            foreach ($request->items as $item) {

                $debit = $item['debit'] ?? 0;
                $credit = $item['credit'] ?? 0;

                $dr_cr = $debit > 0 ? 'Dr' : 'Cr';
                $ledger = Ledger::getLedgerById($txn->iPartyId, $item['ledger_id']); // $ledger = DB::table('LedgerMaster')->where('iLedgerId', $item['ledger_id'])->first();
                JournalTransactionItem::create([
                    'transaction_id' => $txn->id,
                    'ledger_id'      => $ledger->id ?? null, // 'ledger_id'      => $ledger->iLedgerId ?? null,
                    'ledger_name'    => $ledger->name ?? null, // 'ledger_name'    => $ledger->strCustomerName ?? null,
                    'dr_cr' => $dr_cr, // ✅ FIXED
                    'debit' => $debit,
                    'credit' => $credit,
                    'narration' => $item['narration'] ?? null
                ]);
            }

            if ($txn) {

                $saved = JournalTransaction::where('upload_id', $txn->upload_id)->where('status', 'saved')->count();
                $pending = JournalTransaction::where('upload_id', $txn->upload_id)->where('status', 'pending')->count();
                $total = JournalTransaction::where('upload_id', $txn->upload_id)->count();

                BulkJournalUpload::where('id', $txn->upload_id)->update([
                    'total' => $total,
                    'saved' => $saved,
                    'pending' => $pending,
                    'status' => $pending == 0 ? 'completed' : 'pending'
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Updated Successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // ─────────────────────────────────────────────
    // 5. SAVE (BULK SAVE LIKE SALES)
    // ─────────────────────────────────────────────
    public function save(Request $request)
    {
        $uploadId = null;

        foreach ($request->selected as $id) {

            $txn = JournalTransaction::find($id);
            if (!$txn) continue;

            $uploadId = $txn->upload_id;

            if ($txn->total_debit != $txn->total_credit) {
                continue; // skip unbalanced
            }

            $txn->update([
                'status' => 'saved'
            ]);
        }

        if ($uploadId) {

            $saved = JournalTransaction::where('upload_id', $uploadId)->where('status', 'saved')->count();
            $pending = JournalTransaction::where('upload_id', $uploadId)->where('status', 'pending')->count();
            $total = JournalTransaction::where('upload_id', $uploadId)->count();

            BulkJournalUpload::where('id', $uploadId)->update([
                'total' => $total,
                'saved' => $saved,
                'pending' => $pending,
                'status' => $pending == 0 ? 'completed' : 'pending'
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Saved Successfully'
        ]);
    }

    // ─────────────────────────────────────────────
    // 6. SUBMIT
    // ─────────────────────────────────────────────
    public function submit(Request $request)
    {
        foreach ($request->selected as $id) {

            $txn = JournalTransaction::find($id);
            if (!$txn) continue;

            if ($txn->total_debit != $txn->total_credit) {
                return response()->json([
                    'status' => false,
                    'message' => "Journal {$txn->journal_no} not balanced"
                ]);
            }

            $txn->update([
                'status' => 'saved'
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Submitted Successfully'
        ]);
    }

    // ─────────────────────────────────────────────
    // 7. DELETE
    // ─────────────────────────────────────────────
    public function delete($id)
    {
        $txn = JournalTransaction::findOrFail($id);

        $txn->delete();

        return response()->json([
            'status' => true,
            'message' => 'Deleted Successfully'
        ]);
    }

    public function changeUploadStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'status' => 'required|string'
        ]);

        $upload = BulkJournalUpload::find($request->id);

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

                $transactions = JournalTransaction::where('upload_id', $id)->pluck('id');

                JournalTransactionItem::whereIn('transaction_id', $transactions)->delete();
                JournalTransaction::where('upload_id', $id)->delete();
                BulkJournalUpload::where('id', $id)->delete();
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

    public function manualCreate(Request $request)
    {
        $iPartyId = session('iPartyId');
        if (!$iPartyId) {
            return response()->json([
                'status' => false,
                'message' => 'Please select company first'
            ]);
        }
        DB::beginTransaction();
        try {
            // ✅ CREATE / UPDATE UPLOAD
            $upload = BulkJournalUpload::where('iPartyId', $iPartyId)
                ->where('type', 'Manual')
                ->first();
            if ($upload) {
                $upload->update([
                    'pending' => $upload->pending + 1,
                    'total'   => $upload->total + 1,
                ]);
            } else {
                $upload = BulkJournalUpload::create([
                    'iPartyId'  => $iPartyId,
                    'batch_id'  => Str::uuid(),
                    'file_name' => 'Manual Entry',
                    'file_path' => 'manual',
                    'type'      => 'Manual',
                    'status'    => 'Pending',
                    'total_rows' => 0,
                    'processed_rows' => 0,
                    'synced'    => 0,
                    'total'     => 1,
                    'pending'   => 1,
                    'saved'     => 0
                ]);
            }
            // =====================================================
            // ✅ CREATE JOURNAL TRANSACTION
            // =====================================================
            $transaction = JournalTransaction::create([
                'iPartyId'   => $iPartyId,
                'upload_id'  => $upload->id,
                'journal_no' => $request->journal_no,
                'date'       => $request->date ?? now(),
                'narration'  => $request->narration,
                'status'     => 'pending',
                'source'     => 'manual',
            ]);
            $totalDebit = 0;
            $totalCredit = 0;
            // =====================================================
            // ✅ INSERT ITEMS (DR / CR ENTRIES)
            // =====================================================
            foreach ($request->items as $row) {
                //$ledger = $row['ledger_name'] ?? null;
                // $ledgerData = $ledger
                //     ? Ledger::getLedgerByName($iPartyId, $ledger)
                //     : null;
                $ledger = DB::table('LedgerMaster')->where('iLedgerId', $row['ledger_id'])->first();
                $debit  = (float)($row['debit'] ?? 0);
                $credit = (float)($row['credit'] ?? 0);
                $dr_cr = $debit > 0 ? 'Dr' : 'Cr';
                JournalTransactionItem::create([
                    'iPartyId'       => $iPartyId,
                    'transaction_id' => $transaction->id,
                    'upload_id'      => $upload->id,
                    'ledger_id'      => $ledger->iLedgerId ?? null,
                    'ledger_name'    => $ledger->strCustomerName ?? null,
                    'dr_cr'          => $dr_cr,
                    'debit'          => $debit,
                    'credit'         => $credit,
                    'narration'      => $request->narration
                ]);
                $totalDebit  += $debit;
                $totalCredit += $credit;
            }
            // =====================================================
            // ✅ VALIDATION (VERY IMPORTANT)
            // =====================================================
            if ($totalDebit != $totalCredit) {
                throw new \Exception('Debit & Credit not matched');
            }
            // =====================================================
            // ✅ UPDATE TOTALS
            // =====================================================
            $transaction->update([
                'total_debit'  => $totalDebit,
                'total_credit' => $totalCredit,
            ]);
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Journal Created',
                'id' => $transaction->id
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
