<?php

namespace App\Http\Controllers;

use App\Models\BulkSalesUpload;
use App\Models\SalesTransaction;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Ledger;
use Illuminate\Support\Facades\DB;
use App\Models\Client;
use Illuminate\Support\Facades\Session;
use App\Models\SalesTransactionItem;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use App\Models\SalesCustomGst;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class SalesUploadController extends Controller
{
    // LIST PAGE
    public function index()
    {
        $iPartyId = session('iPartyId');

        $uploads = BulkSalesUpload::where('iPartyId', $iPartyId)
            ->whereIn('status', ['Pending','Processing'])
            ->latest()->get();

        $clients = Client::orderBy('name')->get();
        $years = DB::table('YearMaster')
            ->where('iPartyId', $iPartyId)
            ->orderBy('strYear', 'asc')
            ->get();
        $this->ensureFinancialYearInSession($years);

        $commonData = $this->getCommonData();

        return view('admin.bulkupload.sales.index', array_merge(compact('uploads', 'clients', 'years'), $commonData));
    }

    private function ensureFinancialYearInSession($years): void
    {
        if (!$years || !count($years)) {
            return;
        }

        $selectedYear = session('year');
        $isValidYear = $selectedYear && collect($years)->contains('strYear', $selectedYear);

        if ($isValidYear && session('year_from') && session('year_to')) {
            return;
        }

        if (!$isValidYear) {
            $today = Carbon::today();
            $startYear = $today->month >= 4 ? $today->year : $today->year - 1;
            $selectedYear = sprintf('%d-%04d', $startYear, $startYear + 1);
            $availableYear = collect($years)->firstWhere('strYear', $selectedYear);
            $selectedYear = $availableYear ? $availableYear->strYear : $years[0]->strYear;
        }

        session(['year' => $selectedYear]);

        [$startYear, $endYear] = explode('-', $selectedYear) + [1 => null];
        if (!$endYear) {
            return;
        }

        session([
            'year_from' => $startYear . '-04-01',
            'year_to' => $endYear . '-03-31',
        ]);
    }

    // SELECT COMPANY
    public function selectCompany($id)
    {
        $client = Client::where('id', $id)->first();
        Session::put('iPartyId', $id);
        Session::put('client_name', $client->name);
        Session::put('guid', $client->guid);

        return back();
    }

    private function getCommonData()
    {
        $iPartyId = session('iPartyId');
        $vchTypes = DB::table('VchHistory')
                ->where('iPartyId', $iPartyId)
                ->where('vchType', 'Sales')
                ->distinct()
                ->pluck('vchType');
        $vchTypes = $vchTypes->isEmpty()
            ? collect(['Sales'])
            : $vchTypes;
        return [
            'vchTypes' => $vchTypes,

            'states' => DB::table('state')->pluck('stateName'),

            'groups' => DB::table('GroupMaster')
                ->where('iPartyId', $iPartyId)
                ->distinct()
                ->pluck('strGroupName'),

            'parents' => DB::table('LedgerMaster')
                ->select('strParents')
                ->where('iPartyId', $iPartyId)
                ->where('strParents', 'Sundry Debtors')
                ->distinct()
                ->get(),

            'ledgers' => Ledger::getAllDebtorsLedgers($iPartyId),
            'iGstLedgers' => Ledger::getAlliGstLedgers($iPartyId),
            'cGstLedgers' => Ledger::getAllcGstLedgers($iPartyId),
            'sGstLedgers' => Ledger::getAllsGstLedgers($iPartyId),
            'salesLedgers' => Ledger::getSalesLedgers($iPartyId),
            'salesGstMappings' => $this->getSalesLedgerGstMappings($iPartyId),
            'roundOffSide' => $this->getRoundOffSetting($iPartyId)['side'],
            'stockItems' => DB::table('StockItemMaster')
                ->select(
                    'iStockIdtemId',
                    'strItemName',
                    'strBaseUnits',
                    'CGSTLedgerId',
                    'SGSTLedgerId',
                    'IGSTLedgerId'
                )
                ->where('iPartyId', $iPartyId)
                ->orderBy('strItemName', 'asc')
                ->get(),
        ];
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
    
    private function calculateTotalAmountWithRoundOff($amount, $sgst, $cgst, $igst, ?string $side = 'normal'): float
    {
        $grandTotal = (float) $amount + (float) $sgst + (float) $cgst + (float) $igst;
        $roundOff = $this->calculateRoundOffAmount($amount, $sgst, $cgst, $igst, $side);

        return round($grandTotal + $roundOff, 2);
    }

    public function upload(Request $request)
    {
        $iPartyId = session('iPartyId');
        if (!$iPartyId) {
            return redirect()->route('data_entry_operators.bulkuploadsales')
                ->with('error', 'Please select company first');
        }

        $request->validate([
            'sales_file' => 'required|mimes:xlsx,xls|max:30720'
        ]);

        $file     = $request->file('sales_file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $path     = $file->storeAs('sales_uploads', $fileName, 'public');

        $upload = BulkSalesUpload::create([
            'iPartyId'  => $iPartyId,
            'file_name' => $fileName,
            'file_path' => $path,
            'type'      => 'Item Invoice',
            'status'    => 'Processing',
        ]);

        // ── READ EXCEL WITH FORMULA EVALUATION ────────────────────────────────
        // Using PhpSpreadsheet directly so formulas like =I2*H2 are calculated
        // and we get numeric values, NOT raw formula strings.
        // ──────────────────────────────────────────────────────────────────────
        $spreadsheet = IOFactory::load($file->getRealPath());
        $worksheet   = $spreadsheet->getActiveSheet();

        // Build rows array with calculated values (not raw cell values)
        $sheet = [];
        foreach ($worksheet->getRowIterator() as $rowIndex => $row) {
            $rowData = [];
            foreach ($row->getCellIterator() as $cell) {
                $rowData[] = $this->getCellValue($cell);
            }
            $sheet[] = $rowData;
        }
        // Free memory — spreadsheet can be large
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        // Normalise header
        $header = array_map(fn($h) => strtoupper(trim((string)$h)), $sheet[0]);
        $isItemInvoice       = in_array('NAME OF ITEM', $header);
        $isAccountingInvoice = in_array('PARTICULARS', $header);

        // ── ITEM INVOICE ──────────────────────────────────────────────────────
        if ($isItemInvoice) {
            $invoiceGroups = [];
            foreach ($sheet as $key => $row) {
                if ($key === 0) continue;                 // skip header
                if (empty(array_filter($row))) continue;  // skip blank rows
                if (empty($row[0]) || empty($row[1]) || empty($row[3])) continue;
                $invoiceNo = trim((string) $row[0]);
                $date      = $this->parseDate($row[1]);
                $partyName = trim((string)$row[3]);
                $groupKey = $invoiceNo.'|'. $partyName.'|'. $date;
                $invoiceGroups[$groupKey][] = [
                    'date'            => $date,
                    'gst_no'          => $row[2]  ?? null,
                    'party_name'      => $row[3]  ?? null,
                    'place_of_supply' => $row[4]  ?? null,
                    'sales_ledger'    => $row[5]  ?? null,
                    'item_name'       => $row[6]  ?? null,
                    'quantity'        => $this->toNumber($row[7]),
                    'rate'            => $this->toNumber($row[8]),
                    'amount'          => $this->toNumber($row[9]),
                    'sgst'            => $this->toNumber($row[10]),
                    'cgst'            => $this->toNumber($row[11]),
                    'igst'            => $this->toNumber($row[12]),
                    'total_amount'    => $this->toNumber($row[13]),
                ];
            }
            $totalInvoices = 0;
            DB::transaction(function () use ($invoiceGroups, $iPartyId, $upload, &$totalInvoices) {
                foreach ($invoiceGroups as $groupKey => $items) {
                    $first = $items[0];
                    $invoiceNo = explode('|', $groupKey)[0];
                    // Aggregate totals across all item lines for this invoice
                    $sumAmount      = array_sum(array_column($items, 'amount'));
                    $sumSgst        = array_sum(array_column($items, 'sgst'));
                    $sumCgst        = array_sum(array_column($items, 'cgst'));
                    $sumIgst        = array_sum(array_column($items, 'igst'));
                    $sumTotalAmount = array_sum(array_column($items, 'total_amount'));
                    //$first = $items[0];
                    $salesLedger = DB::table('LedgerMaster')
                        ->where('iPartyId', $iPartyId)
                        ->where('strCustomerName', $first['sales_ledger'])
                        ->first();

                    $mapping = $this->getGstMapping(
                        $iPartyId,
                        $first['sales_ledger']
                    );

                    $status = 'pending';
                    $amountMatched = true;
                    foreach ($items as $item)
                    {
                        $calculatedAmount =
                            round(
                                (float)$item['quantity'] * (float)$item['rate'],
                                2
                            );

                        $excelAmount =
                            round(
                                (float)$item['amount'],
                                2
                            );

                        if ($calculatedAmount != $excelAmount)
                        {
                            $amountMatched = false;
                            break;
                        }
                    }
                    $is_igst = 0;
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
                    $rates = [];

                    foreach ($items as $item)
                    {
                        $gstRate = 0;

                        if($item['amount'] > 0)
                        {
                            $gstRate =
                            (
                                (
                                    $item['cgst']
                                    + $item['sgst']
                                    + $item['igst']
                                ) * 100
                            ) / $item['amount'];
                        }

                        $rates[] = round($gstRate,2);
                    }

                    $rates = array_unique(
                        array_filter($rates)
                    );

                    $gstMode =
                        count($rates) > 1
                            ? 'custom'
                            : 'standard';

                    $gstRate =
                        count($rates)
                            ? reset($rates)
                            : 0;
                    
                    if (!$this->hasOnlyValidGstSlabs($rates) || $this->salesVoucherExists($iPartyId, 'sales', $invoiceNo, session('year'))) {
                        $status = 'pending';
                    }
                    $roundOffSetting = $this->getRoundOffSetting($iPartyId);
                    $roundOffLedger = $roundOffSetting['ledger'];

                    $transaction = SalesTransaction::create([
                        'iPartyId'          => $iPartyId,
                        'upload_id'         => $upload->id,

                        'invoice_no'        => $invoiceNo,
                        'date'              => $first['date'],

                        'gst_no'            => $first['gst_no'],
                        'party_name'        => $first['party_name'],
                        'place_of_supply'   => $first['place_of_supply'],

                        'sales_ledger'      => $first['sales_ledger'],
                        'sales_ledger_id'   => $salesLedger?->iLedgerId,
                        'sales_ledger_name' => $salesLedger?->strCustomerName,

                        'cgst_id'           => $mapping['cgst_id'],
                        'cgst_ledger_name'  => $mapping['cgst_name'],

                        'sgst_id'           => $mapping['sgst_id'],
                        'sgst_ledger_name'  => $mapping['sgst_name'],

                        'igst_id'           => $mapping['igst_id'],
                        'igst_ledger_name'  => $mapping['igst_name'],

                        'gst_mode'          => $gstMode,
                        'gst_rate'          => $gstRate,

                        'isWithItem'        => 1,
                        'strYear'           => session('year'),
                        'year_from_date'    => session('year_from'),
                        'year_to_date'      => session('year_to'),

                        'amount'            => $sumAmount,
                        'sgst'              => $sumSgst,
                        'cgst'              => $sumCgst,
                        'igst'              => $sumIgst,
                        // 'total_amount'      => $sumTotalAmount,
                        'total_amount'      => $this->calculateTotalAmountWithRoundOff($sumAmount, $sumSgst, $sumCgst, $sumIgst, $roundOffSetting['side']),
                        'is_igst'           => $is_igst,
                        'status'            => $status,
                        'vchType'           => 'sales',
                        'roundoff_id'          => $roundOffLedger?->iLedgerId,
                        'roundoff_ledger_name' => $roundOffLedger?->strCustomerName,
                        'roundoff'          => $this->calculateRoundOffAmount($sumAmount, $sumSgst, $sumCgst, $sumIgst, $roundOffSetting['side']),
                    ]);
                    
                    // One parent transaction per invoice
                    foreach ($items as $item) {
                        $itemMapping = $this->getGstMapping(
                            $iPartyId,
                            $first['sales_ledger'],
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
                        SalesTransactionItem::create([
                            'iPartyId'          => $iPartyId,
                            'transaction_id'    => $transaction->id,
                            'upload_id'         => $upload->id,

                            'item_name'         => $item['item_name'],
                            'quantity'          => $item['quantity'],
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
                            'total_amount'      => $item['total_amount'],
                        ]);
                    }   
                    $totalInvoices++;
                }
            });
            $savedCount = SalesTransaction::where(
                    'upload_id',
                    $upload->id
                )
                ->where('status','saved')
                ->count();

            $pendingCount = $totalInvoices - $savedCount;

            $upload->update([
                'total'   => $totalInvoices,
                'saved'   => $savedCount,
                'pending' => $pendingCount,
                'status'  => $pendingCount > 0
                    ? 'Pending'
                    : 'Completed',
            ]);

            // ── ACCOUNTING INVOICE (no items) ─────────────────────────────────────
        } elseif ($isAccountingInvoice) {

            // $total = 0;
            $invoiceGroups = [];

            foreach ($sheet as $key => $row) {
                if ($key === 0) continue;
                if (empty(array_filter($row))) continue;
                if (empty($row[0]) || empty($row[1]) || empty($row[3])) continue;
                
                $invoiceNo = trim((string) $row[0]);
                $date      = $this->parseDate($row[1]);
                $partyName = trim((string)$row[3]);
                $groupKey = $invoiceNo.'|'. $partyName.'|'. $date;
                $invoiceGroups[$groupKey][] = [
                    'invoice_no'      => $invoiceNo,
                    'date'            => $date,
                    'gst_no'          => $row[2] ?? null,
                    'party_name'      => $row[3] ?? null,
                    'place_of_supply' => $row[4] ?? null,
                    'sales_ledger'    => $row[5] ?? null,
                    'amount'          => $this->toNumber($row[6] ?? 0),
                    'sgst'            => $this->toNumber($row[7] ?? 0),
                    'cgst'            => $this->toNumber($row[8] ?? 0),
                    'igst'            => $this->toNumber($row[9] ?? 0),
                    'total_amount'    => $this->toNumber($row[10] ?? 0),
                ];
            }

            $total = 0;

            DB::transaction(function () use ($invoiceGroups, $iPartyId, $upload, &$total) {
                foreach ($invoiceGroups as $groupKey => $rows) {
                    $first = $rows[0];
                    
                    $sumAmount = array_sum(array_column($rows, 'amount'));
                    $sumSgst = array_sum(array_column($rows, 'sgst'));
                    $sumCgst = array_sum(array_column($rows, 'cgst'));
                    $sumIgst = array_sum(array_column($rows, 'igst'));
                    $sumTotalAmount = array_sum(array_column($rows, 'total_amount'));
                    $isIgst = $sumIgst > 0;

                    $salesLedger = DB::table('LedgerMaster')
                        ->where('iPartyId', $iPartyId)
                        ->where('strCustomerName', $first['sales_ledger'])
                        ->first();

                    $gstSlots = $this->buildSalesCustomGstSlots($rows, $iPartyId);
                    $rates = array_unique(array_filter(array_column($gstSlots, 'gst_rate')));
                    $gstMode = count($rates) > 1 ? 'custom' : 'standard';
                    $gstRate = count($rates) ? reset($rates) : 0;
                    $mapping = $this->getGstMapping($iPartyId, $first['sales_ledger']);
                    $hasGstLedgers = $this->hasRequiredGstLedgers(
                        $gstMode === 'custom' ? $gstSlots : [[
                            'cgst_amount' => $sumCgst,
                            'sgst_amount' => $sumSgst,
                            'igst_amount' => $sumIgst,
                            'cgst_ledger_id' => $mapping['cgst_id'],
                            'sgst_ledger_id' => $mapping['sgst_id'],
                            'igst_ledger_id' => $mapping['igst_id'],
                        ]],
                        $isIgst
                    );
                    $status = $hasGstLedgers ? 'saved' : 'pending';
                    if (!$this->hasOnlyValidGstSlabs($rates) || $this->salesVoucherExists($iPartyId, 'sales', $first['invoice_no'], session('year'))) {
                        $status = 'pending';
                    }
                    $roundOffSetting = $this->getRoundOffSetting($iPartyId);
                    $roundOffLedger = $roundOffSetting['ledger'];

                    $transaction = SalesTransaction::create([
                        'iPartyId'          => $iPartyId,
                        'upload_id'         => $upload->id,
                        'invoice_no'        => $first['invoice_no'],
                        'date'              => $this->parseDate($first['date']),
                        'gst_no'            => $first['gst_no'],
                        'party_name'        => $first['party_name'],
                        'place_of_supply'   => $first['place_of_supply'],

                        'sales_ledger'      => $first['sales_ledger'],
                        'sales_ledger_id'   => $salesLedger?->iLedgerId,
                        'sales_ledger_name' => $salesLedger?->strCustomerName,

                        'cgst_id'           => $mapping['cgst_id'],
                        'cgst_ledger_name'  => $mapping['cgst_name'],
                        'sgst_id'           => $mapping['sgst_id'],
                        'sgst_ledger_name'  => $mapping['sgst_name'],
                        'igst_id'           => $mapping['igst_id'],
                        'igst_ledger_name'  => $mapping['igst_name'],

                        'gst_mode'          => $gstMode,
                        'gst_rate'          => $gstRate,
                        'is_igst'           => $isIgst ? 1 : 0,
                        'isWithItem'        => 0,
                        'strYear'           => session('year'),
                        'year_from_date'    => session('year_from'),
                        'year_to_date'      => session('year_to'),

                        'amount'            => $sumAmount,
                        'sgst'              => $sumSgst,
                        'cgst'              => $sumCgst,
                        'igst'              => $sumIgst,
                        // 'total_amount'      => $sumTotalAmount,
                        'total_amount'      => $this->calculateTotalAmountWithRoundOff($sumAmount, $sumSgst, $sumCgst, $sumIgst, $roundOffSetting['side']),
                        'status'            => $status,
                        'vchType'           => 'sales',
                        'roundoff_id'          => $roundOffLedger?->iLedgerId,
                        'roundoff_ledger_name' => $roundOffLedger?->strCustomerName,
                        'roundoff'          => $this->calculateRoundOffAmount($sumAmount, $sumSgst, $sumCgst, $sumIgst, $roundOffSetting['side']),
                    ]);

                    if ($gstMode === 'custom') {
                        foreach ($gstSlots as $slot) {
                            if (($slot['igst_amount'] ?? 0) != 0 || ($slot['cgst_amount'] ?? 0) != 0 || ($slot['sgst_amount'] ?? 0) != 0) {
                                SalesCustomGst::create(array_merge([
                                    'transaction_id' => $transaction->id,
                                ], $slot));
                            }
                        }
                    }
                    // $asis_igst = 0;
                    $total++;
                }
            });
             
            $savedCount = SalesTransaction::where('upload_id',$upload->id)
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
        }
        return back()->with('success', 'Records Added Successfully');
    }

    public function save(Request $request)
    {
        $iPartyId = session('iPartyId');
        if (!$iPartyId) {
            return redirect()->route('data_entry_operators.bulkuploadsales')
                ->with('error', 'Please select company first');
        }
        $uploadId = null;
        foreach ($request->selected as $key => $id) {
            $row = SalesTransaction::find($id);
            if (!$row) continue;

            $uploadId = $row->upload_id;
            $voucherType = $request->voucher_type[$id] ?? $row->vchType;
            $voucherNo = $request->invoice_no[$id] ?? $row->invoice_no;
            if ($this->salesVoucherExists($row->iPartyId, $voucherType, $voucherNo, $row->strYear ?? session('year'), $row->id)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Duplicate voucher found for the selected VnchType, VnchNo, and Year.'
                ], 422);
            }
            $row->update([
                'invoice_no' => $request->invoice_no[$id],
                'date' => $request->date[$id],
                'party_name' => $request->party_name[$id] ?: ($request->ledger[$id] ?? null),
                'place_of_supply' => $request->place_of_supply[$id],
                'status' => 'saved',
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

            $status = ($pending == 0) ? 'Completed' : 'Pending';

            BulkSalesUpload::where('id', $uploadId)->update([
                'total' => $total,
                'saved' => $saved,
                'pending' => $pending,
                'status'  => $status
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Saved Successfully'
        ]);
        // return back()->with('success', 'Records Updated');
    }

    // PREVIEW PAGE
    public function preview($id)
    {
        $iPartyId = session('iPartyId');
        if (!$iPartyId) {
            return redirect()->route('data_entry_operators.bulkuploadsales')
                ->with('error', 'Please select company first');
        }
        $rows = SalesTransaction::where('upload_id', $id)
            ->where('status', 'pending')
            ->where('iPartyId', $iPartyId)
            ->get();

        $commonData = $this->getCommonData();
        $commonData['iGstLedgers'] = Ledger::mergeLedgersByIds(
            $iPartyId,
            $commonData['iGstLedgers'],
            $rows->pluck('igst_id')->all()
        );
        $commonData['cGstLedgers'] = Ledger::mergeLedgersByIds(
            $iPartyId,
            $commonData['cGstLedgers'],
            $rows->pluck('cgst_id')->all()
        );
        $commonData['sGstLedgers'] = Ledger::mergeLedgersByIds(
            $iPartyId,
            $commonData['sGstLedgers'],
            $rows->pluck('sgst_id')->all()
        );

        return view('admin.bulkupload.sales.preview', array_merge(compact('rows'), $commonData));
    }

    public function storeLedger(Request $request)
    {
        $iPartyId = session('iPartyId');
        if (!$iPartyId) {
            return redirect()->route('data_entry_operators.bulkuploadsales')
                ->with('error', 'Please select company first');
        }
        $iPartyId = session('iPartyId');
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

    public function delete($id)
    {
        $iPartyId = session('iPartyId');

        if (!$iPartyId) {
            return response()->json([
                'status' => false,
                'message' => 'Please select company first'
            ]);
        }

        $row = SalesTransaction::find($id);

        if (!$row) {
            return response()->json([
                'status' => false,
                'message' => 'Record not found'
            ]);
        }

        $uploadId = $row->upload_id;

        // delete row first
        $row->delete();

        // recalculate counts
        $saved = SalesTransaction::where('upload_id', $uploadId)
            ->where('status', 'saved')
            ->count();

        $pending = SalesTransaction::where('upload_id', $uploadId)
            ->where('status', 'pending')
            ->count();

        $total = SalesTransaction::where('upload_id', $uploadId)->count();

        BulkSalesUpload::where('id', $uploadId)->update([
            'total' => $total,
            'saved' => $saved,
            'pending' => $pending
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Record deleted successfully'
        ]);
    }

    // ── SHOW (used by both View and Edit modals via AJAX) ─────────────────────────
    public function show($id)
    {
        // $transaction = SalesTransaction::with(['items','customGst'])->findOrFail($id);
        $iPartyId = session('iPartyId');
        $transaction = SalesTransaction::with([
            'items' => function ($query) use ($iPartyId) {
                if ($iPartyId) {
                    $query->where('iPartyId', $iPartyId);
                }
            },
            'customGst',
        ])
            ->when($iPartyId, fn ($query) => $query->where('iPartyId', $iPartyId))
            ->findOrFail($id);
        $gstMapping = $this->getGstMapping($transaction->iPartyId, $transaction->sales_ledger);
        return response()->json([
            'id'              => $transaction->id,
            'invoice_no'      => $transaction->invoice_no,
            'date'            => date('Y-m-d', strtotime($transaction->date)),
            'gst_no'          => $transaction->gst_no,
            'vchType'         => $transaction->vchType,
            'party_name'      => $transaction->party_name,
            'place_of_supply' => $transaction->place_of_supply,
            'sales_ledger' => $transaction->sales_ledger,
            'amount'          => $transaction->amount,
            'sgst'            => $transaction->sgst,
            'cgst'            => $transaction->cgst,
            'igst'            => $transaction->igst,
            'total_amount'    => $transaction->total_amount,
            'roundoff'        => $transaction->roundoff ?? 0,
            'roundoff_id'     => $transaction->roundoff_id,
            'roundoff_ledger_name' => $transaction->roundoff_ledger_name,
            'is_igst'         => $transaction->is_igst,
            'status'          => $transaction->status,
            'igst_ledger_name'=> $transaction->igst_ledger_name,
            'cgst_ledger_name'=> $transaction->cgst_ledger_name,
            'sgst_ledger_name'=> $transaction->sgst_ledger_name,
            'igst_id' => $transaction->igst_id ?: ($gstMapping['igst_id'] ?? null),
            'cgst_id' => $transaction->cgst_id ?: ($gstMapping['cgst_id'] ?? null),
            'sgst_id' => $transaction->sgst_id ?: ($gstMapping['sgst_id'] ?? null),
            'gst_mode'  => $transaction->gst_mode,
            'gst_rate'  => $transaction->gst_rate ?: $this->calculateGstRate($transaction),
            'isWithItem' => (int) $transaction->isWithItem,

            'address' => $transaction->address,
            'pincode' => $transaction->pincode,
            'city'  => $transaction->city,
            'Remarks'  => $transaction->Remarks,

            'custom_gst' => $transaction->customGst->map(function ($slot) use ($gstMapping) {
                return [
                    'gst_rate' => $slot->gst_rate,
                    'taxable' => $slot->taxable,
                    'ledger_id' => $slot->ledger_id,
                    'ledger_name' => $slot->ledger_name,
                    'amount' => $slot->amount,
                    'igst_ledger_id' => $slot->igst_ledger_id ?: ($gstMapping['igst_id'] ?? null),
                    'igst_amount' => $slot->igst_amount,
                    'cgst_ledger_id' => $slot->cgst_ledger_id ?: ($gstMapping['cgst_id'] ?? null),
                    'cgst_amount' => $slot->cgst_amount,
                    'sgst_ledger_id' => $slot->sgst_ledger_id ?: ($gstMapping['sgst_id'] ?? null),
                    'sgst_amount' => $slot->sgst_amount,
                ];
            }),
            'items' => $transaction->items->map(function ($item) {
                // 🔥 Derive rate if missing (Excel case)
                $rate = $item->rate;
                if (!$rate && $item->quantity > 0) {
                    $rate = $item->amount / $item->quantity;
                }
                // 🔥 Optional: derive GST rate also (if missing)
                $gstRate = $item->gst_rate;
                if (!$gstRate && $item->amount > 0) {
                    $gstTotal = (isset($item->igst) && $item->igst > 0)  ? $item->igst : ($item->cgst + $item->sgst);
                    if ($gstTotal > 0) {
                        $gstRate = ($gstTotal / $item->amount) * 100;
                    }
                }
                return [
                    'id'           => $item->id,
                    'item_name'    => $item->item_name,
                    'hsn'          => $item->hsn,
                    'quantity'     => $item->quantity,
                    'unit'         => $item->unit,
                    'rate'         => round($rate, 2),      // ✅ always available
                    'gst_rate'     => round($gstRate, 2),   // ✅ auto derive if missing
                    'amount'       => $item->amount,
                    'sgst'         => $item->sgst,
                    'cgst'         => $item->cgst,
                    'igst'         => $item->igst,
                    'total_amount' => $item->total_amount,
                ];
            }),
        ]);
    }

    private function calculateGstRate($transaction)
    {
        if ($transaction->amount <= 0) {
            return 0;
        }

        if ($transaction->igst > 0) {
            return round(($transaction->igst / $transaction->amount) * 100, 2);
        } elseif (($transaction->cgst + $transaction->sgst) > 0) {
            return round((($transaction->cgst + $transaction->sgst) / $transaction->amount) * 100, 2);
        }

        return 0;
    }

    private function storeNoItemLedgerWiseCustomGst(SalesTransaction $transaction, array $noitemRows, array $customSlots, array $gstMapping, bool $isIgst): void
    {
        SalesCustomGst::where('transaction_id', $transaction->id)->delete();

        $slotMap = collect($customSlots)
            ->map(function ($slot) {
                $slot['rate'] = (float) ($slot['rate'] ?? 0);
                $slot['sales_ledger_id'] = $slot['sales_ledger_id'] ?? null;
                return $slot;
            })
            ->groupBy(fn ($slot) => sprintf('%s|%s', $slot['rate'], $slot['sales_ledger_id'] ?? ''));

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
                $slotMap[$slotKey] = $slotMap[$slotKey];
            } elseif ($slotMap->has(sprintf('%s|', $gstRate)) && $slotMap[sprintf('%s|', $gstRate)]->isNotEmpty()) {
                $slot = $slotMap[sprintf('%s|', $gstRate)]->shift();
                $slotMap[sprintf('%s|', $gstRate)] = $slotMap[sprintf('%s|', $gstRate)];
            }

            $slot = $slot ? $this->applyGstMappingToCustomSlot($slot, $gstMapping) : null;

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
            }

            $salesLedgerRow = !empty($row['ledger']) ? Ledger::getLedgerById($transaction->iPartyId, $row['ledger']) : null;
            $igstLedger = $slot && !empty($slot['igst_ledger_id']) ? Ledger::getLedgerById($transaction->iPartyId, $slot['igst_ledger_id']) : null;
            $cgstLedger = $slot && !empty($slot['cgst_ledger_id']) ? Ledger::getLedgerById($transaction->iPartyId, $slot['cgst_ledger_id']) : null;
            $sgstLedger = $slot && !empty($slot['sgst_ledger_id']) ? Ledger::getLedgerById($transaction->iPartyId, $slot['sgst_ledger_id']) : null;

            SalesCustomGst::create([
                'transaction_id' => $transaction->id,
                'gst_rate' => $gstRate,
                'taxable' => $rowAmount,
                'ledger_id' => $salesLedgerRow?->id ?? ($transaction->sales_ledger_id ?? null),
                'ledger_name' => $salesLedgerRow?->name ?? ($transaction->sales_ledger_name ?? null),
                'amount' => $rowAmount,
                'igst_ledger_id' => $slot['igst_ledger_id'] ?? null,
                'igst_ledger_name' => $igstLedger?->name ?? null,
                'igst_amount' => $igstAmount,
                'cgst_ledger_id' => $slot['cgst_ledger_id'] ?? null,
                'cgst_ledger_name' => $cgstLedger?->name ?? null,
                'cgst_amount' => $cgstAmount,
                'sgst_ledger_id' => $slot['sgst_ledger_id'] ?? null,
                'sgst_ledger_name' => $sgstLedger?->name ?? null,
                'sgst_amount' => $sgstAmount,
            ]);
        }
    }

    // ── UPDATE (called by Edit modal save) ────────────────────────────────────────
    private function storeWithItemCustomGst(SalesTransaction $transaction, array $customSlots, array $gstMapping): void
    {
        SalesCustomGst::where('transaction_id', $transaction->id)->delete();

        foreach ($customSlots as $slot) {
            $igstAmount = (float) ($slot['igst_amount'] ?? 0);
            $cgstAmount = (float) ($slot['cgst_amount'] ?? 0);
            $sgstAmount = (float) ($slot['sgst_amount'] ?? 0);

            if ($igstAmount == 0 && $cgstAmount == 0 && $sgstAmount == 0) {
                continue;
            }

            $slot = $this->applyGstMappingToCustomSlot($slot, $gstMapping);
            $taxable = (float) ($slot['taxable'] ?? 0);
            $salesLedger = !empty($slot['sales_ledger_id']) && is_numeric($slot['sales_ledger_id'])
                ? Ledger::getLedgerById($transaction->iPartyId, $slot['sales_ledger_id'])
                : null;
            $igstLedger = !empty($slot['igst_ledger_id']) ? Ledger::getLedgerById($transaction->iPartyId, $slot['igst_ledger_id']) : null;
            $cgstLedger = !empty($slot['cgst_ledger_id']) ? Ledger::getLedgerById($transaction->iPartyId, $slot['cgst_ledger_id']) : null;
            $sgstLedger = !empty($slot['sgst_ledger_id']) ? Ledger::getLedgerById($transaction->iPartyId, $slot['sgst_ledger_id']) : null;

            SalesCustomGst::create([
                'transaction_id' => $transaction->id,
                'gst_rate' => $slot['rate'] ?? 0,
                'taxable' => $taxable,
                'ledger_id' => $salesLedger?->id ?? ($transaction->sales_ledger_id ?? null),
                'ledger_name' => $salesLedger?->name ?? ($transaction->sales_ledger_name ?? null),
                'amount' => $taxable,
                'igst_ledger_id' => $slot['igst_ledger_id'] ?? null,
                'igst_ledger_name' => $igstLedger?->name ?? null,
                'igst_amount' => $igstAmount,
                'cgst_ledger_id' => $slot['cgst_ledger_id'] ?? null,
                'cgst_ledger_name' => $cgstLedger?->name ?? null,
                'cgst_amount' => $cgstAmount,
                'sgst_ledger_id' => $slot['sgst_ledger_id'] ?? null,
                'sgst_ledger_name' => $sgstLedger?->name ?? null,
                'sgst_amount' => $sgstAmount,
            ]);
        }
    }

    public function update(Request $request)
    {
        // dd($request);
        \Log::info('SalesUploadController update called', [
            'request_data' => $request->all(),
            'items_count' => count($request->items ?? [])
        ]);
        $data = $request->validate([
            'id'              => 'required|integer',
            'invoice_no'      => 'nullable|string',
            'date'            => 'nullable|date',
            'party_name'      => 'nullable|string',
            'gst_no'          => 'nullable|string',
            'place_of_supply' => 'nullable|string',
            // 'sales_ledger' => 'nullable|string',
            'vchType'         => 'nullable|string',
            'address'         => 'nullable|string',
            'pincode'         => 'nullable|numeric',
            'city'            => 'nullable|string',
            'is_igst'         => 'nullable|numeric',
            'Remarks'         => 'nullable|string',
            'gst_rate'        => 'nullable|numeric',

            'items'           => 'nullable|array',
            'items.*.id'          => 'nullable|integer',
            'items.*.item_name'   => 'nullable|string',
            'items.*.hsn'         => 'nullable|string',
            'items.*.gst_rate'    => 'nullable|numeric',
            'items.*.quantity'    => 'nullable|numeric',
            'items.*.unit'        => 'nullable|string',
            'items.*.rate'        => 'nullable|numeric',
            'items.*.amount'      => 'nullable|numeric',
            'items.*.sgst'        => 'nullable|numeric',
            'items.*.cgst'        => 'nullable|numeric',
            'items.*.igst'        => 'nullable|numeric',
            'items.*.total_amount' => 'nullable|numeric',

            'custom_slots' => 'nullable|array',
            'custom_slots.*.rate' => 'nullable|numeric',
            'custom_slots.*.taxable' => 'nullable|numeric',
            'custom_slots.*.igst_amount' => 'nullable|numeric',
            'custom_slots.*.cgst_amount' => 'nullable|numeric',
            'custom_slots.*.sgst_amount' => 'nullable|numeric',
            'custom_slots.*.igst_ledger_id' => 'nullable|integer',
            'custom_slots.*.cgst_ledger_id' => 'nullable|integer',
            'custom_slots.*.sgst_ledger_id' => 'nullable|integer',
            'custom_slots.*.sales_ledger_id' => 'nullable|integer',
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

        try {
            DB::transaction(function () use ($data, $request) {

            $transaction = SalesTransaction::findOrFail($data['id']);
            $voucherType = $request['vchType'] ?? $transaction->vchType;
            $voucherNo = $request['invoice_no'] ?? $transaction->invoice_no;
            if ($this->salesVoucherExists($transaction->iPartyId, $voucherType, $voucherNo, session('year'), $transaction->id)) {
                throw new \InvalidArgumentException('Duplicate voucher found for the selected VnchType, VnchNo, and Year.');
            }

            // ===============================
            // HEADER UPDATE
            // ===============================
            $sales_ledger = isset($request['sales_ledger_name']) && $request['sales_ledger_name'] != "Select Ledger" ? $request['sales_ledger_name'] : $transaction->sales_ledger;
            $sales_ledger_id = $sales_ledger ? Ledger::getLedgerByName($transaction->iPartyId, $sales_ledger) : null;
            $gstMapping = $this->getGstMapping($transaction->iPartyId, $sales_ledger_id->name ?? $sales_ledger);
            $transaction->update([
                'invoice_no'      => $request['invoice_no'] ?? $transaction->invoice_no,
                'date'            => $request['date'],
                'party_name'      => $request['party_name'],
                'gst_no'          => $request['gst_no'],
                'place_of_supply' => $request['place_of_supply'],
                //'sales_ledger'    => $request['sales_ledger_name'] ?? $transaction->sales_ledger_name,
                'sales_ledger'    => isset($request['sales_ledger_name']) && $request['sales_ledger_name'] != "Select Ledger" ? $request['sales_ledger_name'] : $transaction->sales_ledger,
                'vchType'         => $request['vchType'],
                'address'         => $request['address'],
                'pincode'         => $request['pincode'],
                'city'            => $request['city'],
                'is_igst'         => $request['is_igst'] ?? 0,
                'Remarks'         => $request['Remarks'],
                'gst_rate'        => $request['gst_rate'] ?? 0,

                // ✅ NEW
                'gst_mode'        => $request->gst_mode ?? 'standard',

                // ✅ Ledger store (without item case)
                'sales_ledger_id'   => $sales_ledger_id?->id, // $request->sales_ledger_id ?? null,
                'sales_ledger_name' => $sales_ledger_id?->name,

                'strYear'       => session('year'),
                'year_from_date' => session('year_from'),
                'year_to_date'  => session('year_to'),
                'isWithItem'    => $request->entry_mode == 'noitem' ? 0 : 1,
                'gst_rate'      => $request->gst_rate ?? 0,
                'against_invoice' => $request->against_invoice
            ]);

            $gstMode = $request->gst_mode ?? 'standard';

            // ===============================
            // ITEMS HANDLING
            // ===============================
            $submittedIds = [];
            $sumAmount = $sumSgst = $sumCgst = $sumIgst = $sumTotal = 0;

            // =========================================================
            // ✅ CASE 1: WITH ITEMS
            // =========================================================
            if (!empty($data['items'])) {

                foreach ($data['items'] as $index => $itemData) {

                    \Log::info("Processing item {$index}", [
                        'itemData' => $itemData,
                        'itemId' => $itemData['id'] ?? null
                    ]);

                    $itemId = $itemData['id'] ?? null;

                    // Prepare data for item (remove id for updates)
                    $itemDataToSave = $itemData;
                    unset($itemDataToSave['id']);
                    $itemDataToSave['iPartyId'] = $transaction->iPartyId;
                    $itemDataToSave['transaction_id'] = $transaction->id;
                    $itemDataToSave['upload_id'] = $transaction->upload_id;

                    \Log::info("Item data to save", ['data' => $itemDataToSave]);

                    if ($itemId) {
                        $item = SalesTransactionItem::find($itemId);

                        if ($item && $item->transaction_id == $transaction->id) {
                            \Log::info("Updating existing item {$itemId}");
                            $result = $item->update($itemDataToSave);
                            \Log::info("Update result", ['result' => $result, 'item_id' => $item->id]);
                            $submittedIds[] = $itemId;
                        } else {
                            \Log::info("Item {$itemId} not found or doesn't belong to transaction, setting itemId to null");
                            $itemId = null;
                        }
                    }

                    if (!$itemId) {
                        \Log::info("Creating new item");
                        $item = SalesTransactionItem::create($itemDataToSave);
                        \Log::info("Created item", ['new_item_id' => $item->id]);
                        $submittedIds[] = $item->id;
                    }

                    $sumAmount += (float)($itemData['amount'] ?? 0);
                    $sumSgst   += (float)($itemData['sgst'] ?? 0);
                    $sumCgst   += (float)($itemData['cgst'] ?? 0);
                    $sumIgst   += (float)($itemData['igst'] ?? 0);
                    $sumTotal  += (float)($itemData['total_amount'] ?? 0);
                }

                // delete removed
                SalesTransactionItem::where('transaction_id', $transaction->id)
                    ->whereNotIn('id', $submittedIds)
                    ->delete();
            }
            // =========================================================
            // ✅ CASE 2: WITHOUT ITEMS
            // =========================================================
            else {

                if (!empty($request->noitem_rows)) {
                    $sumAmount = 0;
                    $sumCgst = 0;
                    $sumSgst = 0;
                    $sumIgst = 0;

                    foreach ($request->noitem_rows as $row) {
                        $rowAmount = (float)($row['amount'] ?? 0);
                        $rowGstRate = (float)($row['gst'] ?? 0);
                        $rowGstAmount = ($rowAmount * $rowGstRate) / 100;

                        $sumAmount += $rowAmount;

                        if (($request->is_igst ?? 0) == 1) {
                            $sumIgst += $rowGstAmount;
                        } else {
                            $sumCgst += $rowGstAmount / 2;
                            $sumSgst += $rowGstAmount / 2;
                        }
                    }
                } else {
                    $amount = (float)($request->noitem_amount ?? 0);

                    $sumAmount = $amount;

                    if (($request->is_igst ?? $transaction->is_igst) == 1) {
                        $sumIgst = (float)($request->igst ?? 0);
                    } else {
                        $sumCgst = (float)($request->cgst ?? 0);
                        $sumSgst = (float)($request->sgst ?? 0);
                    }
                }

                $sumTotal = $sumAmount + $sumCgst + $sumSgst + $sumIgst;

                // delete all items
                SalesTransactionItem::where('transaction_id', $transaction->id)->delete();
            }

            // =========================================================
            // ✅ STANDARD GST LEDGER SAVE
            // =========================================================
            if ($gstMode === 'standard') {

                $transaction->igst_id = $this->resolveGstLedgerId($transaction->iPartyId, $request->igst_ledger, $gstMapping, 'igst_id');
                $transaction->cgst_id = $this->resolveGstLedgerId($transaction->iPartyId, $request->cgst_ledger, $gstMapping, 'cgst_id');
                $transaction->sgst_id = $this->resolveGstLedgerId($transaction->iPartyId, $request->sgst_ledger, $gstMapping, 'sgst_id');

                // $igst_ledger = $request->igst_ledger ? Ledger::getLedgerById($transaction->iPartyId, $request->igst_ledger) : null;
                // $cgst_ledger = $request->cgst_ledger ? Ledger::getLedgerById($transaction->iPartyId, $request->cgst_ledger) : null;
                // $sgst_ledger = $request->sgst_ledger ? Ledger::getLedgerById($transaction->iPartyId, $request->sgst_ledger) : null;
                // store name also
                $transaction->igst_ledger_name = $this->gstLedgerName($transaction->iPartyId, $transaction->igst_id);
                $transaction->cgst_ledger_name = $this->gstLedgerName($transaction->iPartyId, $transaction->cgst_id);
                $transaction->sgst_ledger_name = $this->gstLedgerName($transaction->iPartyId, $transaction->sgst_id);
            } else {
                // $transaction->igst_id = $request->igst_ledger;
                // $igst_ledger = $request->igst_ledger ? Ledger::getLedgerById($transaction->iPartyId, $request->igst_ledger) : null;
                // $transaction->igst_ledger_name = $igst_ledger?->name ?? null;
                $transaction->igst_id = $this->resolveGstLedgerId($transaction->iPartyId, $request->igst_ledger, $gstMapping, 'igst_id');
                $transaction->igst_ledger_name = $this->gstLedgerName($transaction->iPartyId, $transaction->igst_id);
            }

            // =========================================================
            // ✅ CUSTOM GST
            // =========================================================
            if ($gstMode === 'standard') {
                SalesCustomGst::where('transaction_id', $transaction->id)->delete();
            } elseif (!empty($request->custom_slots) && !empty($request->noitem_rows)) {
                $this->storeNoItemLedgerWiseCustomGst(
                    $transaction,
                    $request->noitem_rows,
                    $request->custom_slots,
                    $gstMapping,
                    (bool) ($request->is_igst ?? 0)
                );
            } elseif (!empty($request->custom_slots)) {
                $this->storeWithItemCustomGst($transaction, $request->custom_slots, $gstMapping);
            }

            // =========================================================
            // FINAL TOTAL UPDATE
            // =========================================================
            $roundOffSetting = $this->getRoundOffSetting($transaction->iPartyId);
            $roundOffLedger = $roundOffSetting['ledger'];
            
            $updateData = [
                'amount'       => $sumAmount,
                'sgst'         => $sumSgst,
                'cgst'         => $sumCgst,
                'igst'         => $sumIgst,
                // 'total_amount' => $sumAmount + $sumSgst + $sumCgst + $sumIgst,
                'total_amount' => $this->calculateTotalAmountWithRoundOff($sumAmount, $sumSgst, $sumCgst, $sumIgst, $roundOffSetting['side']),
                'roundoff_id'  => $roundOffLedger?->iLedgerId,
                'roundoff_ledger_name' => $roundOffLedger?->strCustomerName,
                'roundoff'     => $this->calculateRoundOffAmount($sumAmount, $sumSgst, $sumCgst, $sumIgst, $roundOffSetting['side']),
                'status'       => $this->hasOnlyValidGstSlabs($this->extractSalesRequestGstRates($request, $sumAmount, $sumCgst, $sumSgst, $sumIgst)) ? 'saved' : 'pending',
            ];

            \Log::info("Final transaction update", ['data' => $updateData]);

            $transaction->update($updateData);
            if ($transaction) {
                $saved = SalesTransaction::where('upload_id', $transaction->upload_id)
                    ->where('status', 'saved')
                    ->count();
                $pending = SalesTransaction::where('upload_id', $transaction->upload_id)
                    ->where('status', 'pending')
                    ->count();
                $total = SalesTransaction::where('upload_id', $transaction->upload_id)
                    ->count();

                $status = ($pending == 0) ? 'Completed' : 'Pending';

                BulkSalesUpload::where('id', $transaction->upload_id)->update([
                    'total' => $total,
                    'saved' => $saved,
                    'pending' => $pending,
                    'status'  => $status
                ]);
            }
        });
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 422);
        }
        return response()->json(['status' => true,'message' => 'Updated Successfully']);
    }

    private function salesVoucherExists($partyId, ?string $vchType, ?string $vchNo, ?string $year, ?int $ignoreId = null): bool
    {
        if (blank($vchType) || blank($vchNo) || blank($year)) {
            return false;
        }

        $transactionExists = SalesTransaction::where('iPartyId', $partyId)
            ->whereRaw('LOWER(TRIM(vchType)) = ?', [strtolower(trim($vchType))])
            ->whereRaw('LOWER(TRIM(invoice_no)) = ?', [strtolower(trim($vchNo))])
            ->whereRaw('LOWER(TRIM(strYear)) = ?', [strtolower(trim($year))])
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists();

        if ($transactionExists) {
            return true;
        }

        $yearId = DB::table('YearMaster')
            ->where('iPartyId', $partyId)
            ->where('strYear', $year)
            ->value('iYearId');

        return DB::table('VchHistory')
            ->where('iPartyId', $partyId)
            ->whereRaw('LOWER(TRIM(vchType)) = ?', [strtolower(trim($vchType))])
            ->whereRaw('LOWER(TRIM(vchNo)) = ?', [strtolower(trim($vchNo))])
            ->when($yearId, fn ($query) => $query->where('iYearId', $yearId))
            ->exists();
    }

    private function isGstRateWithinDefinedSlabs($rate): bool
    {
        
        $rate = round((float) $rate, 2);
        if ($rate <= 0) {
            return true;
        }
        return in_array($rate, [0.0,0.05, 0.1, 0.125, 0.25, 0.5, 1.0, 1.5, 2.5, 3.0, 5.0, 6.0, 7.5, 9.0, 12.0, 14.0, 18.0, 28.0], true);
    }

    private function hasOnlyValidGstSlabs(array $rates): bool
    {
        foreach ($rates as $rate) {
            if (!$this->isGstRateWithinDefinedSlabs($rate)) {
                return false;
            }
        }

        return true;
    }

    private function extractSalesRequestGstRates(Request $request, float $sumAmount = 0, float $sumCgst = 0, float $sumSgst = 0, float $sumIgst = 0): array
    {
        $lineRates = [];
        foreach ((array) $request->input('items', []) as $item) {
            $lineRates[] = $item['gst_rate'] ?? null;
        }
        foreach ((array) $request->input('custom_slots', []) as $slot) {
            $lineRates[] = $slot['rate'] ?? null;
        }
        foreach ((array) $request->input('noitem_rows', []) as $row) {
            $lineRates[] = $row['gst'] ?? null;
        }
        // $rates[] = $request->input('gst_rate');
        // if ($sumAmount > 0) {
        //     $rates[] = (($sumCgst + $sumSgst + $sumIgst) * 100) / $sumAmount;
        // }

        // $rates = array_filter($rates, fn ($rate) => $rate !== null && $rate !== '' && (float) $rate > 0);

        // if (empty($rates) && $sumAmount > 0) {
        //     $rates[] = (($sumCgst + $sumSgst + $sumIgst) * 100) / $sumAmount;
        // }
        $lineRates = array_values(array_filter($lineRates, fn ($rate) => $rate !== null && $rate !== '' && (float) $rate > 0));
        if (!empty($lineRates)) {
            return $lineRates;
        }
        // return array_filter($rates, fn ($rate) => $rate !== null && $rate !== '' && (float) $rate > 0);
        // return $rates;
        $headerRate = $request->input('gst_rate');
        return ($headerRate !== null && $headerRate !== '' && (float) $headerRate > 0) ? [$headerRate] : [];
    }

    private function getCellValue(Cell $cell): mixed
    {
        try {
            // getCalculatedValue() evaluates formulas like =I2*H2
            return $cell->getCalculatedValue();
        } catch (\Exception $e) {
            // If formula evaluation fails, fall back to raw/cached value
            return $cell->getValue();
        }
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

    private function toNumber(mixed $value): float
    {
        if ($value === null || $value === '') {
            return 0;
        }
        // Remove commas (e.g. "1,000") and cast
        $clean = str_replace(',', '', (string) $value);
        return is_numeric($clean) ? (float) $clean : 0;
    }

    public function changeStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'status' => 'required|string'
        ]);
        
        $upload = BulkSalesUpload::find($request->id);

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
                $transactions = SalesTransaction::where('upload_id', $id)->pluck('id');
                SalesCustomGst::whereIn('transaction_id', $transactions)->delete();
                SalesTransactionItem::whereIn('transaction_id', $transactions)->delete();
                SalesTransaction::where('upload_id', $id)->delete();
                BulkSalesUpload::where('id', $id)->delete();
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
        // dd(session('year_from') . " - " . session('year_to'));
        if ($invoiceDate < session('year_from') || $invoiceDate > session('year_to')) {
            return response()->json([
                'status' => false,
                'message' => 'Invoice date must be within selected financial year'
            ]);
        }

        if ($this->salesVoucherExists($iPartyId, $request->voucher_type ?? 'Sales', $request->invoice, session('year'))) {
            return response()->json([
                'status' => false,
                'message' => 'Duplicate voucher found for the selected VnchType, VnchNo, and Year.'
            ], 422);
        }

        DB::beginTransaction();
        // try {
            // ✅ CREATE UPLOAD
            $upload = BulkSalesUpload::where('iPartyId', $iPartyId)
                ->where('type', 'Manual')
                ->first();

            if ($upload) {
                $upload->update([
                    'pending' => $upload->pending + 1,
                    'total'   => $upload->total + 1,
                    'status'    => 'Pending',
                ]);
            } else {
                $upload = BulkSalesUpload::create([
                    'iPartyId'  => $iPartyId,
                    'file_name' => 'Manual Entry',
                    'file_path' => 'manual',
                    'type'      => 'Manual',
                    'status'    => 'Pending',
                    'total'     => 1,
                    'pending'   => 1,
                    'saved'     => 0
                ]);
            }
            $sales_ledger = isset($request['sales_ledger']) && $request['sales_ledger'] != "Select Ledger" ? $request['sales_ledger'] : null;
            $sales_ledger_id = Ledger::getLedgerByName($iPartyId, $sales_ledger);
            $gstMapping = $this->getGstMapping($iPartyId, $sales_ledger_id->name ?? $sales_ledger);
            // ✅ CREATE TRANSACTION
            $transaction = SalesTransaction::create([
                'iPartyId'     => $iPartyId,
                'upload_id'    => $upload->id,
                'invoice_no'   => $request->invoice,
                'date'         => $request->date ?? now(),
                'party_name'   => $request->party,
                'gst_no'       => $request->gst,
                'place_of_supply' => $request->place,
                'vchType'      => $request->voucher_type ?? 'Sales',
                'status'       => 'pending',
                'source'       => 'manual',
                'gst_mode'     => $request->gst_mode ?? 'standard',
                'Remarks'      => $request->remarks,
                'is_igst'      => $request->is_igst,
                'sales_ledger' => $sales_ledger_id->name ?? null,
                'address'      => $request->address,
                'pincode'      => $request->pincode,
                'city'         => $request->city,
                // ✅ Ledger store (without item case)
                'sales_ledger_id'   => $sales_ledger_id->id ?? 0, // $request->sales_ledger_id ?? null,
                'sales_ledger_name' => $sales_ledger_id->name ?? null,
                'strYear'       => session('year'),
                'year_from_date' => session('year_from'),
                'year_to_date'  => session('year_to'),
                'isWithItem'    => $request->entry_mode == 'noitem' ? 0 : 1,
                'gst_rate'      => $request->gst_rate ?? 0,
                'against_invoice' => $request->against_invoice
            ]);

            $sumAmount = $sumSgst = $sumCgst = $sumIgst = $sumTotal = 0;

            // =====================================================
            // ✅ CASE 1: WITH ITEMS
            // =====================================================
            if (!empty($request->items)) {
                foreach ($request->items as $item) {
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
                    SalesTransactionItem::create([
                        'iPartyId'       => $iPartyId,
                        'transaction_id' => $transaction->id,
                        'upload_id'      => $upload->id,
                        'item_name'      => $item['item'] ?? null,
                        'hsn'            => $item['hsn'] ?? null,
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
                $sumAmount = 0;
                $sumCgst   = 0;
                $sumSgst   = 0;
                $sumIgst   = 0;
                if($request->entry_mode == 'noitem')
                {
                    $sumAmount = collect($request->noitem_rows ?? [])->sum(fn ($row) => (float) ($row['amount'] ?? 0));

                    if (($request->gst_mode ?? 'standard') == 'custom' && !empty($request->custom_slots))
                    {
                        $this->storeNoItemLedgerWiseCustomGst(
                            $transaction,
                            $request->noitem_rows,
                            $request->custom_slots,
                            $gstMapping,
                            (bool) ($request->is_igst ?? 0)
                        );

                        foreach ($request->custom_slots as $slot)
                        {
                            $sumCgst += (float) ($slot['cgst_amount'] ?? 0);
                            $sumSgst += (float) ($slot['sgst_amount'] ?? 0);
                            $sumIgst += (float) ($slot['igst_amount'] ?? 0);
                        }
                    }
                    else
                    {
                        foreach($request->noitem_rows as $row)
                        {
                            $rowAmount = (float)($row['amount'] ?? 0);
                            $rowGstRate = (float)($row['gst'] ?? 0);
                            $rowGstAmount = ($rowAmount * $rowGstRate) / 100;

                            if (($request->is_igst ?? 0) == 1) {
                                $sumIgst += $rowGstAmount;
                            } else {
                                $sumCgst += $rowGstAmount / 2;
                                $sumSgst += $rowGstAmount / 2;
                            }
                        }
                    }
                }
                else
                {
                    $amount = (float)($request->amount ?? 0);
                    $sumAmount = $amount;
                    if (($request->is_igst ?? $transaction->is_igst) == 1) {
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
                // $transaction->igst_id = $request->igst_ledger;
                // $transaction->cgst_id = $request->cgst_ledger;
                // $transaction->sgst_id = $request->sgst_ledger;

                // $transaction->igst_ledger_name = optional(
                //     Ledger::getLedgerById($iPartyId, $request->igst_ledger)
                // )->name;

                // $transaction->cgst_ledger_name = optional(
                //     Ledger::getLedgerById($iPartyId, $request->cgst_ledger)
                // )->name;

                // $transaction->sgst_ledger_name = optional(
                //     Ledger::getLedgerById($iPartyId, $request->sgst_ledger)
                // )->name;
                $transaction->igst_id = $this->resolveGstLedgerId($iPartyId, $request->igst_ledger, $gstMapping, 'igst_id');
                $transaction->cgst_id = $this->resolveGstLedgerId($iPartyId, $request->cgst_ledger, $gstMapping, 'cgst_id');
                $transaction->sgst_id = $this->resolveGstLedgerId($iPartyId, $request->sgst_ledger, $gstMapping, 'sgst_id');

                $transaction->igst_ledger_name = $this->gstLedgerName($iPartyId, $transaction->igst_id);
                $transaction->cgst_ledger_name = $this->gstLedgerName($iPartyId, $transaction->cgst_id);
                $transaction->sgst_ledger_name = $this->gstLedgerName($iPartyId, $transaction->sgst_id);
            }

            // =====================================================
            // ✅ CUSTOM GST
            // =====================================================
            if (($request->gst_mode ?? 'standard') == 'custom' && $request->entry_mode != 'noitem' && !empty($request->custom_slots)) {
                $this->storeWithItemCustomGst($transaction, $request->custom_slots, $gstMapping);
            }

            // if (!empty($request->custom_slots)) {
            //     foreach ($request->custom_slots as $slot) {
            //         if (
            //             ($slot['igst_amount'] ?? 0) != 0 ||
            //             ($slot['cgst_amount'] ?? 0) != 0 ||
            //             ($slot['sgst_amount'] ?? 0) != 0
            //         ) {
            //             SalesCustomGst::create([
            //                 'transaction_id' => $transaction->id,
            //                 'gst_rate'       => $slot['rate'] ?? 0,
            //                 'taxable'        => $slot['taxable'] ?? 0,

            //                 'igst_ledger_id'   => $slot['igst_ledger_id'] ?? null,
            //                 'igst_ledger_name' => optional(Ledger::getLedgerById($iPartyId, $slot['igst_ledger_id']))->name,
            //                 'igst_amount'      => $slot['igst_amount'] ?? 0,

            //                 'cgst_ledger_id'   => $slot['cgst_ledger_id'] ?? null,
            //                 'cgst_ledger_name' => optional(Ledger::getLedgerById($iPartyId, $slot['cgst_ledger_id']))->name,
            //                 'cgst_amount'      => $slot['cgst_amount'] ?? 0,

            //                 'sgst_ledger_id'   => $slot['sgst_ledger_id'] ?? null,
            //                 'sgst_ledger_name' => optional(Ledger::getLedgerById($iPartyId, $slot['sgst_ledger_id']))->name,
            //                 'sgst_amount'      => $slot['sgst_amount'] ?? 0,
            //             ]);
            //         }
            //     }
            // }

            // =====================================================
            // ✅ FINAL TOTAL UPDATE
            // =====================================================
            $roundOffSetting = $this->getRoundOffSetting($iPartyId);
            $roundOffLedger = $roundOffSetting['ledger'];
            $transaction->update([
                'amount'       => $sumAmount,
                'sgst'         => $sumSgst,
                'cgst'         => $sumCgst,
                'igst'         => $sumIgst,
                // 'total_amount' => $sumAmount + $sumSgst + $sumCgst + $sumIgst,
                'total_amount' => $this->calculateTotalAmountWithRoundOff($sumAmount, $sumSgst, $sumCgst, $sumIgst, $roundOffSetting['side']),
                'roundoff_id'  => $roundOffLedger?->iLedgerId,
                'roundoff_ledger_name' => $roundOffLedger?->strCustomerName,
                'roundoff'     => $this->calculateRoundOffAmount($sumAmount, $sumSgst, $sumCgst, $sumIgst, $roundOffSetting['side']),
            ]);

            DB::commit();
            return response()->json([
                'status' => true,
                'id' => $transaction->id
            ]);

        // } catch (\Exception $e) {
        //     DB::rollback();
        //     return response()->json([
        //         'status' => false,
        //         'message' => $e->getMessage()
        //     ]);
        // }
    }

}

