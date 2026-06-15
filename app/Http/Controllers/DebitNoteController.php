<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Client;

use App\Models\DebitNoteTransaction;
use App\Models\DebitNoteTransactionItem;
use App\Models\BulkDebitNoteUpload;
use Illuminate\Support\Facades\Session;
use App\Models\Ledger;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use App\Models\DebitNoteCustomGst;

class DebitNoteController extends Controller
{
    public function index(Request $request)
    {
        $iPartyId = session('iPartyId');

        if (!$iPartyId) {
            return back()->with('error', 'Please select company first');
        }
        $uploads = BulkDebitNoteUpload::where('iPartyId', $iPartyId)
            ->whereIn('status', ['Pending','Processing'])
            ->orderBy('id', 'desc')
            ->get();

        $clients = Client::orderBy('name')->get();
        $years = DB::table('YearMaster')
            ->where('iPartyId', $iPartyId)
            ->orderBy('strYear', 'asc')
            ->get();
        // ✅ Voucher Types
        $vchTypes = DB::table('VchHistory')
            ->where('iPartyId', $iPartyId)
            ->where('vchType', 'Debit Note')
            ->distinct()
            ->pluck('vchType');

        // ✅ States
        $states = DB::table('state')
            ->pluck('stateName');
        
        // ✅ Groups
        $groups = DB::table('GroupMaster')
            ->where('iPartyId', $iPartyId)
            ->distinct()
            ->pluck('strGroupName');

        // ✅ Party Ledgers (Sundry Creditors)
        $parents = DB::table('LedgerMaster')
            ->select('strParents')
            ->where('iPartyId', $iPartyId)
            ->where('strParents', 'Sundry Creditors')
            ->distinct()
            ->get();

        $ledgers = Ledger::getAllCreditorsLedgers($iPartyId);

        // ✅ GST Ledgers
        $iGstLedgers = Ledger::getAlliGstLedgers($iPartyId);
        $cGstLedgers = Ledger::getAllcGstLedgers($iPartyId);
        $sGstLedgers = Ledger::getAllsGstLedgers($iPartyId);

        // ✅ SALES RETURN LEDGER (IMPORTANT 🔥)
        $purchaseLedgers = Ledger::getPurchaseLedgers($iPartyId);
        $stockItems = DB::table('StockItemMaster')
            ->select('*', 'CGSTLedgerId as cgst_id', 'SGSTLedgerId as sgst_id', 'IGSTLedgerId as igst_id')
            ->where('iPartyId', $iPartyId)
            ->orderBy('strItemName', 'asc') // optional (recommended)
            ->get();
        $purchaseGstMappings = $this->getPurchaseLedgerGstMappings($iPartyId);
        return view('admin.bulkupload.debit_note.index', compact('uploads', 'clients','vchTypes','states','groups','parents'
        ,'ledgers','iGstLedgers','cGstLedgers','sGstLedgers','purchaseLedgers','stockItems','years','purchaseGstMappings'));
    }

    public function selectCompany($id)
    {
        $client = Client::where('id', $id)->first();
        Session::put('iPartyId', $id);
        Session::put('client_name', $client->name);
        Session::put('guid', $client->guid);
        return back(); // ->with('success', 'Records Updated');
        //return redirect()->route('sales.upload.page');
    }
    private function getSalesLedgerGstMappings($partyId): array
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
            ->where('strParents', 'Sales Accounts')
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

