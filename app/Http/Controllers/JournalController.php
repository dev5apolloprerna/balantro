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

                JournalTransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'ledger_name' => $row[2],
                    'dr_cr' => ucfirst($drcr),
                    'debit' => $drcr == 'dr' ? $amount : 0,
                    'credit' => $drcr == 'cr' ? $amount : 0,
                    'narration' => $row[5] ?? null
                ]);
            }
            $totalInvoices++;
        }

        $upload->update([
            'status' => 'completed',
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
            ->get();

        
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
            'items' => $txn->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'ledger_name' => $item->ledger_name,
                    'ledger_id' => $item->ledger_id,
                    'dr_cr' => $item->dr_cr,
                    'debit' => $item->debit,
                    'credit' => $item->credit,
                    'narration' => $item->narration
                ];
            })
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
                $ledger = DB::table('LedgerMaster')->where('iLedgerId', $item['ledger_id'])->first();
                JournalTransactionItem::create([
                    'transaction_id' => $txn->id,
                    'ledger_id'      => $ledger->iLedgerId ?? null,
                    'ledger_name'    => $ledger->strCustomerName ?? null,
                    'dr_cr' => $dr_cr, // ✅ FIXED
                    'debit' => $debit,
                    'credit' => $credit,
                    'narration' => $item['narration'] ?? null
                ]);
            }

            if ($txn) {

                $saved = JournalTransaction::where('upload_id', $request->id)->where('status', 'saved')->count();
                $pending = JournalTransaction::where('upload_id', $request->id)->where('status', 'pending')->count();
                $total = JournalTransaction::where('upload_id', $request->id)->count();

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
