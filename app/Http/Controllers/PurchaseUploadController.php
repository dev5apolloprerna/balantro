<?php

namespace App\Http\Controllers;

use App\Models\BulkPurchaseUpload;
use App\Models\PurchaseTransaction;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Ledger;
use Illuminate\Support\Facades\DB;
use App\Support\VoucherValidation;
use App\Models\Client;
use Illuminate\Support\Facades\Session;
use App\Models\PurchaseTransactionItem;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use App\Models\PurchaseCustomGst;

class PurchaseUploadController extends Controller
{
    use VoucherValidation;
    // LIST PAGE
    public function index()
    {
        $iPartyId = session('iPartyId');

        $uploads = BulkPurchaseUpload::where('iPartyId', $iPartyId)
            ->whereIn('status', ['Pending','Processing'])
            ->latest()
            ->get();

        $years = DB::table('YearMaster')
            ->where('iPartyId', $iPartyId)
            ->orderBy('strYear', 'asc')
            ->get();

        $clients = Client::orderBy('name')->get();
        // $purcasheLedgers = Ledger::getPurchaseLedgers($iPartyId);
        $vchTypes = DB::table('VchHistory')
            ->where('iPartyId', $iPartyId)
            ->where('vchType', 'Purchase')
            ->distinct()
            ->pluck('vchType');
        $vchTypes = $vchTypes->isEmpty()
            ? collect(['Purchase'])
            : $vchTypes;
        $states = DB::table('state')
            ->pluck('stateName');
        $groups = DB::table('GroupMaster')
            ->where('iPartyId', $iPartyId)
            ->distinct()
            ->pluck('strGroupName');
        $parents = DB::table('LedgerMaster')
            ->select('strParents')
            ->where('iPartyId', $iPartyId)
            ->where('strParents', 'Sundry Creditors')
            ->distinct()
            ->get();
        $ledgers = Ledger::getAllCreditorsLedgers($iPartyId);

        $iGstLedgers = Ledger::getAlliGstLedgers($iPartyId);
        $cGstLedgers = Ledger::getAllcGstLedgers($iPartyId);
        $sGstLedgers = Ledger::getAllsGstLedgers($iPartyId);
        $purcasheLedgers = Ledger::getPurchaseLedgers($iPartyId);
        $stockItems = DB::table('StockItemMaster')
            ->select(
                    'iStockIdtemId',
                    'strItemName',
                    'strBaseUnits',
                    'CGSTLedgerId',
                    'SGSTLedgerId',
                    'IGSTLedgerId'
                )
            ->where('iPartyId', $iPartyId)
            ->orderBy('strItemName', 'asc') // optional (recommended)
            ->get();
        
        $purchaseGstMappings = $this->getPurchaseLedgerGstMappings($iPartyId);
        $roundOffSide = $this->getRoundOffSetting($iPartyId)['side'];
        return view('admin.bulkupload.purchase.index', compact('uploads', 'clients', 'purcasheLedgers','vchTypes',
        'states','groups','parents','ledgers','iGstLedgers','cGstLedgers','sGstLedgers','years','stockItems','purchaseGstMappings','roundOffSide'));
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

    private function getGstMapping($partyId,$purchaseLedgerName,$itemName = null)
    {
        $mapping = [
            'cgst_id' => null,
            'sgst_id' => null,
            'igst_id' => null,

            'cgst_name' => null,
            'sgst_name' => null,
            'igst_name' => null,
        ];

        if (!empty($itemName))
        {
            $item = DB::table('StockItemMaster')
                ->select(
                    'iStockIdtemId',
                    'strItemName',
                    'strBaseUnits',
                    'CGSTLedgerId',
                    'SGSTLedgerId',
                    'IGSTLedgerId'
                )
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

        if (
            empty($mapping['cgst_id']) &&
            empty($mapping['sgst_id']) &&
            empty($mapping['igst_id'])
        )
        {
            $ledger = DB::table('LedgerMaster')
                ->where('iPartyId', $partyId)
                ->where('strCustomerName', $purchaseLedgerName)
                ->first();

            if ($ledger)
            {
                $mapping['cgst_id'] = $ledger->CGSTLedgerId;
                $mapping['sgst_id'] = $ledger->SGSTLedgerId;
                $mapping['igst_id'] = $ledger->IGSTLedgerId;
            }
        }

        if (!empty($mapping['cgst_id']))
        {
            $cgst = DB::table('LedgerMaster')
                ->where('iPartyId', $partyId)
                ->where('iLedgerId', $mapping['cgst_id'])
                ->first();

            $mapping['cgst_name'] = $cgst->strCustomerName ?? null;
        }

        if (!empty($mapping['sgst_id']))
        {
            $sgst = DB::table('LedgerMaster')
                ->where('iPartyId', $partyId)
                ->where('iLedgerId', $mapping['sgst_id'])
                ->first();

            $mapping['sgst_name'] = $sgst->strCustomerName ?? null;
        }

        if (!empty($mapping['igst_id']))
        {
            $igst = DB::table('LedgerMaster')
                ->where('iPartyId', $partyId)
                ->where('iLedgerId', $mapping['igst_id'])
                ->first();

            $mapping['igst_name'] = $igst->strCustomerName ?? null;
        }

        return $mapping;
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

    private function storeNoItemLedgerWiseCustomGst(PurchaseTransaction $transaction, array $noitemRows, array $customSlots, array $gstMapping, bool $isIgst): void
    {
        PurchaseCustomGst::where('transaction_id', $transaction->id)->delete();

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

            PurchaseCustomGst::create([
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

    private function normalizeGstNo(?string $gstNo): string
    {
        return strtoupper(preg_replace('/\s+/', '', trim((string) $gstNo)));
    }

    private function findPartyLedgerForUpload(int $partyId, ?string $partyName, ?string $gstNo): ?array
    {
        $partyName = trim((string) $partyName);
        $gstNo = $this->normalizeGstNo($gstNo);

        $ledgerSelect = "name, gst_no, address, pincode, city, state";
        $ledgerQuery = DB::query()->fromSub(function ($query) use ($partyId) {
            $query->from('LedgerMaster')
                ->selectRaw("strCustomerName AS name, GSTNo AS gst_no, LedgerAddress AS address, Pincode AS pincode, '' AS city, StateName AS state")
                ->where('iPartyId', $partyId)
                ->unionAll(
                    DB::table('ledgers')
                        ->selectRaw("Name AS name, GstNo AS gst_no, TRIM(CONCAT_WS(' ', AddressLine1, AddressLine2)) AS address, Pincode AS pincode, City AS city, State AS state")
                        ->where('iPartyId', $partyId)
                );
        }, 'party_ledgers')
            ->selectRaw($ledgerSelect);

        $ledger = null;
        $matchedByGst = false;
        $matchedByName = false;

        if ($gstNo !== '') {
            $ledger = (clone $ledgerQuery)
                ->whereRaw("UPPER(REPLACE(TRIM(COALESCE(gst_no, '')), ' ', '')) = ?", [$gstNo])
                ->first();
            $matchedByGst = (bool) $ledger;
        }

        if (!$ledger && $partyName !== '') {
            $ledger = (clone $ledgerQuery)
                ->whereRaw('LOWER(TRIM(name)) = ?', [strtolower($partyName)])
                ->first();
            $matchedByName = (bool) $ledger;
        }

        if (!$ledger) {
            return null;
        }

        $details = [
            'name' => trim((string) ($ledger->name ?? '')),
            'gst_no' => trim((string) ($ledger->gst_no ?? '')),
            'address' => trim((string) ($ledger->address ?? '')),
            'pincode' => trim((string) ($ledger->pincode ?? '')),
            'city' => trim((string) ($ledger->city ?? '')),
            'state' => trim((string) ($ledger->state ?? '')),
            'matched_by_gst' => $matchedByGst,
            'matched_by_name' => $matchedByName,
        ];

        if ($details['state'] !== '' && is_numeric($details['state'])) {
            $details['state'] = DB::table('state')
                ->where('stateId', $details['state'])
                ->value('stateName') ?? $details['state'];
        }

        return $details;
    }

    private function isPartyLedgerAcceptedForUpload(?array $ledgerDetails, ?string $uploadedGstNo): bool
    {
        if (!$ledgerDetails) {
            return false;
        }

        $hasUploadedGstNo = $this->normalizeGstNo($uploadedGstNo) !== '';

        if ($hasUploadedGstNo) {
            return !empty($ledgerDetails['matched_by_gst']);
        }

        return !empty($ledgerDetails['matched_by_name']);
    }

    private function hasCompletePartyLedgerDetails(?array $ledgerDetails): bool
    {
        return $ledgerDetails
            && trim((string) ($ledgerDetails['gst_no'] ?? '')) !== ''
            && trim((string) ($ledgerDetails['address'] ?? '')) !== ''
            && trim((string) ($ledgerDetails['pincode'] ?? '')) !== ''
            && trim((string) ($ledgerDetails['state'] ?? '')) !== '';
    }

    private function getCompletePartyLedgerDetails(int $partyId, ?string $partyName, ?string $gstNo = null): ?array
    {
        $ledgerDetails = $this->findPartyLedgerForUpload($partyId, $partyName, $gstNo);

        return $this->hasCompletePartyLedgerDetails($ledgerDetails) ? $ledgerDetails : null;
    }

    private function mergePartyLedgerDetails(array $source, ?array $ledgerDetails): array
    {
        if (!$ledgerDetails) {
            return $source;
        }

        if (!empty($ledgerDetails['matched_by_gst']) && trim((string) ($ledgerDetails['name'] ?? '')) !== '') {
            $source['party_name'] = $ledgerDetails['name'];
        }

        if (!$this->hasCompletePartyLedgerDetails($ledgerDetails)) {
            return $source;
        }

        $source['gst_no'] = trim((string) ($source['gst_no'] ?? '')) !== '' ? $source['gst_no'] : $ledgerDetails['gst_no'];
        $source['place_of_supply'] = trim((string) ($source['place_of_supply'] ?? '')) !== '' ? $source['place_of_supply'] : $ledgerDetails['state'];
        $source['address'] = trim((string) ($source['address'] ?? '')) !== '' ? $source['address'] : $ledgerDetails['address'];
        $source['pincode'] = trim((string) ($source['pincode'] ?? '')) !== '' ? $source['pincode'] : $ledgerDetails['pincode'];
        $source['city'] = trim((string) ($source['city'] ?? '')) !== '' ? $source['city'] : $ledgerDetails['city'];

        return $source;
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
            return redirect()->route('data_entry_operators.bulkuploadpurchase')
                ->with('error', 'Please select company first');
        }

        $request->validate([
            'purchase_file' => 'required|mimes:xlsx,xls|max:30720'
        ]);

        $file     = $request->file('purchase_file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $path     = $file->storeAs('purchase_uploads', $fileName, 'public');

        $upload = BulkPurchaseUpload::create([
            'iPartyId'  => $iPartyId,
            'file_name' => $fileName,
            'file_path' => $path,
            'type'      => 'Item Invoice',
            'status'    => 'Processing',
            // 'purchase_ledger' => $request->purchase_ledger
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
                if (empty($row[0]) || empty($row[1])) continue; // if (empty($row[0]) || empty($row[1]) || empty($row[3])) continue;

                
                $invoiceNo = trim((string) $row[0]);
                $date      = $this->parseDate($row[1]);
                $partyName = trim((string)$row[3]);

                $groupKey = $invoiceNo.'|'.$partyName.'|'.$date;
                $invoiceGroups[$groupKey][] = [
                    'date'            => $date,
                    'invoice_no'            => $invoiceNo,
                    'gst_no'          => $row[2]  ?? null,
                    'party_name'      => $row[3]  ?? null,
                    'place_of_supply' => $row[4]  ?? null,
                    'purchase_ledger' => $row[5]  ?? null,
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

                    // Aggregate totals across all item lines for this invoice
                    $sumAmount      = array_sum(array_column($items, 'amount'));
                    $sumSgst        = array_sum(array_column($items, 'sgst'));
                    $sumCgst        = array_sum(array_column($items, 'cgst'));
                    $sumIgst        = array_sum(array_column($items, 'igst'));
                    $sumTotalAmount = array_sum(array_column($items, 'total_amount'));

                    $partyLedgerDetails = $this->findPartyLedgerForUpload(
                        $iPartyId,
                        $items[0]['party_name'] ?? null,
                        $items[0]['gst_no'] ?? null
                    );
                    $partyLedgerMatched = $this->isPartyLedgerAcceptedForUpload(
                        $partyLedgerDetails,
                        $items[0]['gst_no'] ?? null
                    );
                    $first = $this->mergePartyLedgerDetails(
                        $items[0],
                        $partyLedgerDetails
                    );
                    $purchaseLedger = DB::table('LedgerMaster')
                        ->where('iPartyId', $iPartyId)
                        ->where('strCustomerName', $first['purchase_ledger'])
                        ->first();

                    $mapping = $this->getGstMapping(
                        $iPartyId,
                        $first['purchase_ledger']
                    );

                    $status = 'pending';
                    $amountMatched = true;
                    $rates = [];

                    foreach ($items as $item)
                    {
                        $gstRate = 0;
                        if($item['amount'] > 0)
                        {
                            $gstRate =(($item['cgst'] + $item['sgst'] + $item['igst']) * 100 ) / $item['amount'];
                        }
                        $rates[] = round($gstRate,2);
                        $calculatedAmount = round((float)$item['quantity'] * (float)$item['rate'],2);

                        if($calculatedAmount != round((float)$item['amount'],2))
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

                    $rates = array_unique($rates);
                    $hasValidGstSlab = $this->allGstRatesAreApplicable($rates);
                    $isDuplicateVoucher = $this->voucherCombinationExists('purchase_transactions', [
                        'iPartyId' => $iPartyId,
                        'voucher_column' => 'vchType',
                        'voucher_value' => 'Purchase',
                        'number_column' => 'invoice_no',
                        'number_value' => $first['invoice_no'] ?? $invoiceNo,
                        'party_column' => 'party_name',
                        'party_value' => $first['party_name'],
                        'date_column' => 'date',
                        'date_value' => $first['date'],
                        'year_column' => 'strYear',
                        'year_value' => session('year'),
                    ]) || $this->vchHistoryCombinationExists([
                        'iPartyId' => $iPartyId,
                        'voucher_value' => 'Purchase',
                        'number_value' => $first['invoice_no'] ?? $invoiceNo,
                        'party_value' => $first['party_name'],
                        'history_date_value' => $this->historyDate($first['date']),
                        'year_value' => session('year'),
                    ]);
                    $hasRequiredDetails = $this->hasPurchaseRequiredDetails(
                        $first['party_name'] ?? null,
                        $items,
                        [],
                        $first['purchase_ledger'] ?? null
                    );
                    $purchaseLedgerMapped = $this->purchaseLedgerIsMapped($iPartyId, $first['purchase_ledger'] ?? null);

                    if (!$hasRequiredDetails || !$purchaseLedgerMapped || !$hasValidGstSlab || $isDuplicateVoucher || !$partyLedgerMatched) {
                        $status = 'pending';
                    }

                    $gstMode =
                        count($rates) > 1
                            ? 'custom'
                            : 'standard';

                    $gstRate =
                        count($rates)
                            ? reset($rates)
                            : 0;
                    // One parent transaction per invoice
                    // $transaction = PurchaseTransaction::create([
                    //     'iPartyId'        => $iPartyId,
                    //     'upload_id'       => $upload->id,
                    //     'invoice_no'      => $invoiceNo,
                    //     'date'            => $first['date'],
                    //     'gst_no'          => $first['gst_no'],
                    //     'party_name'      => $first['party_name'],
                    //     'place_of_supply' => $first['place_of_supply'],
                    //     'purchase_ledger' => $first['purchase_ledger'],
                    //     'amount'          => $sumAmount,
                    //     'sgst'            => $sumSgst,
                    //     'cgst'            => $sumCgst,
                    //     'igst'            => $sumIgst,
                    //     'total_amount'    => $sumTotalAmount,
                    //     'status'          => $status,
                    //     'vchType'         => 'purchase',
                    // ]);
                    $roundOffSetting = $this->getRoundOffSetting($iPartyId);
                    $roundOffLedger = $roundOffSetting['ledger'];
                    
                    $transaction = PurchaseTransaction::create([
                        'iPartyId'              => $iPartyId,
                        'upload_id'             => $upload->id,

                        'invoice_no'            => $first['invoice_no'] ?? $invoiceNo,
                        'date'                  => $first['date'],
                        'gst_no'                => $first['gst_no'],
                        'party_name'            => $first['party_name'],
                        'place_of_supply'       => $first['place_of_supply'],
                        'address'               => $first['address'] ?? null,
                        'pincode'               => $first['pincode'] ?? null,
                        'city'                  => $first['city'] ?? null,

                        'purchase_ledger'       => $first['purchase_ledger'],
                        'purchase_ledger_id'    => $purchaseLedger?->iLedgerId,
                        'purchase_ledger_name'  => $purchaseLedger?->strCustomerName,

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
                        // 'total_amount'         => $sumTotalAmount,
                        'total_amount'         => $this->calculateTotalAmountWithRoundOff($sumAmount, $sumSgst, $sumCgst, $sumIgst, $roundOffSetting['side']),
                        'is_igst'             => $is_igst,
                        'status'              => $status,
                        'vchType'             => 'Purchase',
                        'roundoff_id'          => $roundOffLedger?->iLedgerId,
                        'roundoff_ledger_name' => $roundOffLedger?->strCustomerName,
                        'roundoff'          => $this->calculateRoundOffAmount($sumAmount, $sumSgst, $sumCgst, $sumIgst, $roundOffSetting['side']),
                    ]);

                    // One item row per line
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
                        PurchaseTransactionItem::create([
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

                        // PurchaseTransactionItem::create([
                        //     'iPartyId'       => $iPartyId,
                        //     'transaction_id' => $transaction->id,
                        //     'upload_id'      => $upload->id,
                        //     'item_name'      => $item['item_name'],
                        //     'quantity'       => $item['quantity'],
                        //     'rate'           => $item['rate'],
                        //     'amount'         => $item['amount'],
                        //     'sgst'           => $item['sgst'],
                        //     'cgst'           => $item['cgst'],
                        //     'igst'           => $item['igst'],
                        //     'total_amount'   => $item['total_amount'],
                        // ]);
                    }

                    $totalInvoices++;
                }
            });
            $savedCount = PurchaseTransaction::where(
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
            // $upload->update([
            //     'total'   => $totalInvoices,
            //     'pending' => $totalInvoices,
            //     'status'  => 'Pending',
            // ]);

        // ── ACCOUNTING INVOICE (no items) ─────────────────────────────────────
        } elseif ($isAccountingInvoice) {
            
            $invoiceGroups = [];
            foreach ($sheet as $key => $row) {
                if ($key === 0) continue;
                if (empty(array_filter($row))) continue;
                if (empty($row[0]) || empty($row[1])) continue; // if (empty($row[0]) || empty($row[1]) || empty($row[3])) continue;
                
                
                $invoiceNo = trim((string) $row[0]);
                $date      = $this->parseDate($row[1]);
                $partyName = trim((string)$row[3]);

                $groupKey = $invoiceNo.'|'.$partyName.'|'.$date;
                $invoiceGroups[$groupKey][] = [
                    'invoice_no'      => $invoiceNo,
                    'date'            => $this->parseDate($row[1]),
                    'gst_no'          => $row[2] ?? null,
                    'party_name'      => $row[3] ?? null,
                    'place_of_supply' => $row[4] ?? null,
                    'purchase_ledger' => $row[5] ?? null,
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
                    // $first = $this->mergePartyLedgerDetails(
                    //     $rows[0],
                    //     $this->getCompletePartyLedgerDetails($iPartyId, $rows[0]['party_name'] ?? null)
                    // );

                    $partyLedgerDetails = $this->findPartyLedgerForUpload(
                        $iPartyId,
                        $rows[0]['party_name'] ?? null,
                        $rows[0]['gst_no'] ?? null
                    );
                    $partyLedgerMatched = $this->isPartyLedgerAcceptedForUpload(
                        $partyLedgerDetails,
                        $rows[0]['gst_no'] ?? null
                    );
                    $first = $this->mergePartyLedgerDetails(
                        $rows[0],
                        $partyLedgerDetails
                    );
                    
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

                    // Build custom GST slots from individual rows (NOT grouped by rate)
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
                    
                    // 🔥 IMPORTANT: For accounting invoices (without items), always use 'custom' mode
                    // because multiple rows represent different GST components
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
                    
                    // Status is always 'saved' for accounting invoices since they will use custom GST
                    $hasValidGstSlab = $this->allGstRatesAreApplicable(array_column($gstSlots, 'gst_rate'));
                    $isDuplicateVoucher = $this->voucherCombinationExists('purchase_transactions', [
                        'iPartyId' => $iPartyId,
                        'voucher_column' => 'vchType',
                        'voucher_value' => 'Purchase',
                        'number_column' => 'invoice_no',
                        'number_value' => $first['invoice_no'],
                        'party_column' => 'party_name',
                        'party_value' => $first['party_name'],
                        'date_column' => 'date',
                        'date_value' => $this->parseDate($first['date']),
                        'year_column' => 'strYear',
                        'year_value' => session('year'),
                    ]) || $this->vchHistoryCombinationExists([
                        'iPartyId' => $iPartyId,
                        'voucher_value' => 'Purchase',
                        'number_value' => $first['invoice_no'],
                        'party_value' => $first['party_name'],
                        'history_date_value' => $this->historyDate($first['date']),
                        'year_value' => session('year'),
                    ]);
                    $hasRequiredDetails = $this->hasPurchaseRequiredDetails(
                        $first['party_name'] ?? null,
                        [],
                        $rows,
                        $first['purchase_ledger'] ?? null
                    );
                    $purchaseLedgerMapped = $this->purchaseLedgerIsMapped($iPartyId, $first['purchase_ledger'] ?? null);
                    $gstLedgersMapped = $this->customGstLedgersAreMapped($gstSlots, $isIgst);
                    $amountMatched = $this->purchaseAmountsMatch([], $rows);
                    $status = (!$hasRequiredDetails || !$purchaseLedgerMapped || !$gstLedgersMapped || !$amountMatched || !$hasValidGstSlab || $isDuplicateVoucher || !$partyLedgerMatched) ? 'pending' : 'saved';
                    $roundOffSetting = $this->getRoundOffSetting($iPartyId);
                    $roundOffLedger = $roundOffSetting['ledger'];

                    $transaction = PurchaseTransaction::create([
                        'iPartyId'          => $iPartyId,
                        'upload_id'         => $upload->id,
                        
                        'invoice_no'        => $first['invoice_no'],
                        'date'              => $this->parseDate($first['date']),
                        
                        'gst_no'            => $first['gst_no'],
                        'party_name'        => $first['party_name'],
                        'place_of_supply'   => $first['place_of_supply'],
                        'address'           => $first['address'] ?? null,
                        'pincode'           => $first['pincode'] ?? null,
                        'city'              => $first['city'] ?? null,
                        
                        'purchase_ledger'      => $first['purchase_ledger'],
                        'purchase_ledger_id'   => $purchaseLedger?->iLedgerId,
                        'purchase_ledger_name' => $purchaseLedger?->strCustomerName,
                        
                        'cgst_id'           => $mapping['cgst_id'],
                        'cgst_ledger_name'  => $mapping['cgst_name'],
                        'sgst_id'           => $mapping['sgst_id'],
                        'sgst_ledger_name'  => $mapping['sgst_name'],
                        'igst_id'           => $mapping['igst_id'],
                        'igst_ledger_name'  => $mapping['igst_name'],
                        
                        'gst_mode'          => $gstMode,  // Always 'custom'
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
                        'vchType'           => 'purchase',
                        
                        'roundoff_id'          => $roundOffLedger?->iLedgerId,
                        'roundoff_ledger_name' => $roundOffLedger?->strCustomerName,
                        'roundoff'          => $this->calculateRoundOffAmount($sumAmount, $sumSgst, $sumCgst, $sumIgst, $roundOffSetting['side']),
                    ]);

                    // Create custom GST slots - ONE PER ROW
                    foreach ($gstSlots as $slot) {
                        if (($slot['igst_amount'] ?? 0) != 0 || 
                            ($slot['cgst_amount'] ?? 0) != 0 || 
                            ($slot['sgst_amount'] ?? 0) != 0) {
                            
                            PurchaseCustomGst::create([
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
            
            $savedCount = PurchaseTransaction::where('upload_id', $upload->id)
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

        return back()->with('success', 'Records Added Successfully');
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
        $date = trim((string) $value);
        if ($date === '') {
            return date('Y-m-d');
        }

        $normalizedDate = str_replace(['-', '.'], '/', $date);
        if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $normalizedDate, $matches)) {
            $day = (int) $matches[1];
            $month = (int) $matches[2];
            $year = (int) $matches[3];

            if (checkdate($month, $day, $year)) {
                return sprintf('%04d-%02d-%02d', $year, $month, $day);
            }
        }

        return date('Y-m-d', strtotime($date));
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

    private function hasPurchaseRequiredDetails(?string $partyName, array $items = [], array $noitemRows = [], ?string $purchaseLedger = null): bool
    {
        $hasParty = trim((string) $partyName) !== '';
        $hasItem = collect($items)->contains(fn ($item) => trim((string) ($item['item_name'] ?? $item['item'] ?? '')) !== '');
        $hasLedger = trim((string) $purchaseLedger) !== '' && trim((string) $purchaseLedger) !== 'Select Ledger';

        if (!$hasLedger) {
            $hasLedger = collect($noitemRows)->contains(fn ($row) => trim((string) ($row['ledger'] ?? '')) !== '');
        }

        return $hasParty && ($hasItem || $hasLedger);
    }

    private function purchaseLedgerIsMapped(int $partyId, ?string $purchaseLedger): bool
    {
        $purchaseLedger = trim((string) $purchaseLedger);

        if ($purchaseLedger === '' || $purchaseLedger === 'Select Ledger') {
            return false;
        }

        return DB::table('LedgerMaster')
            ->where('iPartyId', $partyId)
            ->where('strCustomerName', $purchaseLedger)
            ->exists();
    }

    private function gstLedgersAreMappedForAmounts(
        bool $isIgst,
        float $igst,
        float $cgst,
        float $sgst,
        $igstLedgerId,
        $cgstLedgerId,
        $sgstLedgerId
    ): bool {
        if ($isIgst || $igst > 0) {
            return $igst <= 0 || !empty($igstLedgerId);
        }

        return ($cgst <= 0 || !empty($cgstLedgerId))
            && ($sgst <= 0 || !empty($sgstLedgerId));
    }

    private function customGstLedgersAreMapped(array $customSlots, bool $isIgst): bool
    {
        foreach ($customSlots as $slot) {
            if (!$this->gstLedgersAreMappedForAmounts(
                $isIgst,
                (float) ($slot['igst_amount'] ?? 0),
                (float) ($slot['cgst_amount'] ?? 0),
                (float) ($slot['sgst_amount'] ?? 0),
                $slot['igst_ledger_id'] ?? null,
                $slot['cgst_ledger_id'] ?? null,
                $slot['sgst_ledger_id'] ?? null
            )) {
                return false;
            }
        }

        return true;
    }

    private function purchaseAmountsMatch(array $items = [], array $noitemRows = []): bool
    {
        foreach ($items as $item) {
            $quantity = (float) ($item['quantity'] ?? 0);
            $rate = (float) ($item['rate'] ?? 0);
            $amount = (float) ($item['amount'] ?? 0);

            if ($quantity > 0 && $rate > 0 && round($quantity * $rate, 2) != round($amount, 2)) {
                return false;
            }
        }

        foreach ($noitemRows as $row) {
            if ((float) ($row['amount'] ?? 0) <= 0) {
                return false;
            }
        }

        return true;
    }

    // SAVE PURCHASE
    public function save(Request $request)
    {
        $iPartyId = session('iPartyId');
        if (!$iPartyId) {
            return redirect()->route('data_entry_operators.bulkuploadpurchase')
                ->with('error', 'Please select company first');
        }

        $uploadId = null;
        foreach ($request->selected as $key => $id) {
            $row = PurchaseTransaction::find($id);
            if (!$row) continue;
            $voucherType = $request->voucher_type[$id] ?? $row->vchType;
            $invoiceNo = $request->invoice_no[$id] ?? $row->invoice_no;
            $partyName = $request->party_name[$id] ?: ($request->party_ledger[$id] ?? $request->ledger[$id] ?? $row->party_name);
            $date = $request->date[$id] ?? $row->date;
            if ($this->voucherCombinationExists('purchase_transactions', [
                'iPartyId' => $iPartyId,
                'voucher_column' => 'vchType',
                'voucher_value' => $voucherType,
                'number_column' => 'invoice_no',
                'number_value' => $invoiceNo,
                'party_column' => 'party_name',
                'party_value' => $partyName,
                'date_column' => 'date',
                'date_value' => $date,
                'year_column' => 'strYear',
                'year_value' => session('year'),
            ], $row->id) || $this->vchHistoryCombinationExists([
                'iPartyId' => $iPartyId,
                'voucher_value' => $voucherType,
                'number_value' => $invoiceNo,
                'party_value' => $partyName,
                'history_date_value' => $this->historyDate($date),
                'year_value' => session('year'),
            ])) {
                return response()->json(['status' => false, 'message' => 'Duplicate voucher combination is not allowed.']);
            }
            $uploadId = $row->upload_id;
            $existingItems = PurchaseTransactionItem::where('transaction_id', $row->id)
                ->get(['item_name'])
                ->map(fn ($item) => ['item_name' => $item->item_name])
                ->all();
            $purchaseLedger = $row->purchase_ledger;
            $rowStatus = $this->hasPurchaseRequiredDetails($partyName, $existingItems, [], $purchaseLedger)
                ? 'saved'
                : 'pending';
            $row->update([
                'invoice_no' => $request->invoice_no[$id],
                'date' => $request->date[$id],
                //'party_name' => $request->party_name[$id] ?? $request->ledger[$id],
                // 'party_name' => $request->party_name[$id]  ?: ($request->party_ledger[$id] ?? $request->ledger[$id]),
                'party_name' => $partyName,
                'place_of_supply' => $request->place_of_supply[$id],
                'purchase_ledger' => $request->party_name[$id]  ?: ($request->party_ledger[$id] ?? $request->ledger[$id]),
                //'status' => 'saved',
                'status' => $rowStatus,
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

            $status = ($pending == 0) ? 'Completed' : 'Pending';

            BulkPurchaseUpload::where('id', $uploadId)->update([
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
    }

    // PREVIEW PAGE
    public function preview($id)
    {
        $iPartyId = session('iPartyId');
        if (!$iPartyId) {
            return redirect()->route('data_entry_operators.bulkuploadpurchase')
                ->with('error', 'Please select company first');
        }

        $rows = PurchaseTransaction::where('upload_id', $id)
            ->where('status', 'pending')
            ->where('iPartyId', $iPartyId)
            ->paginate(50);
        $vchTypes = DB::table('VchHistory')
            ->where('iPartyId', $iPartyId)
            ->where('vchType', 'Purchase')
            ->distinct()
            ->pluck('vchType');
        $vchTypes = $vchTypes->isEmpty()
            ? collect(['Purchase'])
            : $vchTypes;
        $states = DB::table('state')
            ->pluck('stateName');
        $groups = DB::table('GroupMaster')
            ->where('iPartyId', $iPartyId)
            ->distinct()
            ->pluck('strGroupName');
        $parents = DB::table('LedgerMaster')
            ->select('strParents')
            ->where('iPartyId', $iPartyId)
            ->where('strParents', 'Sundry Creditors')
            ->distinct()
            ->get();
        $ledgers = Ledger::getAllCreditorsLedgers($iPartyId);

        $iGstLedgers = Ledger::getAlliGstLedgers($iPartyId);
        $cGstLedgers = Ledger::getAllcGstLedgers($iPartyId);
        $sGstLedgers = Ledger::getAllsGstLedgers($iPartyId);
        $purcasheLedgers = Ledger::getPurchaseLedgers($iPartyId);
        $stockItems = DB::table('StockItemMaster')
            ->select(
                'iStockIdtemId',
                'strItemName',
                'strBaseUnits',
                'CGSTLedgerId',
                'SGSTLedgerId',
                'IGSTLedgerId'
            )
            ->where('iPartyId', $iPartyId)
            ->orderBy('strItemName', 'asc') // optional (recommended)
            ->get();
        $purchaseGstMappings = $this->getPurchaseLedgerGstMappings($iPartyId);
        $roundOffSide = $this->getRoundOffSetting($iPartyId)['side'];
        return view('admin.bulkupload.purchase.preview', compact(
            'rows',
            'ledgers',
            'vchTypes',
            'groups',
            'states',
            'parents',
            'iGstLedgers',
            'cGstLedgers',
            'sGstLedgers',
            'purcasheLedgers',
            'stockItems',
            'purchaseGstMappings',
            'roundOffSide'
        ));
    }

    // DELETE ROW
    public function delete($id)
    {
        $row = PurchaseTransaction::find($id);
        if (!$row) {
            return response()->json([
                'status' => false,
                'message' => 'Record not found'
            ]);
        }
        $uploadId = $row->upload_id;
        $row->delete();
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
        return response()->json([
            'status' => true,
            'message' => 'Record deleted successfully'
        ]);
    }

    public function storeLedger(Request $request)
    {
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
            'OpeningType' => 'DR',
            'IsActive' => 1
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Ledger Created Successfully'
        ]);
    }

    // ── SHOW (used by both View and Edit modals via AJAX) ─────────────────────────
    public function show($id)
    {
        $transaction = PurchaseTransaction::with(['items','customGst'])->findOrFail($id);
        $gstMapping = $this->getGstMapping($transaction->iPartyId, $transaction->purchase_ledger);

        return response()->json([
            'id'              => $transaction->id,
            'invoice_no'      => $transaction->invoice_no,
            'date'            => $transaction->date,
            'gst_no'          => $transaction->gst_no,
            'vchType'         => $transaction->vchType,
            'party_name'      => $transaction->party_name,
            'place_of_supply' => $transaction->place_of_supply,
            'purchase_ledger' => $transaction->purchase_ledger,
            'amount'          => $transaction->amount,
            'sgst'            => $transaction->sgst,
            'cgst'            => $transaction->cgst,
            'igst'            => $transaction->igst,
            'total_amount'    => $transaction->total_amount,
            'roundoff'        => $transaction->roundoff ?? 0,
            'roundoff_id'     => $transaction->roundoff_id,
            'roundoff_ledger_name' => $transaction->roundoff_ledger_name,
            'Remarks'         => $transaction->Remarks,
            'address'         => $transaction->address,
            'pincode'         => $transaction->pincode,
            'city'            => $transaction->city,
            'is_igst'         => $transaction->is_igst,
            'status'          => $transaction->status,

            'igst_ledger_name'=> $transaction->igst_ledger_name,
            'cgst_ledger_name'=> $transaction->cgst_ledger_name,
            'sgst_ledger_name'=> $transaction->sgst_ledger_name,
            'igst_id' => $transaction->igst_id,
            'cgst_id' => $transaction->cgst_id,
            'sgst_id' => $transaction->sgst_id,
            'gst_mode'  => $transaction->gst_mode,
            'gst_rate'  => $transaction->gst_rate ?: $this->calculateGstRate($transaction),

            'address' => $transaction->address,
            'pincode' => $transaction->pincode,
            'city'  => $transaction->city,
            'Remarks'  => $transaction->Remarks,
            'custom_gst' => $transaction->customGst->map(function ($slot) use ($gstMapping) {
                return [
                    'id' => $slot->id,
                    'gst_rate' => $slot->gst_rate,
                    'taxable' => $slot->taxable,
                    'amount' => $slot->amount ?? $slot->taxable,
                    'ledger_id' => $slot->ledger_id,
                    'ledger_name' => $slot->ledger_name,
                    'igst_ledger_id' => $slot->igst_ledger_id ?: ($gstMapping['igst_id'] ?? null),
                    'igst_ledger_name' => $slot->igst_ledger_name ?: ($gstMapping['igst_name'] ?? null),
                    'igst_amount' => $slot->igst_amount,
                    'cgst_ledger_id' => $slot->cgst_ledger_id ?: ($gstMapping['cgst_id'] ?? null),
                    'cgst_ledger_name' => $slot->cgst_ledger_name ?: ($gstMapping['cgst_name'] ?? null),
                    'cgst_amount' => $slot->cgst_amount,
                    'sgst_ledger_id' => $slot->sgst_ledger_id ?: ($gstMapping['sgst_id'] ?? null),
                    'sgst_ledger_name' => $slot->sgst_ledger_name ?: ($gstMapping['sgst_name'] ?? null),
                    'sgst_amount' => $slot->sgst_amount,
                ];
            }),
            // ✅ FIXED ITEMS
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
    
    public function update(Request $request)
    {
        $data = $request->validate([
            'id'              => 'required|integer',
            'invoice_no'      => 'nullable|string',
            'date'            => 'nullable|date',
            'party_name'      => 'nullable|string',
            'gst_no'          => 'nullable|string',
            'place_of_supply' => 'nullable|string',
            // 'purchase_ledger' => 'nullable|string',
            'vchType'         => 'nullable|string',

            'address'         => 'nullable|string',
            'pincode'         => 'nullable|numeric',
            'city'            => 'nullable|string',
            'is_igst'         => 'nullable|numeric',
            'Remarks'         => 'nullable|string',


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
            'custom_slots.*.purchase_ledger_id' => 'nullable',
            'custom_slots.*.rate' => 'nullable|numeric',
            'custom_slots.*.taxable' => 'nullable|numeric',
            'custom_slots.*.igst_ledger_id' => 'nullable',
            'custom_slots.*.igst_amount' => 'nullable|numeric',
            'custom_slots.*.cgst_ledger_id' => 'nullable',
            'custom_slots.*.cgst_amount' => 'nullable|numeric',
            'custom_slots.*.sgst_ledger_id' => 'nullable',
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

            $transaction = PurchaseTransaction::findOrFail($data['id']);
            $newVoucherType = $request['vchType'] ?? $transaction->vchType;
            $newInvoiceNo = $request['invoice_no'] ?? $transaction->invoice_no;
            $newPartyName = $request['party_name'] ?? $transaction->party_name;
            $newDate = $request['date'] ?? $transaction->date;
            if ($this->voucherCombinationExists('purchase_transactions', [
                'iPartyId' => $transaction->iPartyId,
                'voucher_column' => 'vchType',
                'voucher_value' => $newVoucherType,
                'number_column' => 'invoice_no',
                'number_value' => $newInvoiceNo,
                'party_column' => 'party_name',
                'party_value' => $newPartyName,
                'date_column' => 'date',
                'date_value' => $newDate,
                'year_column' => 'strYear',
                'year_value' => session('year'),
            ], $transaction->id) || $this->vchHistoryCombinationExists([
                'iPartyId' => $transaction->iPartyId,
                'voucher_value' => $newVoucherType,
                'number_value' => $newInvoiceNo,
                'party_value' => $newPartyName,
                'history_date_value' => $this->historyDate($newDate),
                'year_value' => session('year'),
            ])) {
                throw new \Exception('Duplicate voucher combination is not allowed.');
            }
            // ===============================
            // HEADER UPDATE
            // ===============================
            $purchase_ledger = isset($request['purchase_ledger_name']) && $request['purchase_ledger_name'] != "Select Ledger" ? $request['purchase_ledger_name'] : $transaction->purchase_ledger;
            // $purchase_ledger = isset($request['purchase_ledger']) && $request['purchase_ledger'] != "Select Ledger" ? $request['purchase_ledger'] : null;
            $purchase_ledger_id = Ledger::getLedgerByName($transaction->iPartyId, $purchase_ledger);
            // $firstNoItemLedger = collect($request->noitem_rows ?? [])->firstWhere('ledger');
            // $purchaseLedgerId = $request->purchase_ledger_id ?: ($firstNoItemLedger['ledger'] ?? null);
            // $purchase_ledger = isset($request['purchase_ledger_name']) && $request['purchase_ledger_name'] != "Select Ledger"
            //     ? $request['purchase_ledger_name']
            //     : $transaction->purchase_ledger;
            // $purchase_ledger_id = $purchaseLedgerId
            //     ? Ledger::getLedgerById($transaction->iPartyId, $purchaseLedgerId)
            //     : Ledger::getLedgerByName($transaction->iPartyId, $purchase_ledger);
            // $purchase_ledger = $purchase_ledger_id->name ?? $purchase_ledger;
            // $purchase_ledger_id = Ledger::getLedgerByName($transaction->iPartyId, $purchase_ledger);
            $transaction->update([
                'invoice_no'      => $request['invoice_no'] ?? $transaction->invoice_no,
                'date'            => $request['date'],
                'party_name'      => $request['party_name'],
                'gst_no'          => $request['gst_no'],
                'place_of_supply' => $request['place_of_supply'],
                //'purchase_ledger' => isset($request['purchase_ledger_name']) && $request['purchase_ledger_name'] != "Select Ledger" ? $request['purchase_ledger_name'] : $transaction->purchase_ledger,
                'purchase_ledger' => $purchase_ledger,
                'vchType'         => $request['vchType'],
                'address'         => $request['address'],
                'pincode'         => $request['pincode'],
                'city'            => $request['city'],
                'is_igst'         => $request['is_igst'] ?? 0,
                'Remarks'         => $request['Remarks'],

                // ✅ NEW
                'gst_mode'        => $request->gst_mode ?? 'standard',

                // ✅ Ledger store (without item case)
                'purchase_ledger_id'   => $purchase_ledger_id->id ?? 0,
                'purchase_ledger_name' => $purchase_ledger_id->name ?? '',
                // 'purchase_ledger_id'   => $purchase_ledger_id->id ?? null,
                // 'purchase_ledger_name' => $purchase_ledger,
                'strYear'       => session('year'),
                'year_from_date' => session('year_from'),
                'year_to_date'  => session('year_to'),
                'isWithItem'    => $request->entry_mode == 'noitem' ? 0 : 1,
            ]);

            $gstMode = $request->gst_mode ?? 'standard';
            // $gstMapping = $this->getGstMapping($transaction->iPartyId, $purchase_ledger_id->name ?? $purchase_ledger);
            $gstMapping = $this->getGstMapping($transaction->iPartyId, $purchase_ledger);

            // ===============================
            // ITEMS HANDLING
            // ===============================
            $submittedIds = [];
            $sumAmount = $sumSgst = $sumCgst = $sumIgst = $sumTotal = 0;

            // =========================================================
            // ✅ CASE 1: WITH ITEMS
            // =========================================================
            if (!empty($data['items'])) {
                PurchaseTransactionItem::where('transaction_id', $transaction->id)
                    ->delete();
                foreach ($data['items'] as $itemData) {

                    $itemId = $itemData['id'] ?? null;

                    $itemData['iPartyId'] = $transaction->iPartyId;
                    $itemData['transaction_id'] = $transaction->id;
                    $itemData['upload_id'] = $transaction->upload_id;

                    if ($itemId) {
                        $item = PurchaseTransactionItem::find($itemId);

                        if ($item && $item->transaction_id == $transaction->id) {
                            $item->update($itemData);
                            $submittedIds[] = $itemId;
                        } else {
                            $itemId = null;
                        }
                    }

                    if (!$itemId) {
                        $item = PurchaseTransactionItem::create($itemData);
                    }

                    $sumAmount += (float)($itemData['amount'] ?? 0);
                    $sumSgst   += (float)($itemData['sgst'] ?? 0);
                    $sumCgst   += (float)($itemData['cgst'] ?? 0);
                    $sumIgst   += (float)($itemData['igst'] ?? 0);
                    $sumTotal  += (float)($itemData['total_amount'] ?? 0);
                }

            }
            // =========================================================
            // ✅ CASE 2: WITHOUT ITEMS
            // =========================================================
            else {

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
                    $amount = (float)($request->noitem_amount ?? 0);
                    $sumAmount = $amount;

                    if ($transaction->is_igst == 1) {
                        $sumIgst = (float)($request->igst ?? 0);
                    } else {
                        $sumCgst = (float)($request->cgst ?? 0);
                        $sumSgst = (float)($request->sgst ?? 0);
                    }
                }

                $sumTotal = $sumAmount + $sumCgst + $sumSgst + $sumIgst;

                // delete all items
                PurchaseTransactionItem::where('transaction_id', $transaction->id)->delete();
            }

            // =========================================================
            // ✅ STANDARD GST LEDGER SAVE
            // =========================================================
            if ($gstMode === 'standard') {

                $transaction->igst_id = $request->igst_ledger ?: ($gstMapping['igst_id'] ?? null);
                $transaction->cgst_id = $request->cgst_ledger ?: ($gstMapping['cgst_id'] ?? null);
                $transaction->sgst_id = $request->sgst_ledger ?: ($gstMapping['sgst_id'] ?? null);

                $igst_ledger = Ledger::getLedgerById($transaction->iPartyId, $transaction->igst_id);
                $cgst_ledger = Ledger::getLedgerById($transaction->iPartyId, $transaction->cgst_id);
                $sgst_ledger = Ledger::getLedgerById($transaction->iPartyId, $transaction->sgst_id);
                // store name also
                $transaction->igst_ledger_name = $igst_ledger->name ?? null;
                $transaction->cgst_ledger_name = $cgst_ledger->name ?? null;
                $transaction->sgst_ledger_name = $sgst_ledger->name ?? null;
            } else {
                $transaction->igst_id = $request->igst_ledger ?: ($gstMapping['igst_id'] ?? null);
                $igst_ledger = Ledger::getLedgerById($transaction->iPartyId, $transaction->igst_id);
                $transaction->igst_ledger_name = $igst_ledger->name ?? null;
            }

            // =========================================================
            // ✅ CUSTOM GST (RATE-WISE)
            // =========================================================
            if (!empty($request->custom_slots) && !empty($request->noitem_rows) && ($request->gst_mode ?? 'standard') === 'custom') {
                $this->storeNoItemLedgerWiseCustomGst(
                    $transaction,
                    $request->noitem_rows,
                    $request->custom_slots,
                    $gstMapping,
                    (bool) $transaction->is_igst
                );
            } elseif (!empty($request->custom_slots)) {
                // delete old
                PurchaseCustomGst::where('transaction_id', $transaction->id)->delete();
                foreach ($request->custom_slots as $slot) {

                    if ($slot['igst_amount'] != 0 || $slot['cgst_amount'] != 0 || $slot['sgst_amount'] != 0) {
                        $slot = $this->applyGstMappingToCustomSlot($slot, $gstMapping);
                        $igstLedger = Ledger::getLedgerById($transaction->iPartyId, $slot['igst_ledger_id']);
                        $cgstLedger = Ledger::getLedgerById($transaction->iPartyId, $slot['cgst_ledger_id']);
                        $sgstLedger = Ledger::getLedgerById($transaction->iPartyId, $slot['sgst_ledger_id']);
                        $purchaseLedger = !empty($slot['purchase_ledger_id']) ? Ledger::getLedgerById($transaction->iPartyId, $slot['purchase_ledger_id']) : null;


                        PurchaseCustomGst::create([
                            'transaction_id' => $transaction->id,
                            // ✅ NEVER NULL
                            'gst_rate' => $slot['rate'] ?? 0,
                            'taxable'  => $slot['taxable'] ?? 0,
                            'ledger_id' => $purchaseLedger?->id,
                            'ledger_name' => $purchaseLedger?->name,

                            'igst_ledger_id'   => $slot['igst_ledger_id'] ?? null,
                            'igst_ledger_name' => $igstLedger->name ?? null,
                            'igst_amount'      => $slot['igst_amount'] ?? 0,

                            'cgst_ledger_id'   => $slot['cgst_ledger_id'] ?? null,
                            'cgst_ledger_name' => $cgstLedger->name ?? null,
                            'cgst_amount'      => $slot['cgst_amount'] ?? 0,

                            'sgst_ledger_id'   => $slot['sgst_ledger_id'] ?? null,
                            'sgst_ledger_name' => $sgstLedger->name ?? null,
                            'sgst_amount'      => $slot['sgst_amount'] ?? 0,
                        ]);
                    }
                }
            }

            if ($gstMode === 'custom' && !empty($request->custom_slots)) {
                $sumIgst = collect($request->custom_slots)->sum(fn ($slot) => (float) ($slot['igst_amount'] ?? 0));
                $sumCgst = collect($request->custom_slots)->sum(fn ($slot) => (float) ($slot['cgst_amount'] ?? 0));
                $sumSgst = collect($request->custom_slots)->sum(fn ($slot) => (float) ($slot['sgst_amount'] ?? 0));
            }
            
            // =========================================================
            // FINAL TOTAL UPDATE
            // =========================================================
            $roundOffSetting = $this->getRoundOffSetting($transaction->iPartyId);
            $roundOffLedger = $roundOffSetting['ledger'];
            $requestItems = $data['items'] ?? [];
            $requestNoitemRows = $request->noitem_rows ?? [];
            $canMarkSaved = $this->hasPurchaseRequiredDetails(
                $request['party_name'] ?? null,
                $requestItems,
                $requestNoitemRows,
                $purchase_ledger
            );
            $hasApplicableGstRates = $this->allGstRatesAreApplicable($this->extractVoucherRequestGstRates(
                $requestItems,
                $requestNoitemRows,
                $request->custom_slots ?? [],
                $request->gst_rate ?? null
            ));
            $partyLedgerDetails = $this->findPartyLedgerForUpload(
                $transaction->iPartyId,
                $request['party_name'] ?? null,
                $request['gst_no'] ?? null
            );
            $partyLedgerMatched = $this->isPartyLedgerAcceptedForUpload(
                $partyLedgerDetails,
                $request['gst_no'] ?? null
            );
            $purchaseLedgerMapped = $this->purchaseLedgerIsMapped($transaction->iPartyId, $purchase_ledger);
            $amountMatched = $this->purchaseAmountsMatch($requestItems, $requestNoitemRows);
            $gstLedgersMapped = $gstMode === 'custom'
                ? $this->customGstLedgersAreMapped(
                    collect($request->custom_slots ?? [])
                        ->map(fn ($slot) => $this->applyGstMappingToCustomSlot($slot, $gstMapping))
                        ->all(),
                    (bool) $transaction->is_igst
                )
                : $this->gstLedgersAreMappedForAmounts(
                    (bool) $transaction->is_igst,
                    (float) $sumIgst,
                    (float) $sumCgst,
                    (float) $sumSgst,
                    $transaction->igst_id,
                    $transaction->cgst_id,
                    $transaction->sgst_id
                );
            $transaction->update([
                'amount'       => $sumAmount,
                'sgst'         => $sumSgst,
                'cgst'         => $sumCgst,
                'igst'         => $sumIgst,
                // 'total_amount' => $sumAmount + $sumSgst + $sumCgst + $sumIgst,
                'total_amount' => $this->calculateTotalAmountWithRoundOff($sumAmount, $sumSgst, $sumCgst, $sumIgst, $roundOffSetting['side']),
                // 'status'       => $this->allGstRatesAreApplicable($this->extractVoucherRequestGstRates(
                //     $data['items'] ?? [],
                //     $request->noitem_rows ?? [],
                //     $request->custom_slots ?? [],
                //     $request->gst_rate ?? null
                // )) ? 'saved' : 'pending',
                'status'       => (
                    $canMarkSaved
                    && $partyLedgerMatched
                    && $purchaseLedgerMapped
                    && $amountMatched
                    && $hasApplicableGstRates
                    && $gstLedgersMapped
                ) ? 'saved' : 'pending',
                'roundoff_id'  => $roundOffLedger?->iLedgerId,
                'roundoff_ledger_name' => $roundOffLedger?->strCustomerName,
                'roundoff'     => $this->calculateRoundOffAmount($sumAmount, $sumSgst, $sumCgst, $sumIgst, $roundOffSetting['side']),                
            ]);

            if ($transaction) {
                $saved = PurchaseTransaction::where('upload_id', $transaction->upload_id)
                    ->where('status', 'saved')
                    ->count();
                $pending = PurchaseTransaction::where('upload_id', $transaction->upload_id)
                    ->where('status', 'pending')
                    ->count();
                $total = PurchaseTransaction::where('upload_id', $transaction->upload_id)
                    ->count();

                $status = ($pending == 0) ? 'Completed' : 'Pending';

                BulkPurchaseUpload::where('id', $transaction->upload_id)->update([
                    'total' => $total,
                    'saved' => $saved,
                    'pending' => $pending,
                    'status'  => $status
                ]);
            }
        });
        return response()->json(['status' => true,'message' => 'Updated Successfully']);
    }

    public function changeUploadStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'status' => 'required|string'
        ]);

        $upload = BulkPurchaseUpload::find($request->id);
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
        $ids = collect((array) $request->input('ids', []))
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No records selected'
            ]);
        }

        $uploadsQuery = BulkPurchaseUpload::whereIn('id', $ids);

        if (session('iPartyId')) {
            $uploadsQuery->where('iPartyId', session('iPartyId'));
        }

        $uploadIds = $uploadsQuery->pluck('id');

        if ($uploadIds->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No matching purchase uploads found'
            ]);
        }

        DB::beginTransaction();
        try {
            $transactionIds = PurchaseTransaction::whereIn('upload_id', $uploadIds)->pluck('id');
            PurchaseCustomGst::whereIn('transaction_id', $transactionIds)->delete();
            PurchaseTransactionItem::whereIn('transaction_id', $transactionIds)->delete();
            PurchaseTransaction::whereIn('upload_id', $uploadIds)->delete();
            BulkPurchaseUpload::whereIn('id', $uploadIds)->delete();

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Selected purchase upload(s) deleted successfully'
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
            $upload = BulkPurchaseUpload::where('iPartyId', $iPartyId)
                ->where('type', 'Manual')
                ->first();

            if ($upload) {
                $upload->update([
                    'pending' => $upload->pending + 1,
                    'total'   => $upload->total + 1,
                    'status'    => 'Pending',
                ]);
            } else {
                $upload = BulkPurchaseUpload::create([
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
            $purchase_ledger = isset($request['purchase_ledger']) && $request['purchase_ledger'] != "Select Ledger" ? $request['purchase_ledger'] : null;
            $purcashe_ledger_id = Ledger::getLedgerByName($iPartyId, $purchase_ledger);
            
            // $gstMapping = $this->getGstMapping($iPartyId, $purcashe_ledger_id->name ?? $purchase_ledger);
            // $firstNoItemLedger = collect($request->noitem_rows ?? [])->firstWhere('ledger');
            // $purchaseLedgerId = $request->purchase_ledger_id ?: ($firstNoItemLedger['ledger'] ?? null);
            // $purchase_ledger = isset($request['purchase_ledger']) && $request['purchase_ledger'] != "Select Ledger"
            //     ? $request['purchase_ledger']
            //     : null;
            
            // $purcashe_ledger_id = $purchaseLedgerId
            //     ? Ledger::getLedgerById($iPartyId, $purchaseLedgerId)
            //     : Ledger::getLedgerByName($iPartyId, $purchase_ledger);

            $purchase_ledger = $purcashe_ledger_id->name ?? $purchase_ledger;
            $gstMapping = $this->getGstMapping($iPartyId, $purchase_ledger);
            if ($this->voucherCombinationExists('purchase_transactions', ['iPartyId'=>$iPartyId,'voucher_column'=>'vchType','voucher_value'=>$request->voucher_type ?? 'Purchase','number_column'=>'invoice_no','number_value'=>$request->invoice,'party_column'=>'party_name','party_value'=>$request->party,'date_column'=>'date','date_value'=>$request->date,'year_column'=>'strYear','year_value'=>session('year')]) || $this->vchHistoryCombinationExists(['iPartyId'=>$iPartyId,'voucher_value'=>$request->voucher_type ?? 'Purchase','number_value'=>$request->invoice,'party_value'=>$request->party,'history_date_value'=>$this->historyDate($request->date),'year_value'=>session('year')])) { return response()->json(['status'=>false,'message'=>'Duplicate voucher combination is not allowed.']); }
            // ✅ CREATE TRANSACTION
            $transaction = PurchaseTransaction::create([
                'iPartyId'     => $iPartyId,
                'upload_id'    => $upload->id,
                'invoice_no'   => $request->invoice,
                'date'         => $request->date ?? now(),
                'party_name'   => $request->party,
                'gst_no'       => $request->gst,
                'place_of_supply' => $request->place,
                'vchType'      => $request->voucher_type ?? 'Purchase',
                'status'       => 'pending',
                'source'       => 'manual',
                'gst_mode'     => $request->gst_mode ?? 'standard',
                'Remarks'      => $request->remarks,
                'is_igst'      => $request->is_igst,
                // 'purchase_ledger' => $purcashe_ledger_id->name,
                'purchase_ledger' => $purchase_ledger,
                'address'      => $request->address,
                'pincode'      => $request->pincode,
                'city'         => $request->city,
                // ✅ Ledger store (without item case)
                // 'purchase_ledger_id'   => $purcashe_ledger_id->id, // $request->sales_ledger_id ?? null,
                // 'purchase_ledger_name' => $purcashe_ledger_id->name,
                'purchase_ledger_id'   => $purcashe_ledger_id->id ?? null, // $request->sales_ledger_id ?? null,
                'purchase_ledger_name' => $purchase_ledger,
                'strYear'       => session('year'),
                'year_from_date' => session('year_from'),
                'year_to_date'  => session('year_to'),
                'isWithItem'    => $request->entry_mode == 'noitem' ? 0 : 1,
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
                    PurchaseTransactionItem::create([
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
                        $purchaseLedger = !empty($slot['purchase_ledger_id']) ? Ledger::getLedgerById($iPartyId, $slot['purchase_ledger_id']) : null;
                        PurchaseCustomGst::create([
                            'transaction_id' => $transaction->id,
                            'gst_rate'       => $slot['rate'] ?? 0,
                            'taxable'        => $slot['taxable'] ?? 0,
                            'ledger_id'      => $purchaseLedger?->id,
                            'ledger_name'    => $purchaseLedger?->name,

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

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }


}