    private function getGstMapping($partyId,$salesLedgerName,$itemName = null)
    {
        $mapping = [
            'cgst_id' => null,
            'sgst_id' => null,
            'igst_id' => null,

            'cgst_name' => null,
            'sgst_name' => null,
            'igst_name' => null,
        ];

        /*
        |--------------------------------------------------------------------------
        | FIRST PRIORITY : ITEM GST MAPPING
        |--------------------------------------------------------------------------
        */

        if (!empty($itemName))
        {
            $itemName = trim((string) $itemName);
            $item = DB::table('StockItemMaster')
                ->where('iPartyId', $partyId)
                ->where('strItemName', $itemName)
                ->first();

            if ($item)
            {
                $mapping['cgst_id'] = $item->CGSTLedgerId;
                $mapping['sgst_id'] = $item->SGSTLedgerId;
                $mapping['igst_id'] = $item->IGSTLedgerId;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | SECOND PRIORITY : LEDGER GST MAPPING
        |--------------------------------------------------------------------------
        */

        if (
            empty($mapping['cgst_id']) &&
            empty($mapping['sgst_id']) &&
            empty($mapping['igst_id'])
        )
        {
            $ledger = DB::table('LedgerMaster')
                ->where('iPartyId', $partyId)
                ->where('strCustomerName', $salesLedgerName)
                ->first();

            if ($ledger)
            {
                $mapping['cgst_id'] = $ledger->CGSTLedgerId;
                $mapping['sgst_id'] = $ledger->SGSTLedgerId;
                $mapping['igst_id'] = $ledger->IGSTLedgerId;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | GET GST LEDGER NAMES
        |--------------------------------------------------------------------------
        */

        if (!empty($mapping['cgst_id']))
        {
            $cgst = DB::table('LedgerMaster')
                ->where('iPartyId', $partyId)
                ->where('iLedgerId', $mapping['cgst_id'])
                ->first();

            $mapping['cgst_name'] =
                $cgst->strCustomerName ?? null;
        }

        if (!empty($mapping['sgst_id']))
        {
            $sgst = DB::table('LedgerMaster')
                ->where('iPartyId', $partyId)
                ->where('iLedgerId', $mapping['sgst_id'])
                ->first();

            $mapping['sgst_name'] =
                $sgst->strCustomerName ?? null;
        }

        if (!empty($mapping['igst_id']))
        {
            $igst = DB::table('LedgerMaster')
                ->where('iPartyId', $partyId)
                ->where('iLedgerId', $mapping['igst_id'])
                ->first();

            $mapping['igst_name'] =
                $igst->strCustomerName ?? null;
        }

        return $mapping;
    }

    private function resolveGstLedgerId($partyId, $submittedId, ?array $mapping, string $key)
    {
        return $submittedId ?: ($mapping[$key] ?? null);
    }

    private function gstLedgerName($partyId, $ledgerId): ?string
    {
        return $ledgerId ? (Ledger::getLedgerById($partyId, $ledgerId)?->name ?? null) : null;
    }

    private function firstItemNameFromRequest(Request $request): ?string
    {
        $items = $request->input('items', []);
        if (empty($items) || !is_array($items)) {
            return null;
        }

        $first = reset($items);
        $itemName = $first['item_name'] ?? $first['item'] ?? null;
        return $itemName ? trim((string) $itemName) : null;
    }

    private function applyGstMappingToCustomSlot(array $slot, array $mapping): array
    {
        $slot['igst_ledger_id'] = $slot['igst_ledger_id'] ?? null;
        $slot['cgst_ledger_id'] = $slot['cgst_ledger_id'] ?? null;
        $slot['sgst_ledger_id'] = $slot['sgst_ledger_id'] ?? null;

        if (!empty($slot['igst_amount']) && empty($slot['igst_ledger_id'])) {
            $slot['igst_ledger_id'] = $mapping['igst_id'] ?? null;
        }

        if (!empty($slot['cgst_amount']) && empty($slot['cgst_ledger_id'])) {
            $slot['cgst_ledger_id'] = $mapping['cgst_id'] ?? null;
        }

        if (!empty($slot['sgst_amount']) && empty($slot['sgst_ledger_id'])) {
            $slot['sgst_ledger_id'] = $mapping['sgst_id'] ?? null;
        }

        return $slot;
    }

    private function storeNoItemLedgerWiseCustomGst(DebitNoteTransaction $transaction, array $noitemRows, array $customSlots, array $gstMapping, bool $isIgst): void
    {
        DebitNoteCustomGst::where('transaction_id', $transaction->id)->delete();

        $slotMap = collect($customSlots)
            ->map(function ($slot) {
                $slot['rate'] = (float) ($slot['rate'] ?? 0);
                $slot['purchase_ledger_id'] = $slot['purchase_ledger_id'] ?? null;
                return $slot;
            })
            ->groupBy(fn ($slot) => sprintf('%s|%s', $slot['rate'], $slot['purchase_ledger_id'] ?? ''));

        foreach ($noitemRows as $row) {
            $gstRate = (float) ($row['gst'] ?? 0);
            $rowAmount = (float) ($row['amount'] ?? 0);
            if ($rowAmount <= 0) {
                continue;
            }

            $ledgerId = $row['ledger'] ?? null;
            $slotKey = sprintf('%s|%s', $gstRate, $ledgerId ?? '');
            $slot = null;

            if ($slotMap->has($slotKey) && $slotMap[$slotKey]->isNotEmpty()) {
                $slot = $slotMap[$slotKey]->shift();
            } elseif ($slotMap->has(sprintf('%s|', $gstRate)) && $slotMap[sprintf('%s|', $gstRate)]->isNotEmpty()) {
                $slot = $slotMap[sprintf('%s|', $gstRate)]->shift();
            }

            $purchaseLedgerRow = !empty($ledgerId) ? Ledger::getLedgerById($transaction->iPartyId, $ledgerId) : null;
            $rowMapping = $purchaseLedgerRow ? $this->getGstMapping($transaction->iPartyId, $purchaseLedgerRow->name) : $gstMapping;
            $slot = $slot ? $this->applyGstMappingToCustomSlot($slot, $rowMapping) : null;

            $igstAmount = 0;
            $cgstAmount = 0;
            $sgstAmount = 0;

            if ($slot) {
                $igstAmount = (float) ($slot['igst_amount'] ?? 0);
                $cgstAmount = (float) ($slot['cgst_amount'] ?? 0);
                $sgstAmount = (float) ($slot['sgst_amount'] ?? 0);
            } else {
                $gstTotal = ($rowAmount * $gstRate) / 100;
                if ($isIgst) {
                    $igstAmount = $gstTotal;
                } else {
                    $cgstAmount = $gstTotal / 2;
                    $sgstAmount = $gstTotal / 2;
                }
                $slot = [
                    'igst_ledger_id' => $rowMapping['igst_id'] ?? null,
                    'cgst_ledger_id' => $rowMapping['cgst_id'] ?? null,
                    'sgst_ledger_id' => $rowMapping['sgst_id'] ?? null,
                ];
            }

            DebitNoteCustomGst::create([
                'transaction_id' => $transaction->id,
                'gst_rate' => $gstRate,
                'taxable' => $rowAmount,
                'ledger_id' => $purchaseLedgerRow?->id ?? ($transaction->purchase_ledger_id ?? null),
                'ledger_name' => $purchaseLedgerRow?->name ?? ($transaction->purchase_ledger_name ?? null),
                'amount' => $rowAmount,
                'igst_ledger_id' => $slot['igst_ledger_id'] ?? null,
                'igst_ledger_name' => $this->gstLedgerName($transaction->iPartyId, $slot['igst_ledger_id'] ?? null),
                'igst_amount' => $igstAmount,
                'cgst_ledger_id' => $slot['cgst_ledger_id'] ?? null,
                'cgst_ledger_name' => $this->gstLedgerName($transaction->iPartyId, $slot['cgst_ledger_id'] ?? null),
                'cgst_amount' => $cgstAmount,
                'sgst_ledger_id' => $slot['sgst_ledger_id'] ?? null,
                'sgst_ledger_name' => $this->gstLedgerName($transaction->iPartyId, $slot['sgst_ledger_id'] ?? null),
                'sgst_amount' => $sgstAmount,
            ]);
        }
    }

    private function buildSalesCustomGstSlots(array $rows, int $partyId): array
    {
        $slots = [];

        foreach ($rows as $row) {
            $amount = $this->toNumber($row['amount'] ?? 0);
            $sgst = $this->toNumber($row['sgst'] ?? 0);
            $cgst = $this->toNumber($row['cgst'] ?? 0);
            $igst = $this->toNumber($row['igst'] ?? 0);
            $gstRate = $amount > 0
                ? round((($sgst + $cgst + $igst) * 100) / $amount, 2)
                : 0;
            $key = (string) $gstRate;

            if (!isset($slots[$key])) {
                $mapping = $this->getGstMapping($partyId, $row['sales_ledger'] ?? null);
                $salesLedger = !empty($row['sales_ledger'])
                    ? Ledger::getLedgerByName($partyId, $row['sales_ledger'])
                    : null;

                $slots[$key] = [
                    'gst_rate' => $gstRate,
                    'taxable' => 0,
                    'ledger_id' => $salesLedger?->id,
                    'ledger_name' => $salesLedger?->name,
                    'amount' => 0,
                    'cgst_ledger_id' => $mapping['cgst_id'],
                    'cgst_ledger_name' => $mapping['cgst_name'],
                    'cgst_amount' => 0,
                    'sgst_ledger_id' => $mapping['sgst_id'],
                    'sgst_ledger_name' => $mapping['sgst_name'],
                    'sgst_amount' => 0,
                    'igst_ledger_id' => $mapping['igst_id'],
                    'igst_ledger_name' => $mapping['igst_name'],
                    'igst_amount' => 0,
                ];
            }

            $slots[$key]['taxable'] += $amount;
            $slots[$key]['amount'] += $amount;
            $slots[$key]['cgst_amount'] += $cgst;
            $slots[$key]['sgst_amount'] += $sgst;
            $slots[$key]['igst_amount'] += $igst;
        }

        return array_values($slots);
    }

    private function hasRequiredGstLedgers(array $slots, bool $isIgst): bool
    {
        foreach ($slots as $slot) {
            if ($isIgst) {
                if (($slot['igst_amount'] ?? 0) > 0 && empty($slot['igst_ledger_id'])) {
                    return false;
                }
            } elseif (
                (($slot['cgst_amount'] ?? 0) > 0 && empty($slot['cgst_ledger_id'])) ||
                (($slot['sgst_amount'] ?? 0) > 0 && empty($slot['sgst_ledger_id']))
            ) {
                return false;
            }
        }

        return true;
    }

    private function getRoundOffLedger($partyId)
    {
        return DB::table('LedgerMaster')
            ->where('iPartyId', $partyId)
            ->where(function($q){
                $q->where('strCustomerName', 'LIKE', '%round%')
                ->orWhere('strCustomerName', 'LIKE', '%roundoff%')
                ->orWhere('strCustomerName', 'LIKE', '%round off%')
                ->orWhere('strCustomerName', 'LIKE', '%rounding%');
            })
            ->first();
    }

    private function getRoundOffSetting($partyId): array
    {
        $profile = DB::table('profiles')->where('user_id', $partyId)->first();
        $roundOffLedger = null;

        $roundOffLedgerId = $profile->roundoff_ledger_id ?? null;
        $roundOffSide = $profile->roundoff_side ?? 'normal';

        if (!empty($roundOffLedgerId)) {
            $roundOffLedger = DB::table('LedgerMaster')
                ->where('iPartyId', $partyId)
                ->where('iLedgerId', $roundOffLedgerId)
                ->first();
        }

        return [
            'side' => $roundOffSide ?: 'normal',
            'ledger' => $roundOffLedger ?: $this->getRoundOffLedger($partyId),
        ];
    }

    private function calculateRoundOffAmount($amount, $sgst, $cgst, $igst, ?string $side = 'normal'): float
    {
        $grandTotal = (float) $amount + (float) $sgst + (float) $cgst + (float) $igst;

        $roundedGrandTotal = match ($side) {
            'upper_side' => ceil($grandTotal),
            'lower_side' => floor($grandTotal),
            default => round($grandTotal),
        };

        return round($roundedGrandTotal - $grandTotal, 2);
    }
    public function upload(Request $request)
    {
        $iPartyId = session('iPartyId');

        if (!$iPartyId) {
            return back()->with('error', 'Please select company first');
        }

        $request->validate([
            'debit_notes_file' => 'required|mimes:xlsx,xls|max:30720'
        ]);

        $file = $request->file('debit_notes_file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('debit_note_uploads', $fileName, 'public');

        $upload = BulkDebitNoteUpload::create([
            'iPartyId' => $iPartyId,
            'batch_id' => Str::uuid(),
            'file_name' => $fileName,
            'file_path' => $path,
            'note_type' => 'debit',
            'uploaded_by' => $request->user_id,
            'uploaded_at' => now(),
            'status' => 'Processing'
        ]);

        // ===============================
        // READ EXCEL
        // ===============================
        $spreadsheet = IOFactory::load($file->getRealPath());
        $worksheet = $spreadsheet->getActiveSheet();

        $sheet = [];
        foreach ($worksheet->getRowIterator() as $row) {
            $rowData = [];
            foreach ($row->getCellIterator() as $cell) {
                $rowData[] = $this->getCellValue($cell);
            }
            $sheet[] = $rowData;
        }

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        // ===============================
        // HEADER MAP (🔥 IMPORTANT)
        // ===============================
        $header = array_map(fn($h) => strtoupper(trim((string)$h)), $sheet[0]);
        $headerMap = array_flip($header);

        $isItemInvoice = in_array('NAME OF ITEM', $header);
        $isAccountingInvoice = in_array('PARTICULARS', $header);

        // =========================================================
        // ITEM BASED (WITH ITEMS)
        // =========================================================
        if ($isItemInvoice) {

            $noteGroups = [];

            foreach ($sheet as $key => $row) {
                if ($key == 0) continue;
                if (empty(array_filter($row))) continue;

                $noteNo = trim($row[$headerMap['SUPPLIER INV NO']] ?? '');
                $date      = $this->parseDate($row[$headerMap['INVOICE DATE']] ?? null);
                $partyName = $row[$headerMap['PARTY A/C NAME']] ?? '';

                $groupKey = $noteNo.'|'.$partyName.'|'.$date;
                $noteGroups[$groupKey][] = [
                    'date' => $this->parseDate($row[$headerMap['INVOICE DATE']] ?? null),
                    'gst_no' => $row[$headerMap['GST NO']] ?? '',
                    'party_name' => $row[$headerMap['PARTY A/C NAME']] ?? '',
                    'place_of_supply' => $row[$headerMap['PLACE OF SUPPLY']] ?? '',

                    // 🔥 IMPORTANT (PURCHASE LEDGER)
                    'purchase_ledger' => $row[$headerMap['PURCHASE LEDGER']] ?? '',

                    'item_name' => $row[$headerMap['NAME OF ITEM']] ?? '',
                    'qty' => $this->toNumber($row[$headerMap['QUANTITY']] ?? 0),
                    'rate' => $this->toNumber($row[$headerMap['RATE']] ?? 0),
                    'amount' => $this->toNumber($row[$headerMap['AMOUNT']] ?? 0),

                    'sgst' => $this->toNumber($row[$headerMap['SGST']] ?? 0),
                    'cgst' => $this->toNumber($row[$headerMap['CGST']] ?? 0),
                    'igst' => $this->toNumber($row[$headerMap['IGST']] ?? 0),

                    'total' => $this->toNumber($row[$headerMap['TOTAL AMOUNT']] ?? 0),
                ];
            }

            $total = 0;

            DB::transaction(function () use ($noteGroups, $upload, $iPartyId, &$total) {

                foreach ($noteGroups as $groupKey => $items) {

                    $sumAmount = array_sum(array_column($items, 'amount'));
                    $sumCgst = array_sum(array_column($items, 'cgst'));
                    $sumSgst = array_sum(array_column($items, 'sgst'));
                    $sumIgst = array_sum(array_column($items, 'igst'));
                    $sumTotal = array_sum(array_column($items, 'total'));

                    $first = $items[0];
                    $purchaseLedger = DB::table('LedgerMaster')
                        ->where('iPartyId', $iPartyId)
                        ->where('strCustomerName', $first['purchase_ledger'])
                        ->first();

                    $mapping = $this->getGstMapping(
                        $iPartyId,
                        $first['purchase_ledger']
                    );

                    $status = 'pending';
                    $is_igst = 0;
                    $amountMatched = true;
                    $rates = [];

                    foreach ($items as $item)
                    {
                        $gstRate = 0;
                        if($item['amount'] > 0)
                        {
                            $gstRate = (($item['cgst'] + $item['sgst'] + $item['igst']) * 100) / $item['amount'];
                        }

                        $rates[] = round($gstRate,2);
                        // $calculatedAmount = round((float)$item['quantity'] * (float)$item['rate'],2);
                        $calculatedAmount = round((float)$item['qty'] * (float)$item['rate'],2);
                        if($calculatedAmount != round((float)$item['amount'],2))
                        {
                            $amountMatched = false;
                            break;
                        }
                    }
                    if($amountMatched)
                    {
                        if($sumIgst > 0)
                        {
                            if(!empty($mapping['igst_id']))
                            {
                                $status = 'saved';
                            }

                            $is_igst = 1;
                        }
                        else
                        {
                            if(
                                !empty($mapping['cgst_id']) &&
                                !empty($mapping['sgst_id'])
                            )
                            {
                                $status = 'saved';
                            }

                            $is_igst = 0;
                        }
                    }

                    $rates = array_unique(array_filter($rates));

                    $gstMode =
                        count($rates) > 1
                            ? 'custom'
                            : 'standard';

                    $gstRate =
                        count($rates)
                            ? reset($rates)
                            : 0;
                    $roundOffSetting = $this->getRoundOffSetting($iPartyId);
                    $roundOffLedger = $roundOffSetting['ledger'];
                    $transaction = DebitNoteTransaction::create([
                        'iPartyId'        => $iPartyId,
                        'upload_id'       => $upload->id,
                        'note_type'       => 'debit',

                        'note_no'         => $first['note_no'],
                        'note_date'       => $first['date'],
                        'gst_no'          => $first['gst_no'],
                        'party_name'      => $first['party_name'],
                        'place_of_supply' => $first['place_of_supply'],

                        'purchase_ledger' => $first['purchase_ledger'], // 🔥
                        'purchase_ledger_id'    => $purchaseLedger?->iLedgerId,
                        'purchase_ledger_name'  => $purchaseLedger?->strCustomerName,
                        'vch_type'        => 'Debit Note',

                        'cgst_id'              => $mapping['cgst_id'],
                        'cgst_ledger_name'     => $mapping['cgst_name'],

                        'sgst_id'              => $mapping['sgst_id'],
                        'sgst_ledger_name'     => $mapping['sgst_name'],

                        'igst_id'              => $mapping['igst_id'],
                        'igst_ledger_name'     => $mapping['igst_name'],

                        'gst_mode'             => $gstMode,
                        'gst_rate'             => $gstRate,

                        'isWithItem'        => 1,
                        'strYear'           => session('year'),
                        'year_from_date'    => session('year_from'),
                        'year_to_date'      => session('year_to'),

                        'amount'               => $sumAmount,
                        'sgst'                 => $sumSgst,
                        'cgst'                 => $sumCgst,
                        'igst'                 => $sumIgst,
                        'total_amount'         => $sumTotal,

                        'is_igst'             => $is_igst,
                        'status'              => $status,
                        'is_delete'       => 0,
                        'roundoff_id'          => $roundOffLedger?->iLedgerId,
                        'roundoff_ledger_name' => $roundOffLedger?->strCustomerName,
                        'roundoff'          => $this->calculateRoundOffAmount($sumAmount, $sumSgst, $sumCgst, $sumIgst, $roundOffSetting['side']),
                    ]);

                    foreach ($items as $item) {
                        $itemMapping = $this->getGstMapping(
                            $iPartyId,
                            $first['purchase_ledger'],
                            $item['item_name']
                        );

                        $itemRate = 0;

                        if($item['amount'] > 0)
                        {
                            $itemRate =
                            (
                                (
                                    $item['cgst']
                                    + $item['sgst']
                                    + $item['igst']
                                ) * 100
                            ) / $item['amount'];
                        }
                        $stockItem = DB::table('StockItemMaster')
                            ->where('iPartyId',$iPartyId)
                            ->where('strItemName',$item['item_name'])
                            ->first();
                        DebitNoteTransactionItem::create([
                            // 'transaction_id' => $transaction->id,
                            // 'item_name' => $item['item_name'],
                            // 'quantity' => $item['qty'],
                            // 'rate' => $item['rate'],
                            // 'amount' => $item['amount'],
                            // 'cgst' => $item['cgst'],
                            // 'sgst' => $item['sgst'],
                            // 'igst' => $item['igst'],
                            // 'total_amount' => $item['total'],
                            'transaction_id'    => $transaction->id,

                            'item_name'         => $item['item_name'],
                            'quantity'          => $item['qty'],
                            'rate'              => $item['rate'],

                            'gst_rate'          => round($itemRate,2),
                            'unit'              => $stockItem->strBaseUnits ?? 'NOS',
                            'cgst_id'           => $itemMapping['cgst_id'],
                            'sgst_id'           => $itemMapping['sgst_id'],
                            'igst_id'           => $itemMapping['igst_id'],

                            'amount'            => $item['amount'],
                            'sgst'              => $item['sgst'],
                            'cgst'              => $item['cgst'],
                            'igst'              => $item['igst'],
                            'total_amount'      => $item['total'],
                        ]);
                    }

                    $total++;
                }
            });
            $savedCount = DebitNoteTransaction::where(
                    'upload_id',
                    $upload->id
                )
                ->where('status','saved')
                ->count();

            $pendingCount = $total - $savedCount;

            $upload->update([
                'total'   => $total,
                'saved'   => $savedCount,
                'pending' => $pendingCount,
                'status'  => $pendingCount > 0
                    ? 'Pending'
                    : 'Completed',
            ]);
            // $upload->update([
            //     'total' => $total,
            //     'pending' => $total,
            //     'status' => 'Pending'
            // ]);
        }

        // =========================================================
        // WITHOUT ITEM (ACCOUNTING) - FIXED
        // =========================================================
        elseif ($isAccountingInvoice) {
            
            // Group rows by note number (same as credit note)
            $noteGroups = [];
            foreach ($sheet as $key => $row) {
                if ($key == 0) continue;
                if (empty(array_filter($row))) continue;
                
                $noteNo = trim($row[$headerMap['SUPPLIER INV NO']] ?? '');
                if (empty($noteNo)) continue;
                $date      = $this->parseDate($row[$headerMap['INVOICE DATE']] ?? null);
                $partyName = $row[$headerMap['PARTY A/C NAME']] ?? '';

                $groupKey = $noteNo.'|'.$partyName.'|'.$date;
                $noteGroups[$groupKey][] = [
                    'note_no'         => $noteNo,
                    'date'            => $this->parseDate($row[$headerMap['INVOICE DATE']] ?? null),
                    'gst_no'          => $row[$headerMap['GST NO']] ?? '',
                    'party_name'      => $row[$headerMap['PARTY A/C NAME']] ?? '',
                    'place_of_supply' => $row[$headerMap['PLACE OF SUPPLY']] ?? '',
                    'purchase_ledger' => $row[$headerMap['PARTICULARS']] ?? '',
                    'amount'          => $this->toNumber($row[$headerMap['AMOUNT']] ?? 0),
                    'sgst'            => $this->toNumber($row[$headerMap['SGST']] ?? 0),
                    'cgst'            => $this->toNumber($row[$headerMap['CGST']] ?? 0),
                    'igst'            => $this->toNumber($row[$headerMap['IGST']] ?? 0),
                    'total_amount'    => $this->toNumber($row[$headerMap['TOTAL AMOUNT']] ?? 0),
                ];
            }

            $total = 0;

            DB::transaction(function () use ($noteGroups, $upload, $iPartyId, &$total) {
                foreach ($noteGroups as $groupKey => $rows) {
                    $first = $rows[0];
                    
                    // Calculate totals
                    $sumAmount = array_sum(array_column($rows, 'amount'));
                    $sumSgst = array_sum(array_column($rows, 'sgst'));
                    $sumCgst = array_sum(array_column($rows, 'cgst'));
                    $sumIgst = array_sum(array_column($rows, 'igst'));
                    $sumTotalAmount = array_sum(array_column($rows, 'total_amount'));
                    $isIgst = $sumIgst > 0;

                    $purchaseLedger = DB::table('LedgerMaster')
                        ->where('iPartyId', $iPartyId)
                        ->where('strCustomerName', $first['purchase_ledger'])
                        ->first();

                    // 🔥 IMPORTANT: Build custom GST slots from individual rows (ONE PER ROW)
                    $gstSlots = [];
                    foreach ($rows as $row) {
                        $amount = $row['amount'];
                        $sgst = $row['sgst'];
                        $cgst = $row['cgst'];
                        $igst = $row['igst'];
                        $gstRate = $amount > 0
                            ? round((($sgst + $cgst + $igst) * 100) / $amount, 2)
                            : 0;
                        
                        // Get mapping for this specific row's purchase ledger
                        $rowMapping = $this->getGstMapping($iPartyId, $row['purchase_ledger'] ?? $first['purchase_ledger']);
                        
                        // Get purchase ledger object for this row
                        $rowPurchaseLedger = null;
                        if (!empty($row['purchase_ledger'])) {
                            $rowPurchaseLedger = DB::table('LedgerMaster')
                                ->where('iPartyId', $iPartyId)
                                ->where('strCustomerName', $row['purchase_ledger'])
                                ->first();
                        }
                        
                        // Use row's purchase ledger or fallback to first row's purchase ledger
                        $ledgerId = $rowPurchaseLedger?->iLedgerId ?? $purchaseLedger?->iLedgerId;
                        $ledgerName = $rowPurchaseLedger?->strCustomerName ?? $purchaseLedger?->strCustomerName;
                        
                        $gstSlots[] = [
                            'gst_rate' => $gstRate,
                            'taxable' => $amount,
                            'ledger_id' => $ledgerId,
                            'ledger_name' => $ledgerName,
                            'amount' => $amount,
                            'cgst_ledger_id' => $rowMapping['cgst_id'],
                            'cgst_ledger_name' => $rowMapping['cgst_name'],
                            'cgst_amount' => $cgst,
                            'sgst_ledger_id' => $rowMapping['sgst_id'],
                            'sgst_ledger_name' => $rowMapping['sgst_name'],
                            'sgst_amount' => $sgst,
                            'igst_ledger_id' => $rowMapping['igst_id'],
                            'igst_ledger_name' => $rowMapping['igst_name'],
                            'igst_amount' => $igst,
                        ];
                    }
                    
                    // 🔥 For accounting invoices (without items), ALWAYS use 'custom' mode
                    $gstMode = 'custom';
                    
                    // Calculate a single representative GST rate for the transaction header
                    $gstRate = 0;
                    foreach ($gstSlots as $slot) {
                        if ($slot['gst_rate'] > 0) {
                            $gstRate = $slot['gst_rate'];
                            break;
                        }
                    }
                    
                    $mapping = $this->getGstMapping($iPartyId, $first['purchase_ledger']);
                    
                    // Status is always 'saved' for accounting invoices
                    $status = 'saved';
                    $roundOffSetting = $this->getRoundOffSetting($iPartyId);
                    $roundOffLedger = $roundOffSetting['ledger'];

                    $transaction = DebitNoteTransaction::create([
                        'iPartyId'        => $iPartyId,
                        'upload_id'       => $upload->id,
                        'note_type'       => 'debit',

                        'note_no'         => $first['note_no'],
                        'note_date'       => $first['date'],
                        'against_invoice' => $first['note_no'] ?? null,

                        'gst_no'          => $first['gst_no'],
                        'party_name'      => $first['party_name'],
                        'place_of_supply' => $first['place_of_supply'],

                        'purchase_ledger'      => $first['purchase_ledger'],
                        'purchase_ledger_id'   => $purchaseLedger?->iLedgerId,
                        'purchase_ledger_name' => $purchaseLedger?->strCustomerName,

                        'cgst_id'           => $mapping['cgst_id'],
                        'cgst_ledger_name'  => $mapping['cgst_name'],
                        'sgst_id'           => $mapping['sgst_id'],
                        'sgst_ledger_name'  => $mapping['sgst_name'],
                        'igst_id'           => $mapping['igst_id'],
                        'igst_ledger_name'  => $mapping['igst_name'],

                        'gst_mode'          => $gstMode,  // 🔥 ALWAYS 'custom'
                        'gst_rate'          => $gstRate,
                        'is_igst'           => $isIgst ? 1 : 0,
                        'vch_type'          => 'Debit Note',

                        'isWithItem'        => 0,
                        'strYear'           => session('year'),
                        'year_from_date'    => session('year_from'),
                        'year_to_date'      => session('year_to'),

                        'taxable_amount'    => $sumAmount,
                        'sgst'              => $sumSgst,
                        'cgst'              => $sumCgst,
                        'igst'              => $sumIgst,
                        'total_amount'      => $sumTotalAmount,

                        'status'            => $status,
                        'is_delete'         => 0,
                        
                        'strYear'           => session('year'),
                        'year_from_date'    => session('year_from'),
                        'year_to_date'      => session('year_to'),
                        'isWithItem'        => 0,
                        
                        'roundoff_id'          => $roundOffLedger?->iLedgerId,
                        'roundoff_ledger_name' => $roundOffLedger?->strCustomerName,
                        'roundoff'          => $this->calculateRoundOffAmount($sumAmount, $sumSgst, $sumCgst, $sumIgst, $roundOffSetting['side']),
                    ]);

                    // Create custom GST slots - ONE PER ROW
                    foreach ($gstSlots as $slot) {
                        if (($slot['igst_amount'] ?? 0) != 0 || 
                            ($slot['cgst_amount'] ?? 0) != 0 || 
                            ($slot['sgst_amount'] ?? 0) != 0) {
                            
                            DebitNoteCustomGst::create([
                                'transaction_id' => $transaction->id,
                                'gst_rate' => $slot['gst_rate'],
                                'taxable' => $slot['taxable'],
                                'ledger_id' => $slot['ledger_id'],
                                'ledger_name' => $slot['ledger_name'],
                                'amount' => $slot['amount'],
                                'cgst_ledger_id' => $slot['cgst_ledger_id'],
                                'cgst_ledger_name' => $slot['cgst_ledger_name'],
                                'cgst_amount' => $slot['cgst_amount'],
                                'sgst_ledger_id' => $slot['sgst_ledger_id'],
                                'sgst_ledger_name' => $slot['sgst_ledger_name'],
                                'sgst_amount' => $slot['sgst_amount'],
                                'igst_ledger_id' => $slot['igst_ledger_id'],
                                'igst_ledger_name' => $slot['igst_ledger_name'],
                                'igst_amount' => $slot['igst_amount'],
                            ]);
                        }
                    }
                    
                    $total++;
                }
            });
            
            $savedCount = DebitNoteTransaction::where('upload_id', $upload->id)
                ->where('status', 'saved')
                ->count();

            $pendingCount = $total - $savedCount;

            $upload->update([
                'total'   => $total,
                'saved'   => $savedCount,
                'pending' => $pendingCount,
                'status'  => $pendingCount > 0 ? 'Pending' : 'Completed',
            ]);
        }

        return back()->with('success', 'Debit Notes Uploaded Successfully');
    }

    public function preview($id)
    {
        $iPartyId = session('iPartyId'); // same as sales

        if (!$iPartyId) {
            return redirect()->route('dn.upload')
                ->with('error', 'Please select company first');
        }

        // ✅ Fetch pending Debit notes (same like sales)
        $rows = DebitNoteTransaction::where('upload_id', $id)
            ->where('status', 'Pending') // or 'pending' if using string
            ->where('iPartyId', $iPartyId)
            ->get();

        // ✅ Voucher Types
        $vchTypes = DB::table('VchHistory')
            ->where('iPartyId', $iPartyId)
            ->where('vchType', 'Debit Note')
            ->distinct()
            ->pluck('vchType');

        $vchTypes = $vchTypes->isEmpty()
            ? collect(['Debit Note'])
            : $vchTypes;

        // ✅ States
        $states = DB::table('state')
            ->pluck('stateName');

        // ✅ Groups
        $groups = DB::table('GroupMaster')
            ->where('iPartyId', $iPartyId)
            ->distinct()
            ->pluck('strGroupName');

        // ✅ Party Ledgers (Sundry Creditors)
        $parents = DB::table('LedgerMaster')
            ->select('strParents')
            ->where('iPartyId', $iPartyId)
            ->where('strParents', 'Sundry Creditors')
            ->distinct()
            ->get();

        $ledgers = Ledger::getAllCreditorsLedgers($iPartyId);

        // ✅ GST Ledgers
        $iGstLedgers = Ledger::getAlliGstLedgers($iPartyId);
        $cGstLedgers = Ledger::getAllcGstLedgers($iPartyId);
        $sGstLedgers = Ledger::getAllsGstLedgers($iPartyId);

        // ✅ SALES RETURN LEDGER (IMPORTANT 🔥)
        $purchaseLedgers = Ledger::getPurchaseLedgers($iPartyId);
        $stockItems = DB::table('StockItemMaster')
            ->select('*', 'CGSTLedgerId as cgst_id', 'SGSTLedgerId as sgst_id', 'IGSTLedgerId as igst_id')
            ->where('iPartyId', $iPartyId)
            ->orderBy('strItemName', 'asc') // optional (recommended)
            ->get();
        $purchaseGstMappings = $this->getPurchaseLedgerGstMappings($iPartyId);
        return view('admin.bulkupload.debit_note.preview', compact(
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

    public function storeLedger(Request $request)
    {
        $iPartyId = session('iPartyId');
        if (!$iPartyId) {
            return redirect()->route('dn.index')
                ->with('error', 'Please select company first');
        }
        DB::table('ledgers')->insert([
            'iPartyId' => $iPartyId,
            'Name' => $request->Name,
            'Parent' => $request->Parent,
            'MailingName' => $request->MailingName,

            'AddressLine1' => $request->AddressLine1,
            'AddressLine2' => $request->AddressLine2,
            'City' => $request->City,
            'State' => $request->State,
            'Country' => $request->Country,
            'Pincode' => $request->Pincode,

            'GstNo' => $request->GstNo,
            'GstRegistrationType' => $request->GstRegistrationType,

            'OpeningBalance' => 0,
            'OpeningType' => 'CR',

            'IsActive' => 1
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Ledger Created Successfully'
        ]);
    }

    public function destroy($id)
    {
        $iPartyId = session('iPartyId');

        if (!$iPartyId) {
            return response()->json([
                'status' => false,
                'message' => 'Please select company first'
            ]);
        }

        $row = DebitNoteTransaction::find($id);
        if (!$row) {
            return response()->json([
                'status' => false,
                'message' => 'Record not found'
            ]);
        }

        $upload_id = $row->upload_id;

        // ✅ Soft delete (as per your structure)
        $row->update(['is_delete' => 1]);

        // ✅ Delete items also (IMPORTANT 🔥)
        DebitNoteTransactionItem::where('transaction_id', $id)->delete();

        // ✅ Recalculate counts
        $saved = DebitNoteTransaction::where('upload_id', $upload_id)
            ->where('status', 'Saved')
            ->where('is_delete', 0)
            ->count();

        $pending = DebitNoteTransaction::where('upload_id', $upload_id)
            ->where('status', 'Pending')
            ->where('is_delete', 0)
            ->count();

        $total = DebitNoteTransaction::where('upload_id', $upload_id)
            ->where('is_delete', 0)
            ->count();

        // ✅ Update bulk upload table
        BulkDebitNoteUpload::where('id', $upload_id)->update([
            'total'     => $total,
            'saved' => $saved,
            'pending'    => $pending,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Debit Note deleted successfully'
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|integer',
            'invoice_no' => 'nullable|string',
            'date' => 'nullable|date',
            'party_name' => 'nullable|string',
            'gst_no' => 'nullable|string',
            'place_of_supply' => 'nullable|string',
            'Remarks' => 'nullable|string',
            'address' => 'nullable|string',
            'pincode' => 'nullable|string',
            'city' => 'nullable|string',
            'against_invoice' => 'nullable|string',
            'vchType' => 'nullable|string',
            'is_igst' => 'nullable|numeric',
            'gst_mode' => 'nullable|string',
            'entry_mode' => 'nullable|string',

            'amount' => 'nullable|numeric',
            'cgst' => 'nullable|numeric',
            'sgst' => 'nullable|numeric',
            'igst' => 'nullable|numeric',
            'total_amount' => 'nullable|numeric',
            'noitem_amount' => 'nullable|numeric',

            'igst_ledger' => 'nullable|integer',
            'cgst_ledger' => 'nullable|integer',
            'sgst_ledger' => 'nullable|integer',

            'purchase_ledger' => 'nullable|string',
            'sales_ledger' => 'nullable|string',

            'items' => 'nullable|array',
            'items.*.id' => 'nullable|integer',
            'items.*.item_name' => 'nullable|string',
            'items.*.hsn_code' => 'nullable|string',
            'items.*.gst_rate' => 'nullable|numeric',
            'items.*.quantity' => 'nullable|numeric',
            'items.*.unit' => 'nullable|string',
            'items.*.rate' => 'nullable|numeric',
            'items.*.amount' => 'nullable|numeric',
            'items.*.cgst' => 'nullable|numeric',
            'items.*.sgst' => 'nullable|numeric',
            'items.*.igst' => 'nullable|numeric',
            'items.*.total_amount' => 'nullable|numeric',

            'custom_slots' => 'nullable|array',
            'custom_slots.*.purchase_ledger_id' => 'nullable',
            'custom_slots.*.rate' => 'nullable|numeric',
            'custom_slots.*.taxable' => 'nullable|numeric',
            'custom_slots.*.igst_ledger_id' => 'nullable|integer',
            'custom_slots.*.igst_amount' => 'nullable|numeric',
            'custom_slots.*.cgst_ledger_id' => 'nullable|integer',
            'custom_slots.*.cgst_amount' => 'nullable|numeric',
            'custom_slots.*.sgst_ledger_id' => 'nullable|integer',
            'custom_slots.*.sgst_amount' => 'nullable|numeric',
            'noitem_rows' => 'nullable|array',
            'noitem_rows.*.ledger' => 'nullable',
            'noitem_rows.*.gst' => 'nullable|numeric',
            'noitem_rows.*.amount' => 'nullable|numeric',
        ]);
        $invoiceDate = $request->date;
        
        if ($invoiceDate < session('year_from') || $invoiceDate > session('year_to')) {
            return response()->json([
                'status' => false,
                'message' => 'Invoice date must be within selected financial year'
            ]);
        }
        
        DB::transaction(function () use ($data, $request) {

            $transaction = DebitNoteTransaction::findOrFail($data['id']);

            // ===============================
            // PURCHASE LEDGER
            // ===============================
            $ledgerName = isset($request->purchase_ledger) && $request->purchase_ledger != "Select Ledger" ? $request->purchase_ledger : $transaction->purchase_ledger;

            $ledger = $ledgerName
                ? Ledger::getLedgerByName($transaction->iPartyId, $ledgerName)
                : null;

            // ===============================
            // HEADER UPDATE (SAFE)
            // ===============================
            $transaction->update([
                'note_no' => $request->invoice_no ?: $transaction->note_no,
                'note_date' => $request->date ?: $transaction->note_date,
                'against_invoice' => $request->against_invoice,
                'party_name' => $request->party_name ?: $transaction->party_name,
                'gst_no' => $request->gst_no ?: $transaction->gst_no,
                'place_of_supply' => $request->place_of_supply ?: $transaction->place_of_supply,

                'address'    => $request->address ?: $transaction->address,
                'pincode'    => $request->pincode ?: $transaction->pincode,
                'city'  => $request->city ?: $transaction->city,

                'remarks' => $request->Remarks ?? $transaction->remarks,
                'vch_type' => $request->vchType ?? 'Debit Note',

                'is_igst' => $request->is_igst ?? $transaction->is_igst,
                'gst_mode' => $request->gst_mode ?? $transaction->gst_mode ?? 'standard',

                'purchase_ledger_id' => $ledger->id ?? $transaction->purchase_ledger_id,
                'purchase_ledger_name' => $ledger->name ?? $transaction->purchase_ledger_name,

                'strYear'       => session('year'),
                'year_from_date' => session('year_from'),
                'year_to_date'  => session('year_to'),
                'isWithItem'    => $request->entry_mode == 'noitem' ? 0 : 1,
            ]);

            $gstMode = $transaction->gst_mode;
            $gstMapping = $this->getGstMapping(
                $transaction->iPartyId,
                $ledger->name ?? $ledgerName,
                $this->firstItemNameFromRequest($request)
            );

            // ===============================
            // ITEMS HANDLING
            // ===============================
            $submittedIds = [];
            $sumAmount = $sumCgst = $sumSgst = $sumIgst = 0;

            if (!empty($data['items'])) {
                DebitNoteTransactionItem::where('transaction_id', $transaction->id)
                    ->delete();
                foreach ($data['items'] as $itemData) {
                    if (isset($itemData['item_name'])) {
                        $itemData['item_name'] = trim((string) $itemData['item_name']);
                    }

                    $itemId = $itemData['id'] ?? null;
                    $itemData['transaction_id'] = $transaction->id;

                    if ($itemId) {
                        $item = DebitNoteTransactionItem::find($itemId);

                        if ($item && $item->transaction_id == $transaction->id) {
                            $item->update($itemData);
                            $submittedIds[] = $itemId;
                        } else {
                            $itemId = null;
                        }
                    }

                    if (!$itemId) {
                        DebitNoteTransactionItem::create($itemData);
                    }

                    $sumAmount += (float)($itemData['amount'] ?? 0);
                    $sumCgst   += (float)($itemData['cgst'] ?? 0);
                    $sumSgst   += (float)($itemData['sgst'] ?? 0);
                    $sumIgst   += (float)($itemData['igst'] ?? 0);
                }

                // delete removed items
                
            } else {

                if (!empty($request->noitem_rows)) {
                    foreach ($request->noitem_rows as $row) {
                        $amount = (float)($row['amount'] ?? 0);
                        $gstRate = (float)($row['gst'] ?? 0);
                        $gstAmount = ($amount * $gstRate) / 100;

                        $sumAmount += $amount;
                        if ($transaction->is_igst == 1) {
                            $sumIgst += $gstAmount;
                        } else {
                            $sumCgst += $gstAmount / 2;
                            $sumSgst += $gstAmount / 2;
                        }
                    }
                } else {
                    $sumAmount = (float)($request->noitem_amount ?? 0);

                    if ($transaction->is_igst == 1) {
                        $sumIgst = (float)($request->igst ?? 0);
                    } else {
                        $sumCgst = (float)($request->cgst ?? 0);
                        $sumSgst = (float)($request->sgst ?? 0);
                    }
                }

                DebitNoteTransactionItem::where('transaction_id', $transaction->id)->delete();
            }

            // ===============================
            // GST LEDGER (STANDARD)
            // ===============================
            if ($gstMode === 'standard') {

                $transaction->igst_id = $request->igst_ledger ?: ($gstMapping['igst_id'] ?? $transaction->igst_id);
                $transaction->cgst_id = $request->cgst_ledger ?: ($gstMapping['cgst_id'] ?? $transaction->cgst_id);
                $transaction->sgst_id = $request->sgst_ledger ?: ($gstMapping['sgst_id'] ?? $transaction->sgst_id);

                $transaction->igst_ledger_name = optional(
                    Ledger::getLedgerById($transaction->iPartyId, $transaction->igst_id)
                )->name;

                $transaction->cgst_ledger_name = optional(
                    Ledger::getLedgerById($transaction->iPartyId, $transaction->cgst_id)
                )->name;

                $transaction->sgst_ledger_name = optional(
                    Ledger::getLedgerById($transaction->iPartyId, $transaction->sgst_id)
                )->name;
            } else {

                $transaction->igst_id = $request->igst_ledger ?: ($gstMapping['igst_id'] ?? $transaction->igst_id);

                $transaction->igst_ledger_name = optional(
                    Ledger::getLedgerById($transaction->iPartyId, $transaction->igst_id)
                )->name;

                $transaction->cgst_id = null;
                $transaction->sgst_id = null;
                $transaction->cgst_ledger_name = null;
                $transaction->sgst_ledger_name = null;
            }

            $transaction->save();

            // ===============================
            // CUSTOM GST (DEBIT)
            // ===============================
            if (!empty($request->custom_slots) && !empty($request->noitem_rows) && ($request->gst_mode ?? 'standard') === 'custom') {
                $this->storeNoItemLedgerWiseCustomGst(
                    $transaction,
                    $request->noitem_rows,
                    $request->custom_slots,
                    $gstMapping,
                    (bool) $transaction->is_igst
                );
            } elseif (!empty($request->custom_slots)) {

                DebitNoteCustomGst::where('transaction_id', $transaction->id)->delete();

                foreach ($request->custom_slots as $slot) {

                    if (
                        ($slot['igst_amount'] ?? 0) != 0 ||
                        ($slot['cgst_amount'] ?? 0) != 0 ||
                        ($slot['sgst_amount'] ?? 0) != 0
                    ) {
                        $slot = $this->applyGstMappingToCustomSlot($slot, $gstMapping);
                        $slotPurchaseLedger = !empty($slot['purchase_ledger_id'])
                            ? Ledger::getLedgerById($transaction->iPartyId, $slot['purchase_ledger_id'])
                            : null;

                        DebitNoteCustomGst::create([
                            'transaction_id' => $transaction->id,

                            'gst_rate' => $slot['rate'] ?? 0,
                            'taxable'  => $slot['taxable'] ?? 0,
                            'ledger_id' => $slotPurchaseLedger?->id,
                            'ledger_name' => $slotPurchaseLedger?->name,

                            'igst_ledger_id'   => $slot['igst_ledger_id'] ?? null,
                            'igst_ledger_name' => optional(
                                Ledger::getLedgerById($transaction->iPartyId, $slot['igst_ledger_id'])
                            )->name,
                            'igst_amount'      => $slot['igst_amount'] ?? 0,

                            'cgst_ledger_id'   => $slot['cgst_ledger_id'] ?? null,
                            'cgst_ledger_name' => optional(
                                Ledger::getLedgerById($transaction->iPartyId, $slot['cgst_ledger_id'])
                            )->name,
                            'cgst_amount'      => $slot['cgst_amount'] ?? 0,

                            'sgst_ledger_id'   => $slot['sgst_ledger_id'] ?? null,
                            'sgst_ledger_name' => optional(
                                Ledger::getLedgerById($transaction->iPartyId, $slot['sgst_ledger_id'])
                            )->name,
                            'sgst_amount'      => $slot['sgst_amount'] ?? 0,
                        ]);
                    }
                }
            }

            // ===============================
            // FINAL TOTAL
            // ===============================
            $roundOffSetting = $this->getRoundOffSetting($transaction->iPartyId);
            $roundOffLedger = $roundOffSetting['ledger'];
            $transaction->update([
                'taxable_amount' => $sumAmount,
                'cgst' => $sumCgst,
                'sgst' => $sumSgst,
                'igst' => $sumIgst,
                'total_amount' => $sumAmount + $sumCgst + $sumSgst + $sumIgst,
                'roundoff_id' => $roundOffLedger?->iLedgerId,
                'roundoff_ledger_name' => $roundOffLedger?->strCustomerName,
                'roundoff' => $this->calculateRoundOffAmount($sumAmount, $sumSgst, $sumCgst, $sumIgst, $roundOffSetting['side']),
                'status' => 'saved'
            ]);

            // ===============================
            // BULK UPDATE
            // ===============================
            $saved = DebitNoteTransaction::where('upload_id', $transaction->upload_id)
                ->where('status', 'saved')
                ->where('is_delete', 0)
                ->count();

            $pending = DebitNoteTransaction::where('upload_id', $transaction->upload_id)
                ->where('status', 'Pending')
                ->where('is_delete', 0)
                ->count();

            $total = DebitNoteTransaction::where('upload_id', $transaction->upload_id)
                ->where('is_delete', 0)
                ->count();

            $status = ($pending == 0) ? 'Completed' : 'Pending';

            BulkDebitNoteUpload::where('id', $transaction->upload_id)->update([
                'total'   => $total,
                'saved'   => $saved,
                'pending' => $pending,
                'status'  => $status
            ]);
        });

        return response()->json([
            'status' => true,
            'message' => 'Debit Note Updated Successfully'
        ]);
    }

    public function show($id)
    {
        $transaction = DebitNoteTransaction::with(['items', 'customGst'])->findOrFail($id);
        $firstItemName = $transaction->items->first()?->item_name;
        $gstMapping = $this->getGstMapping($transaction->iPartyId, $transaction->purchase_ledger, $firstItemName);
        $itemGstMappingsByRate = $transaction->items
            ->mapWithKeys(function ($item) use ($transaction) {
                $gstRate = $item->gst_rate;
                if (!$gstRate && $item->amount > 0) {
                    $gstTotal = ($item->igst > 0) ? $item->igst : ($item->cgst + $item->sgst);
                    if ($gstTotal > 0) {
                        $gstRate = ($gstTotal / $item->amount) * 100;
                    }
                }

                return [
                    (string) round((float) $gstRate, 2) => $this->getGstMapping(
                        $transaction->iPartyId,
                        $transaction->purchase_ledger,
                        $item->item_name
                    )
                ];
            });
        $standardIgstId = $gstMapping['igst_id'] ?? $transaction->igst_id;
        $standardCgstId = $gstMapping['cgst_id'] ?? $transaction->cgst_id;
        $standardSgstId = $gstMapping['sgst_id'] ?? $transaction->sgst_id;
        $noItemRows = $transaction->items->isEmpty()
            ? $transaction->customGst->map(function ($slot) use ($transaction) {
                return [
                    'ledger' => $slot->ledger_id ?? $transaction->purchase_ledger_id,
                    'ledger_name' => $slot->ledger_name ?? $transaction->purchase_ledger_name ?? $transaction->purchase_ledger,
                    'gst' => $slot->gst_rate,
                    'amount' => $slot->amount ?? $slot->taxable,
                ];
            })->values()
            : collect();

        if ($transaction->items->isEmpty() && $noItemRows->isEmpty()) {
            $noItemRows = collect([[
                'ledger' => $transaction->purchase_ledger_id,
                'ledger_name' => $transaction->purchase_ledger_name ?? $transaction->purchase_ledger,
                'gst' => $transaction->gst_rate,
                'amount' => $transaction->taxable_amount,
            ]]);
        }

        return response()->json([
            'id'              => $transaction->id,
            'note_no'         => $transaction->note_no,
            'note_date'       => date('Y-m-d',strtotime($transaction->note_date)),
            'against_invoice' => $transaction->against_invoice,
            'gst_no'          => $transaction->gst_no,
            'party_name'      => $transaction->party_name,
            'place_of_supply' => $transaction->place_of_supply,
            'vch_type'        => $transaction->vch_type,
            // ✅ CLEAN LEDGER FIELD
            'purchase_ledger_id' => $transaction->purchase_ledger_id,
            'purchase_ledger_name' => $transaction->purchase_ledger_name ?? $transaction->purchase_ledger,

            'remarks'         => $transaction->remarks,
            'taxable_amount'  => $transaction->taxable_amount,
            'sgst'            => $transaction->sgst,
            'cgst'            => $transaction->cgst,
            'igst'            => $transaction->igst,
            'total_amount'    => $transaction->total_amount,
            'roundoff'        => $transaction->roundoff ?? 0,
            'roundoff_id'     => $transaction->roundoff_id,
            'roundoff_ledger_name' => $transaction->roundoff_ledger_name,
            'status'          => $transaction->status,

            // ✅ GST
            'gst_mode' => $transaction->gst_mode ?? 'standard',
            'is_igst'  => $transaction->is_igst ?? 0,

            'cgst_id' => $standardCgstId,
            'sgst_id' => $standardSgstId,
            'igst_id' => $standardIgstId,

            'sgst_ledger_name'    => $this->gstLedgerName($transaction->iPartyId, $standardSgstId),
            'cgst_ledger_name'  => $this->gstLedgerName($transaction->iPartyId, $standardCgstId),
            'igst_ledger_name'  => $this->gstLedgerName($transaction->iPartyId, $standardIgstId),
            'gst_mode'  => $transaction->gst_mode,
            'address'    => $transaction->address,
            'pincode'    => $transaction->pincode,
            'city'  => $transaction->city,
            'gst_rate' => $transaction->gst_rate,
            'noitem_rows' => $noItemRows,
            // ✅ CUSTOM GST (optimized)
            'custom_gst' => $transaction->customGst->map(function ($slot) use ($gstMapping, $itemGstMappingsByRate) {
                $slotMapping = $itemGstMappingsByRate->get((string) round((float) $slot->gst_rate, 2), $gstMapping);
                return [
                    'id' => $slot->id,
                    'gst_rate' => $slot->gst_rate,
                    'taxable' => $slot->taxable,
                    'amount' => $slot->amount ?? $slot->taxable,
                    'ledger_id' => $slot->ledger_id,
                    'ledger_name' => $slot->ledger_name,
                    'igst_ledger_id' => $slotMapping['igst_id'] ?? $slot->igst_ledger_id,
                    'igst_ledger_name' => $slotMapping['igst_name'] ?? $slot->igst_ledger_name,
                    'igst_amount' => $slot->igst_amount,
                    'cgst_ledger_id' => $slotMapping['cgst_id'] ?? $slot->cgst_ledger_id,
                    'cgst_ledger_name' => $slotMapping['cgst_name'] ?? $slot->cgst_ledger_name,
                    'cgst_amount' => $slot->cgst_amount,
                    'sgst_ledger_id' => $slotMapping['sgst_id'] ?? $slot->sgst_ledger_id,
                    'sgst_ledger_name' => $slotMapping['sgst_name'] ?? $slot->sgst_ledger_name,
                    'sgst_amount' => $slot->sgst_amount,
                ];
            }),

            'items' => $transaction->items->map(function ($item) {

                $rate = $item->rate;
                if (!$rate && $item->quantity > 0) {
                    $rate = $item->amount / $item->quantity;
                }

                $gstRate = $item->gst_rate;
                if (!$gstRate && $item->amount > 0) {
                    $gstTotal = ($item->igst > 0)
                        ? $item->igst
                        : ($item->cgst + $item->sgst);

                    if ($gstTotal > 0) {
                        $gstRate = ($gstTotal / $item->amount) * 100;
                    }
                }

                return [
                    'id'           => $item->id,
                    'item_name'    => $item->item_name,
                    'hsn_code'     => $item->hsn_code,
                    'quantity'     => $item->quantity,
                    'unit'         => $item->unit,
                    'rate'         => round($rate, 2),
                    'gst_rate'     => round($gstRate, 2),
                    'amount'       => round($item->amount, 2),
                    'sgst'         => round($item->sgst, 2),
                    'cgst'         => round($item->cgst, 2),
                    'igst'         => round($item->igst, 2),
                    'total_amount' => round($item->total_amount, 2),
                ];
            }),
        ]);
    }

    public function save(Request $request)
    {
        $iPartyId = session('iPartyId');

        if (!$iPartyId) {
            return response()->json([
                'status' => false,
                'message' => 'Please select company first'
            ]);
        }

        $uploadId = null;

        foreach ($request->selected as $id) {

            $row = DebitNoteTransaction::find($id);
            if (!$row) continue;

            $uploadId = $row->upload_id;

            // ===============================
            // PURCHASE LEDGER
            // ===============================
            $ledgerName = $request->purchase_ledger_name[$id] ?? null;

            $ledger = $ledgerName
                ? Ledger::getLedgerByName($iPartyId, $ledgerName)
                : null;

            // ===============================
            // UPDATE ROW
            // ===============================
            $row->update([
                'note_no' => $request->note_no[$id] ?? $row->note_no,
                'note_date' => $request->note_date[$id] ?? $row->note_date,

                'party_name' => $request->party_name[$id]
                    ?: ($request->purchase_ledger_name[$id] ?? $row->party_name),

                'place_of_supply' => $request->place_of_supply[$id] ?? $row->place_of_supply,

                'purchase_ledger_id' => $ledger->id ?? $row->purchase_ledger_id,
                'purchase_ledger_name' => $ledger->name ?? $row->purchase_ledger_name,

                'vch_type' => $request->vch_type[$id] ?? 'Debit Note',

                'status' => 'saved'
            ]);
        }

        // ===============================
        // BULK UPDATE
        // ===============================
        if ($uploadId) {

            $saved = DebitNoteTransaction::where('upload_id', $uploadId)
                ->where('status', 'saved')
                ->where('is_delete', 0)
                ->count();

            $pending = DebitNoteTransaction::where('upload_id', $uploadId)
                ->where('status', 'Pending')
                ->where('is_delete', 0)
                ->count();

            $total = DebitNoteTransaction::where('upload_id', $uploadId)
                ->where('is_delete', 0)
                ->count();

            $status = ($pending == 0) ? 'Completed' : 'Pending';

            BulkDebitNoteUpload::where('id', $uploadId)->update([
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

    private function getCellValue($cell)
    {
        return $cell->getCalculatedValue();
    }

    private function toNumber($value)
    {
        return is_numeric($value) ? (float)$value : 0;
    }

    private function parseDate(mixed $value): string
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }
        if (is_numeric($value)) {
            return Date::excelToDateTimeObject((float) $value)->format('Y-m-d');
        }
        return date('Y-m-d', strtotime((string) $value));
    }

    public function changeUploadStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'status' => 'required|string'
        ]);

        $upload = BulkDebitNoteUpload::find($request->id);
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
                $transactions = DebitNoteTransaction::where('upload_id', $id)->pluck('id');
                DebitNoteCustomGst::whereIn('transaction_id', $transactions)->delete();
                DebitNoteTransactionItem::whereIn('transaction_id', $transactions)->delete();
                DebitNoteTransaction::where('upload_id', $id)->delete();
                BulkDebitNoteUpload::where('id', $id)->delete();
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

        $invoiceDate = $request->date;        
        if ($invoiceDate < session('year_from') || $invoiceDate > session('year_to')) {
            return response()->json([
                'status' => false,
                'message' => 'Invoice date must be within selected financial year'
            ]);
        }
        DB::beginTransaction();
        try {
            // ✅ CREATE UPLOAD
            $upload = BulkDebitNoteUpload::where('iPartyId', $iPartyId)
                ->where('type', 'Manual')
                ->first();

            if ($upload) {
                $upload->update([
                    'pending' => $upload->pending + 1,
                    'total'   => $upload->total + 1,
                    'status'    => 'Pending',
                ]);
            } else {
                $upload = BulkDebitNoteUpload::create([
                    'iPartyId'  => $iPartyId,
                    'batch_id'  => Str::uuid(),
                    'file_name' => 'Manual Entry',
                    'file_path' => 'manual',
                    'note_type' => 'debit',
                    'type'      => 'Manual',
                    'status'    => 'Pending',
                    'total'     => 1,
                    'pending'   => 1,
                    'saved'     => 0,
                    'uploaded_by' => auth()->user()->id,
                ]);
            }
            $purchase_ledger = isset($request['purchase_ledger']) && $request['purchase_ledger'] != "Select Ledger" ? $request['purchase_ledger'] : null;
            $purcashe_ledger_id = Ledger::getLedgerByName($iPartyId, $purchase_ledger);
            $gstMapping = $this->getGstMapping(
                $iPartyId,
                $purcashe_ledger_id->name ?? $purchase_ledger,
                $this->firstItemNameFromRequest($request)
            );
            // ✅ CREATE TRANSACTION
            $transaction = DebitNoteTransaction::create([
                'iPartyId'     => $iPartyId,
                'note_type'    => 'debit',
                'upload_id'    => $upload->id,
                'note_no'   => $request->invoice,
                'note_date'         => $request->date ?? now(),
                'against_invoice' => $request->against_invoice,
                'party_name'   => $request->party,
                'gst_no'       => $request->gst,
                'place_of_supply' => $request->place,
                'vch_type'      => $request->voucher_type ?? 'Purchase',
                'status'       => 'pending',
                'source'       => 'manual',
                'gst_mode'     => $request->gst_mode ?? 'standard',
                'remarks'      => $request->remarks,
                'is_igst'      => $request->is_igst,
                'purchase_ledger' => $purcashe_ledger_id->name,
                'address'      => $request->address,
                'pincode'      => $request->pincode,
                'city'         => $request->city,
                // ✅ Ledger store (without item case)
                'purchase_ledger_id'   => $purcashe_ledger_id->id, // $request->sales_ledger_id ?? null,
                'purchase_ledger_name' => $purcashe_ledger_id->name,
                'strYear'       => session('year'),
                'year_from_date' => session('year_from'),
                'year_to_date'  => session('year_to'),
                'isWithItem'    => $request->entry_mode == 'noitem' ? 0 : 1,
                'gst_rate'      => $request->gst_rate ?? 0
            ]);

            $sumAmount = $sumSgst = $sumCgst = $sumIgst = $sumTotal = 0;

            // =====================================================
            // ✅ CASE 1: WITH ITEMS
            // =====================================================
            if (!empty($request->items)) {

                foreach ($request->items as $item) {
                    $itemName = isset($item['item']) ? trim((string) $item['item']) : null;
                    $unit = 'NOS';
                    if(isset($item['unit']) && $item['unit'] <> ""){
                        $unit = $item['unit'];
                    } else {
                        if(isset($item['item']) && $item['item'] <>""){
                            $stockItem = DB::table('StockItemMaster')
                                ->where('iPartyId',$iPartyId)
                                ->where('strItemName',$item['item'])
                                ->first();
                            $unit = $stockItem->strBaseUnits; 
                        }    
                    }
                    DebitNoteTransactionItem::create([
                        'iPartyId'       => $iPartyId,
                        'transaction_id' => $transaction->id,
                        'upload_id'      => $upload->id,
                        'item_name'      => $itemName,
                        'hsn_code'            => $item['hsn'] ?? null,
                        'quantity'       => $item['qty'] ?? 0,
                        'rate'           => $item['rate'] ?? 0,
                        'amount'         => $item['amount'] ?? 0,
                        'sgst'           => $item['sgst'] ?? 0,
                        'cgst'           => $item['cgst'] ?? 0,
                        'igst'           => $item['igst'] ?? 0,
                        'total_amount'   => $item['total_amount'] ?? 0,
                        'unit'           => $unit ?? 'NOS',
                        'gst_rate'       => $item['gst'] ?? 0,
                    ]);

                    $sumAmount += $item['amount'] ?? 0;
                    $sumSgst   += $item['sgst'] ?? 0;
                    $sumCgst   += $item['cgst'] ?? 0;
                    $sumIgst   += $item['igst'] ?? 0;
                    $sumTotal  += $item['total_amount'] ?? 0;
                }
            }

            // =====================================================
            // ✅ CASE 2: WITHOUT ITEMS
            // =====================================================
            else {
                if (!empty($request->noitem_rows)) {
                    foreach ($request->noitem_rows as $row) {
                        $amount = (float)($row['amount'] ?? 0);
                        $gstRate = (float)($row['gst'] ?? 0);
                        $gstAmount = ($amount * $gstRate) / 100;

                        $sumAmount += $amount;
                        if ($request->is_igst == 1) {
                            $sumIgst += $gstAmount;
                        } else {
                            $sumCgst += $gstAmount / 2;
                            $sumSgst += $gstAmount / 2;
                        }
                    }
                } else {
                    $amount = (float)($request->amount ?? 0);
                    $sumAmount = $amount;
                    if ($request->is_igst == 1) {
                        $sumIgst = (float)($request->igst ?? 0);
                    } else {
                        $sumCgst = (float)($request->cgst ?? 0);
                        $sumSgst = (float)($request->sgst ?? 0);
                    }
                }
                $sumTotal = $sumAmount + $sumCgst + $sumSgst + $sumIgst;
            }

            // =====================================================
            // ✅ STANDARD GST
            // =====================================================
            if ($request->gst_mode == 'standard') {
                $transaction->igst_id = $request->igst_ledger ?: ($gstMapping['igst_id'] ?? null);
                $transaction->cgst_id = $request->cgst_ledger ?: ($gstMapping['cgst_id'] ?? null);
                $transaction->sgst_id = $request->sgst_ledger ?: ($gstMapping['sgst_id'] ?? null);

                $transaction->igst_ledger_name = optional(
                    Ledger::getLedgerById($iPartyId, $transaction->igst_id)
                )->name;

                $transaction->cgst_ledger_name = optional(
                    Ledger::getLedgerById($iPartyId, $transaction->cgst_id)
                )->name;

                $transaction->sgst_ledger_name = optional(
                    Ledger::getLedgerById($iPartyId, $transaction->sgst_id)
                )->name;
            }

            // =====================================================
            // ✅ CUSTOM GST
            // =====================================================
            if (!empty($request->custom_slots) && !empty($request->noitem_rows) && ($request->gst_mode ?? 'standard') === 'custom') {
                $this->storeNoItemLedgerWiseCustomGst(
                    $transaction,
                    $request->noitem_rows,
                    $request->custom_slots,
                    $gstMapping,
                    (bool) $request->is_igst
                );
            } elseif (!empty($request->custom_slots)) {
                foreach ($request->custom_slots as $slot) {
                    if (
                        ($slot['igst_amount'] ?? 0) != 0 ||
                        ($slot['cgst_amount'] ?? 0) != 0 ||
                        ($slot['sgst_amount'] ?? 0) != 0
                    ) {
                        $slot = $this->applyGstMappingToCustomSlot($slot, $gstMapping);
                        $slotPurchaseLedger = !empty($slot['purchase_ledger_id']) ? Ledger::getLedgerById($iPartyId, $slot['purchase_ledger_id']) : null;
                        DebitNoteCustomGst::create([
                            'transaction_id' => $transaction->id,
                            'gst_rate'       => $slot['rate'] ?? 0,
                            'taxable'        => $slot['taxable'] ?? 0,
                            'ledger_id'      => $slotPurchaseLedger?->id,
                            'ledger_name'    => $slotPurchaseLedger?->name,

                            'igst_ledger_id'   => $slot['igst_ledger_id'] ?? null,
                            'igst_ledger_name' => optional(Ledger::getLedgerById($iPartyId, $slot['igst_ledger_id']))->name,
                            'igst_amount'      => $slot['igst_amount'] ?? 0,

                            'cgst_ledger_id'   => $slot['cgst_ledger_id'] ?? null,
                            'cgst_ledger_name' => optional(Ledger::getLedgerById($iPartyId, $slot['cgst_ledger_id']))->name,
                            'cgst_amount'      => $slot['cgst_amount'] ?? 0,

                            'sgst_ledger_id'   => $slot['sgst_ledger_id'] ?? null,
                            'sgst_ledger_name' => optional(Ledger::getLedgerById($iPartyId, $slot['sgst_ledger_id']))->name,
                            'sgst_amount'      => $slot['sgst_amount'] ?? 0,
                        ]);
                    }
                }
            }

            // =====================================================
            // ✅ FINAL TOTAL UPDATE
            // =====================================================
            $roundOffSetting = $this->getRoundOffSetting($iPartyId);
            $roundOffLedger = $roundOffSetting['ledger'];
            $transaction->update([
                'taxable_amount'       => $sumAmount,
                'sgst'         => $sumSgst,
                'cgst'         => $sumCgst,
                'igst'         => $sumIgst,
                'total_amount' => $sumAmount + $sumSgst + $sumCgst + $sumIgst,
                'roundoff_id' => $roundOffLedger?->iLedgerId,
                'roundoff_ledger_name' => $roundOffLedger?->strCustomerName,
                'roundoff' => $this->calculateRoundOffAmount($sumAmount, $sumSgst, $sumCgst, $sumIgst, $roundOffSetting['side']),
            ]);

            DB::commit();
            return response()->json([
                'status' => true,
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

