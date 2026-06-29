<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Client;

use App\Models\CreditNoteTransaction;
use App\Models\CreditNoteTransactionItem;
use App\Models\BulkCreditNoteUpload;
use Illuminate\Support\Facades\Session;
use App\Models\Ledger;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use App\Models\CreditNoteCustomGst;

class CreditNoteController extends Controller
{
    public function index(Request $request)
    {
        $iPartyId = session('iPartyId');
        $uploads = BulkCreditNoteUpload::where('iPartyId', $iPartyId)
            ->whereIn('status', ['pending','processing'])
            ->orderBy('id', 'desc')
            ->get();

        $clients = Client::orderBy('name')->get();
        $years = DB::table('YearMaster')
            ->where('iPartyId', $iPartyId)
            ->orderBy('strYear', 'asc')
            ->get();

        $vchTypes = DB::table('VchHistory')
            ->where('iPartyId', $iPartyId)
            ->where('vchType', 'Credit Note')
            ->distinct()
            ->pluck('vchType');
        if ($vchTypes->isEmpty()) {
            $vchTypes = collect(['Credit Note']);
        }
        // ✅ States
        $states = DB::table('state')
            ->pluck('stateName');

        // ✅ Groups
        $groups = DB::table('GroupMaster')
            ->where('iPartyId', $iPartyId)
            ->distinct()
            ->pluck('strGroupName');

        // ✅ Party Ledgers (Sundry Debtors)
        $parents = DB::table('LedgerMaster')
            ->select('strParents')
            ->where('iPartyId', $iPartyId)
            ->where('strParents', 'Sundry Debtors')
            ->distinct()
            ->get();

        $ledgers = Ledger::getAllDebtorsLedgers($iPartyId);

        // ✅ GST Ledgers
        $iGstLedgers = Ledger::getAlliGstLedgers($iPartyId);
        $cGstLedgers = Ledger::getAllcGstLedgers($iPartyId);
        $sGstLedgers = Ledger::getAllsGstLedgers($iPartyId);

        // ✅ SALES RETURN LEDGER (IMPORTANT 🔥)
        $salesLedgers = Ledger::getSalesLedgers($iPartyId);
        $stockItems = DB::table('StockItemMaster')
            ->select('*', 'CGSTLedgerId as cgst_id', 'SGSTLedgerId as sgst_id', 'IGSTLedgerId as igst_id')
            ->where('iPartyId', $iPartyId)
            ->orderBy('strItemName', 'asc') // optional (recommended)
            ->get();
        $salesGstMappings = $this->getSalesLedgerGstMappings($iPartyId);
         $roundOffSide = $this->getRoundOffSetting($iPartyId)['side'];
        return view('admin.bulkupload.credit_note.index', compact('uploads', 'clients','vchTypes','states','groups'
        ,'parents','ledgers','iGstLedgers','cGstLedgers','sGstLedgers','salesLedgers','stockItems','roundOffSide','years','salesGstMappings'));
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

    private function getLedgerDetailsForAutofill($partyId)
    {
        return DB::table('LedgerMaster')
            ->selectRaw("iLedgerId AS id, strCustomerName AS name, GSTNo AS gst_no, LedgerAddress AS address, Pincode AS pincode, '' AS city, StateName AS state")
            ->where('iPartyId', $partyId)
            ->union(
                DB::table('ledgers')
                    ->selectRaw("id AS id, name AS name, GstNo AS gst_no, CONCAT_WS(', ', NULLIF(AddressLine1, ''), NULLIF(AddressLine2, '')) AS address, Pincode AS pincode, City AS city, State AS state")
                    ->where('iPartyId', $partyId)
            )
            ->get();
    }

        private function normalizeGstNo($gstNo): string
    {
        return strtoupper(preg_replace('/\s+/', '', trim((string) $gstNo)));
    }

    private function ledgerDetailsArray($ledger, string $matchedBy): array
    {
        return [
            'party_name' => trim((string) ($ledger->party_name ?? '')),
            'gst_no' => trim((string) ($ledger->gst_no ?? '')),
            'address' => trim((string) ($ledger->address ?? '')),
            'pincode' => trim((string) ($ledger->pincode ?? '')),
            'city' => trim((string) ($ledger->city ?? '')),
            'state' => trim((string) ($ledger->state ?? '')),
            'matched_by' => $matchedBy,
        ];
    }

    private function hasMinimumLedgerDetails(array $details): bool
    {
        return $details['party_name'] !== '' && $details['gst_no'] !== '';
    }

    private function getCompletePartyLedgerDetails($partyId, ?string $partyName): ?array
    {
        $partyName = trim((string) $partyName);

        if ($partyName === '') {
            return null;
        }

        $ledger = DB::table('LedgerMaster')
            ->selectRaw("strCustomerName AS party_name, GSTNo AS gst_no, LedgerAddress AS address, Pincode AS pincode, '' AS city, StateName AS state") // ->selectRaw("GSTNo AS gst_no, LedgerAddress AS address, Pincode AS pincode, '' AS city, StateName AS state")
            ->where('iPartyId', $partyId)
            ->whereRaw('LOWER(TRIM(strCustomerName)) = ?', [strtolower($partyName)])
            ->first();

        if (!$ledger) {
            $ledger = DB::table('ledgers')
                ->selectRaw("name AS party_name, GstNo AS gst_no, CONCAT_WS(', ', NULLIF(AddressLine1, ''), NULLIF(AddressLine2, '')) AS address, Pincode AS pincode, City AS city, State AS state") // ->selectRaw("GstNo AS gst_no, CONCAT_WS(', ', NULLIF(AddressLine1, ''), NULLIF(AddressLine2, '')) AS address, Pincode AS pincode, City AS city, State AS state")
                ->where('iPartyId', $partyId)
                ->whereRaw('LOWER(TRIM(name)) = ?', [strtolower($partyName)])
                ->first();
        }

        if (!$ledger) {
            return null;
        }

        $details = $this->ledgerDetailsArray($ledger, 'party_name');

        return $this->hasMinimumLedgerDetails($details) ? $details : null;
    }

    private function getCompletePartyLedgerDetailsByGst($partyId, ?string $gstNo): ?array
    {
        $gstNo = $this->normalizeGstNo($gstNo);
        if ($gstNo === '') {
            return null;
        }
        $ledger = DB::table('LedgerMaster')
            ->selectRaw("strCustomerName AS party_name, GSTNo AS gst_no, LedgerAddress AS address, Pincode AS pincode, '' AS city, StateName AS state")
            ->where('iPartyId', $partyId)
            ->whereRaw("UPPER(REPLACE(GSTNo, ' ', '')) = ?", [$gstNo])
            ->first();

        if (!$ledger) {
            $ledger = DB::table('ledgers')
                ->selectRaw("name AS party_name, GstNo AS gst_no, CONCAT_WS(', ', NULLIF(AddressLine1, ''), NULLIF(AddressLine2, '')) AS address, Pincode AS pincode, City AS city, State AS state")
                ->where('iPartyId', $partyId)
                ->whereRaw("UPPER(REPLACE(GstNo, ' ', '')) = ?", [$gstNo])
                ->first();
        }

        if (!$ledger) {
            return null;
        }

        $details = $this->ledgerDetailsArray($ledger, 'gst_no');

        return $this->hasMinimumLedgerDetails($details) ? $details : null;
    }

    private function resolveUploadPartyLedgerDetails($partyId, array $row): ?array
    {
        return $this->getCompletePartyLedgerDetailsByGst($partyId, $row['gst_no'] ?? null)
            ?: $this->getCompletePartyLedgerDetails($partyId, $row['party_name'] ?? null);
    }

    private function isPartyLedgerAcceptedForUpload(?array $ledgerDetails, ?string $uploadedGstNo): bool
    {
        if (!$ledgerDetails) {
            return false;
        }

        if ($this->normalizeGstNo($uploadedGstNo) !== '') {
            return ($ledgerDetails['matched_by'] ?? null) === 'gst_no';
        }

        return ($ledgerDetails['matched_by'] ?? null) === 'party_name';
    }

    private function applyPartyLedgerDetails(array $row, ?array $ledgerDetails): array
    {
        if (!$ledgerDetails) {
            return $row;
        }
        if (($ledgerDetails['matched_by'] ?? null) === 'gst_no') {
            $row['party_name'] = $ledgerDetails['party_name'];
        }
        $row['gst_no'] = $row['gst_no'] ?: $ledgerDetails['gst_no'];
        $row['address'] = $row['address'] ?: $ledgerDetails['address'];
        $row['pincode'] = $row['pincode'] ?: $ledgerDetails['pincode'];
        $row['city'] = $row['city'] ?: $ledgerDetails['city'];
        $row['place_of_supply'] = $row['place_of_supply'] ?: $ledgerDetails['state'];

        return $row;
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

    private function storeNoItemLedgerWiseCustomGst(CreditNoteTransaction $transaction, array $noitemRows, array $customSlots, array $gstMapping, bool $isIgst): void
    {
        CreditNoteCustomGst::where('transaction_id', $transaction->id)->delete();

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
            } elseif ($slotMap->has(sprintf('%s|', $gstRate)) && $slotMap[sprintf('%s|', $gstRate)]->isNotEmpty()) {
                $slot = $slotMap[sprintf('%s|', $gstRate)]->shift();
            }

            $salesLedgerRow = !empty($ledgerId) ? Ledger::getLedgerById($transaction->iPartyId, $ledgerId) : null;
            $rowMapping = $salesLedgerRow ? $this->getGstMapping($transaction->iPartyId, $salesLedgerRow->name) : $gstMapping;
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

            CreditNoteCustomGst::create([
                'transaction_id' => $transaction->id,
                'gst_rate' => $gstRate,
                'taxable' => $rowAmount,
                'ledger_id' => $salesLedgerRow?->id ?? ($transaction->sales_ledger_id ?? null),
                'ledger_name' => $salesLedgerRow?->name ?? ($transaction->sales_ledger_name ?? null),
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
            $salesLedger = !empty($row['sales_ledger'])
                ? Ledger::getLedgerByName($partyId, $row['sales_ledger'])
                : null;
            $ledgerId = $salesLedger?->id;
            $key = $gstRate . '|' . ($ledgerId ?? trim((string) ($row['sales_ledger'] ?? '')));

            if (!isset($slots[$key])) {
                $mapping = $this->getGstMapping($partyId, $row['sales_ledger'] ?? null);

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

    private function amountsMatchCalculatedTotal(float $taxable, float $sgst, float $cgst, float $igst, $submittedTotal = null): bool
    {
        if ($submittedTotal === null || $submittedTotal === '') {
            return true;
        }

        $calculatedTotal = $this->roundCurrency($taxable + $sgst + $cgst + $igst);
        $submittedTotal = $this->roundCurrency($submittedTotal);

        return $calculatedTotal === $submittedTotal
            || $this->roundCurrency(round($calculatedTotal)) === $submittedTotal
            || $this->roundCurrency(ceil($calculatedTotal)) === $submittedTotal
            || $this->roundCurrency(floor($calculatedTotal)) === $submittedTotal;
    }

    private function hasMappedSalesLedgers(array $slots, $headerSalesLedger = null): bool
    {
        if (empty($headerSalesLedger)) {
            return false;
        }

        foreach ($slots as $slot) {
            if (($slot['amount'] ?? $slot['taxable'] ?? 0) > 0 && empty($slot['ledger_id'])) {
                return false;
            }
        }

        return true;
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
            ->where('iPartyId',$partyId)
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

    private function roundCurrency($value): float
    {
        return round((float) $value, 2);
    }

    private function calculateRoundOffAmount($amount, $sgst, $cgst, $igst, ?string $side = 'normal'): float
    {
        $grandTotal = $this->roundCurrency($amount)
            + $this->roundCurrency($sgst)
            + $this->roundCurrency($cgst)
            + $this->roundCurrency($igst);

        $roundedGrandTotal = match ($side) {
            'upper_side' => ceil($grandTotal),
            'lower_side' => floor($grandTotal),
            default => round($grandTotal),
        };

        return round($roundedGrandTotal - $grandTotal, 2);
    }

    private function calculateTotalAmountWithRoundOff($amount, $sgst, $cgst, $igst, ?string $side = 'normal'): float
    {
        $grandTotal = $this->roundCurrency($amount)
            + $this->roundCurrency($sgst)
            + $this->roundCurrency($cgst)
            + $this->roundCurrency($igst);
        $roundOff = $this->calculateRoundOffAmount($amount, $sgst, $cgst, $igst, $side);

        return round($grandTotal + $roundOff, 2);
    }

    // public function upload(Request $request)
    // {
    //     $iPartyId = session('iPartyId'); // same as sales

    //     if (!$iPartyId) {
    //         return back()->with('error', 'Please select company first');
    //     }

    //     $request->validate([
    //         'credit_notes_file' => 'required|mimes:xlsx,xls|max:30720'
    //     ]);

    //     $file = $request->file('credit_notes_file');
    //     $fileName = time() . '_' . $file->getClientOriginalName();
    //     $path = $file->storeAs('credit_note_uploads', $fileName, 'public');

    //     $upload = BulkCreditNoteUpload::create([
    //         'iPartyId' => $iPartyId,
    //         'batch_id' => Str::uuid(),
    //         'file_name' => $fileName,
    //         'file_path' => $path,
    //         'note_type' => 'credit',
    //         'uploaded_by' => $request->user_id,
    //         'uploaded_at' => now(),
    //         'status' => 'Processing'
    //     ]);

    //     // ✅ READ EXCEL WITH FORMULA SUPPORT
    //     $spreadsheet = IOFactory::load($file->getRealPath());
    //     $worksheet = $spreadsheet->getActiveSheet();

    //     $sheet = [];
    //     foreach ($worksheet->getRowIterator() as $row) {
    //         $rowData = [];
    //         foreach ($row->getCellIterator() as $cell) {
    //             $rowData[] = $this->getCellValue($cell); // same helper as sales
    //         }
    //         $sheet[] = $rowData;
    //     }

    //     $spreadsheet->disconnectWorksheets();
    //     unset($spreadsheet);

    //     $header = array_map(fn($h) => strtoupper(trim((string)$h)), $sheet[0]);
    //     $headerMap = array_flip($header);
    //     $isItemInvoice = in_array('NAME OF ITEM', $header);
    //     $isAccountingInvoice = in_array('PARTICULARS', $header);

    //     // ================= ITEM BASED =================
    //     if ($isItemInvoice) {

    //         $noteGroups = [];

    //         foreach ($sheet as $key => $row) {
    //             if ($key == 0) continue;
    //             if (empty(array_filter($row))) continue;

    //             $noteNo = trim($row[0]);

    //             $noteGroups[$noteNo][] = [
    //                 'date' => $this->parseDate($row[$headerMap['INVOICE DATE']] ?? null),
    //                 'gst_no' => $row[$headerMap['GST NO']] ?? '',
    //                 'party_name' => $row[$headerMap['PARTY A/C NAME']] ?? '',
    //                 'place_of_supply' => $row[$headerMap['PLACE OF SUPPLY']] ?? '',
    //                 'sales_ledger' => $row[$headerMap['SALES LEDGER']] ?? '',

    //                 // ✅ FIXED ITEM FIELDS
    //                 'item_name' => $row[$headerMap['NAME OF ITEM']] ?? '',
    //                 'qty' => $this->toNumber($row[$headerMap['QUANTITY']] ?? 0),
    //                 'rate' => $this->toNumber($row[$headerMap['RATE']] ?? 0),
    //                 'amount' => $this->toNumber($row[$headerMap['AMOUNT']] ?? 0),

    //                 'sgst' => $this->toNumber($row[$headerMap['SGST']] ?? 0),
    //                 'cgst' => $this->toNumber($row[$headerMap['CGST']] ?? 0),
    //                 'igst' => $this->toNumber($row[$headerMap['IGST']] ?? 0),

    //                 'total' => $this->toNumber($row[$headerMap['TOTAL AMOUNT']] ?? 0),
    //             ];
    //         }

    //         $total = 0;

    //         DB::transaction(function () use ($noteGroups, $upload, $iPartyId, &$total) {

    //             foreach ($noteGroups as $noteNo => $items) {

    //                 $sumAmount = array_sum(array_column($items, 'amount'));
    //                 $sumCgst = array_sum(array_column($items, 'cgst'));
    //                 $sumSgst = array_sum(array_column($items, 'sgst'));
    //                 $sumIgst = array_sum(array_column($items, 'igst'));
    //                 $sumTotal = array_sum(array_column($items, 'total'));

    //                 $first = $items[0];
    //                 $salesLedger = DB::table('LedgerMaster')
    //                     ->where('iPartyId',$iPartyId)
    //                     ->where('strCustomerName',$first['sales_ledger'])
    //                     ->first();

    //                 $mapping = $this->getGstMapping(
    //                     $iPartyId,
    //                     $first['sales_ledger']
    //                 );

    //                 $status = 'pending';
    //                 $is_igst = 0;

    //                 if($sumIgst > 0)
    //                 {
    //                     if(!empty($mapping['igst_id']))
    //                     {
    //                         $status = 'saved';
    //                     }

    //                     $is_igst = 1;
    //                 }
    //                 else
    //                 {
    //                     if(
    //                         !empty($mapping['cgst_id']) &&
    //                         !empty($mapping['sgst_id'])
    //                     )
    //                     {
    //                         $status = 'saved';
    //                     }

    //                     $is_igst = 0;
    //                 }
    //                 $rates = [];

    //                 foreach ($items as $item)
    //                 {
    //                     $gstRate = 0;

    //                     if($item['amount'] > 0)
    //                     {
    //                         $gstRate =
    //                         (
    //                             (
    //                                 $item['cgst']
    //                                 + $item['sgst']
    //                                 + $item['igst']
    //                             ) * 100
    //                         ) / $item['amount'];
    //                     }

    //                     $rates[] = round($gstRate,2);
    //                 }

    //                 $rates = array_unique(array_filter($rates));

    //                 $gstMode =
    //                     count($rates) > 1
    //                         ? 'custom'
    //                         : 'standard';

    //                 $gstRate =
    //                     count($rates)
    //                         ? reset($rates)
    //                         : 0;
    //                 $roundOffLedger = $this->getRoundOffLedger($iPartyId);

    //                 $transaction = CreditNoteTransaction::create([
    //                     'iPartyId'        => $iPartyId,
    //                     'upload_id'       => $upload->id,
    //                     'note_type'       => 'credit',

    //                     'note_no'         => $noteNo,
    //                     'note_date'       => $first['date'],
    //                     'gst_no'          => $first['gst_no'],
    //                     'party_name'      => $first['party_name'],
    //                     'place_of_supply' => $first['place_of_supply'],

    //                     'sales_ledger'    => $first['sales_ledger'], // 🔥 IMPORTANT
    //                     'vch_type'        => 'Credit Note',
    //                     'taxable_amount'  => $sumAmount,
    //                     'sgst'            => $sumSgst,
    //                     'cgst'            => $sumCgst,
    //                     'igst'            => $sumIgst,
    //                     'total_amount'    => $sumTotal,

    //                     'is_delete'       => 0,

    //                     'gst_mode'          => $gstMode,
    //                     'gst_rate'          => $gstRate,

    //                     'cgst_id'           => $mapping['cgst_id'],
    //                     'cgst_ledger_name'  => $mapping['cgst_name'],

    //                     'sgst_id'           => $mapping['sgst_id'],
    //                     'sgst_ledger_name'  => $mapping['sgst_name'],

    //                     'igst_id'           => $mapping['igst_id'],
    //                     'igst_ledger_name'  => $mapping['igst_name'],

    //                     'is_igst'           => $is_igst,

    //                     'status'            => $status,

    //                     'sales_ledger_id'   => $salesLedger?->iLedgerId,
    //                     'sales_ledger_name' => $salesLedger?->strCustomerName,

    //                     'roundoff_id'          => $roundOffLedger?->iLedgerId,
    //                     'roundoff_ledger_name' => $roundOffLedger?->strCustomerName,
    //                     'roundoff'             => round($sumTotal) - ($sumAmount + $sumCgst + $sumSgst + $sumIgst),
    //                 ]);

    //                 foreach ($items as $item) {
    //                     $itemMapping = $this->getGstMapping(
    //                         $iPartyId,
    //                         $first['sales_ledger'],
    //                         $item['item_name']
    //                     );

    //                     $itemRate = 0;

    //                     if($item['amount'] > 0)
    //                     {
    //                         $itemRate =
    //                         (
    //                             (
    //                                 $item['cgst']
    //                                 + $item['sgst']
    //                                 + $item['igst']
    //                             ) * 100
    //                         ) / $item['amount'];
    //                     }

    //                     CreditNoteTransactionItem::create([
    //                         'transaction_id' => $transaction->id,

    //                         'item_name'      => $item['item_name'],
    //                         'quantity'       => $item['qty'],
    //                         'rate'           => $item['rate'],

    //                         'gst_rate'       => round($itemRate,2),

    //                         'cgst_id'        => $itemMapping['cgst_id'],
    //                         'sgst_id'        => $itemMapping['sgst_id'],
    //                         'igst_id'        => $itemMapping['igst_id'],

    //                         'amount'         => $item['amount'],
    //                         'cgst'           => $item['cgst'],
    //                         'sgst'           => $item['sgst'],
    //                         'igst'           => $item['igst'],
    //                         'total_amount'   => $item['total'],
    //                     ]);
    //                     // CreditNoteTransactionItem::create([
    //                     //     'transaction_id' => $transaction->id,
    //                     //     'item_name' => $item['item_name'],
    //                     //     'quantity' => $item['qty'],
    //                     //     'rate' => $item['rate'],
    //                     //     'amount' => $item['amount'],
    //                     //     'cgst' => $item['cgst'],
    //                     //     'sgst' => $item['sgst'],
    //                     //     'igst' => $item['igst'],
    //                     //     'total_amount' => $item['total'],
    //                     // ]);
    //                 }

    //                 $total++;
    //             }
    //         });
    //         $savedCount = CreditNoteTransaction::where(
    //                 'upload_id',
    //                 $upload->id
    //             )
    //             ->where('status','saved')
    //             ->count();

    //         $pendingCount = $total - $savedCount;

    //         $upload->update([
    //             'total'   => $total,
    //             'saved'   => $savedCount,
    //             'pending' => $pendingCount,
    //             'status'  => $pendingCount > 0
    //                 ? 'pending'
    //                 : 'completed',
    //         ]);
    //         // $upload->update([
    //         //     'total' => $total,
    //         //     'pending' => $total,
    //         //     'status' => 'Pending'
    //         // ]);
    //     }

    //     // ================= ACCOUNTING =================
    //     elseif ($isAccountingInvoice)
    //     {
    //         $invoiceGroups = [];

    //         foreach ($sheet as $key => $row)
    //         {
    //             if ($key == 0) continue;
    //             if (empty(array_filter($row))) continue;

    //             $noteNo = trim($row[$headerMap['REFERANCE NO']] ?? '');

    //             $invoiceGroups[$noteNo][] = [
    //                 'note_no'         => $noteNo,
    //                 'date'            => $this->parseDate($row[$headerMap['INVOICE DATE']] ?? null),
    //                 'gst_no'          => $row[$headerMap['GST NO']] ?? '',
    //                 'party_name'      => $row[$headerMap['PARTY A/C NAME']] ?? '',
    //                 'place_of_supply' => $row[$headerMap['PLACE OF SUPPLY']] ?? '',
    //                 'sales_ledger'    => $row[$headerMap['PARTICULARS']] ?? '',

    //                 'amount'          => $this->toNumber($row[$headerMap['AMOUNT']] ?? 0),
    //                 'sgst'            => $this->toNumber($row[$headerMap['SGST']] ?? 0),
    //                 'cgst'            => $this->toNumber($row[$headerMap['CGST']] ?? 0),
    //                 'igst'            => $this->toNumber($row[$headerMap['IGST']] ?? 0),
    //                 'total_amount'    => $this->toNumber($row[$headerMap['TOTAL AMOUNT']] ?? 0),
    //             ];
    //         }
    //         $total = 0;
    //         DB::transaction(function () use ($invoiceGroups,$upload,$iPartyId,&$total)
    //         {
    //             foreach($invoiceGroups as $noteNo => $rows)
    //             {
    //                 $first = $rows[0];

    //                 $sumAmount = array_sum(array_column($rows,'amount'));
    //                 $sumSgst = array_sum(array_column($rows,'sgst'));
    //                 $sumCgst = array_sum(array_column($rows,'cgst'));
    //                 $sumIgst = array_sum(array_column($rows,'igst'));
    //                 $sumTotal = array_sum(array_column($rows,'total_amount'));
    //                 $isIgst = $sumIgst > 0;
    //                 $gstSlots = $this->buildSalesCustomGstSlots($rows, $iPartyId);
    //                 $rates = array_unique(array_filter(array_column($gstSlots,'gst_rate')));

    //                 $gstMode = count($rates) > 1 ? 'custom' : 'standard';

    //                 $gstRate = count($rates) ? reset($rates) : 0;
    //                 $mapping = $this->getGstMapping($iPartyId,$first['sales_ledger']);
    //                 $hasGstLedgers = $this->hasRequiredGstLedgers(
    //                     $gstMode === 'custom' ? $gstSlots : [[
    //                         'cgst_amount' => $sumCgst,
    //                         'sgst_amount' => $sumSgst,
    //                         'igst_amount' => $sumIgst,
    //                         'cgst_ledger_id' => $mapping['cgst_id'],
    //                         'sgst_ledger_id' => $mapping['sgst_id'],
    //                         'igst_ledger_id' => $mapping['igst_id'],
    //                     ]],
    //                     $isIgst
    //                 );
    //                 $status = $hasGstLedgers ? 'saved' : 'pending';
    //                 $transaction = CreditNoteTransaction::create([
    //                     'iPartyId' => $iPartyId,
    //                     'upload_id'=> $upload->id,
    //                     'note_type'=> 'credit',

    //                     'note_no'=> $noteNo,
    //                     'note_date'=> $first['date'],

    //                     'gst_no'=> $first['gst_no'],
    //                     'party_name'=> $first['party_name'],
    //                     'place_of_supply'=> $first['place_of_supply'],

    //                     'sales_ledger'=> $first['sales_ledger'],

    //                     'taxable_amount'=> $sumAmount,
    //                     'sgst'=> $sumSgst,
    //                     'cgst'=> $sumCgst,
    //                     'igst'=> $sumIgst,
    //                     'total_amount'=> $sumTotal,

    //                     'gst_mode'=> $gstMode,
    //                     'gst_rate'=> $gstRate,

    //                     'is_igst'           => $isIgst ? 1 : 0,
    //                     'isWithItem'        => 0,
    //                     'strYear'           => session('year'),
    //                     'year_from_date'    => session('year_from'),
    //                     'year_to_date'      => session('year_to'),
    //                     'status'            => $status,
    //                     'vchType'           => 'Credit Note',
    //                     'roundoff_id'          => $roundOffLedger?->iLedgerId,
    //                     'roundoff_ledger_name' => $roundOffLedger?->strCustomerName,
    //                     'roundoff'          => $this->calculateRoundOffAmount($sumAmount, $sumSgst, $sumCgst, $sumIgst, $roundOffSetting['side']),
    //                 ]);
    //                 if ($gstMode === 'custom')
    //                 {
    //                     foreach($gstSlots as $slot)
    //                     {
    //                         CreditNoteCustomGst::create([
    //                             'transaction_id' => $transaction->id,

    //                             'gst_rate' => $slot['gst_rate'],
    //                             'taxable' => $slot['taxable'],

    //                             'ledger_id' => $slot['ledger_id'],
    //                             'ledger_name' => $slot['ledger_name'],

    //                             'amount' => $slot['amount'],

    //                             'cgst_ledger_id' => $slot['cgst_ledger_id'],
    //                             'cgst_ledger_name' => $slot['cgst_ledger_name'],
    //                             'cgst_amount' => $slot['cgst_amount'],

    //                             'sgst_ledger_id' => $slot['sgst_ledger_id'],
    //                             'sgst_ledger_name' => $slot['sgst_ledger_name'],
    //                             'sgst_amount' => $slot['sgst_amount'],

    //                             'igst_ledger_id' => $slot['igst_ledger_id'],
    //                             'igst_ledger_name' => $slot['igst_ledger_name'],
    //                             'igst_amount' => $slot['igst_amount'],
    //                         ]);
    //                     }
    //                 }
    //                 $total++;
    //             }
    //         });
    //         $savedCount = CreditNoteTransaction::where('upload_id',$upload->id)
    //             ->where('status','saved')
    //             ->count();

    //         $pendingCount = $total - $savedCount;

    //         $upload->update([
    //             'total'   => $total,
    //             'saved'   => $savedCount,
    //             'pending' => $pendingCount,
    //             'status'  => $pendingCount > 0 ? 'pending' : 'completed',
    //         ]);
    //     }

    //     return back()->with('success', 'Credit Notes Uploaded Successfully');
    // }

    public function upload(Request $request)
    {
        $iPartyId = session('iPartyId');
        
        if (!$iPartyId) {
            return back()->with('error', 'Please select company first');
        }

        $request->validate([
            'credit_notes_file' => 'required|mimes:xlsx,xls|max:30720'
        ]);

        $file = $request->file('credit_notes_file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('credit_note_uploads', $fileName, 'public');

        $upload = BulkCreditNoteUpload::create([
            'iPartyId' => $iPartyId,
            'batch_id' => Str::uuid(),
            'file_name' => $fileName,
            'file_path' => $path,
            'note_type' => 'credit',
            'uploaded_by' => $request->user_id,
            'uploaded_at' => now(),
            'status' => 'Processing'
        ]);

        // READ EXCEL WITH FORMULA SUPPORT
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

        // Normalise header (like SalesController)
        $header = array_map(fn($h) => strtoupper(trim((string)$h)), $sheet[0]);
        $isItemInvoice = in_array('NAME OF ITEM', $header);
        $isAccountingInvoice = in_array('PARTICULARS', $header);

        // ================= ITEM BASED =================
        if ($isItemInvoice) {
            $noteGroups = [];
            
            foreach ($sheet as $key => $row) {
                if ($key === 0) continue;
                if (empty(array_filter($row))) continue;
                if (empty($row[0]) || empty($row[1]) || empty($row[3])) continue;
                
                $noteNo = trim((string) $row[0]);
                $date = $this->parseDate($row[1]);
                $partyName = $row[3] ?? '';

                $groupKey = $noteNo.'|'.$partyName.'|'.$date;
                $noteGroups[$groupKey][] = [
                    'date' => $date,
                    'gst_no' => $row[2] ?? null,
                    'party_name' => $row[3] ?? null,
                    'place_of_supply' => $row[4] ?? null,
                    'address' => null,
                    'pincode' => null,
                    'city' => null,
                    'sales_ledger' => $row[5] ?? null,
                    'item_name' => $row[6] ?? null,
                    'quantity' => $this->toNumber($row[7]),
                    'rate' => $this->toNumber($row[8]),
                    'amount' => $this->toNumber($row[9]),
                    'sgst' => $this->toNumber($row[10]),
                    'cgst' => $this->toNumber($row[11]),
                    'igst' => $this->toNumber($row[12]),
                    'total_amount' => $this->toNumber($row[13]),
                ];
            }
            
            $totalInvoices = 0;
            
            DB::transaction(function () use ($noteGroups, $upload, $iPartyId, &$totalInvoices) {
                foreach ($noteGroups as $groupKey => $items) {
                    // Aggregate totals across all item lines
                    $sumAmount = array_sum(array_column($items, 'amount'));
                    $sumSgst = array_sum(array_column($items, 'sgst'));
                    $sumCgst = array_sum(array_column($items, 'cgst'));
                    $sumIgst = array_sum(array_column($items, 'igst'));
                    $sumTotalAmount = array_sum(array_column($items, 'total_amount'));
                    $partyLedgerDetails = $this->resolveUploadPartyLedgerDetails($iPartyId, $items[0]);
                    $first = $this->applyPartyLedgerDetails($items[0], $partyLedgerDetails);
                    $partyLedgerMatched = $this->isPartyLedgerAcceptedForUpload($partyLedgerDetails, $items[0]['gst_no'] ?? null);
                    
                    $salesLedger = DB::table('LedgerMaster')
                        ->where('iPartyId', $iPartyId)
                        ->where('strCustomerName', $first['sales_ledger'])
                        ->first();

                    $mapping = $this->getGstMapping(
                        $iPartyId,
                        $first['sales_ledger']
                    );

                    // Calculate GST rates for each item to determine mode
                    $rates = [];
                    $amountMatched = true;
                    foreach ($items as $item) {
                        $gstRate = 0;
                        if ($item['amount'] > 0) {
                            $gstRate = (($item['cgst'] + $item['sgst'] + $item['igst']) * 100) / $item['amount'];
                        }
                        $rates[] = round($gstRate, 2);

                        $calculatedAmount = round((float)$item['quantity'] * (float)$item['rate'],2);
                        if($calculatedAmount != round((float)$item['amount'],2))
                        {
                            $amountMatched = false;
                            break;
                        }
                    }
                    
                    $rates = array_unique(array_filter($rates));
                    $gstMode = count($rates) > 1 ? 'custom' : 'standard';
                    $gstRate = count($rates) ? reset($rates) : 0;
                    
                    $status = 'pending';
                   
                    
                    $is_igst = 0;
                    if($amountMatched)
                    {
                        if ($sumIgst > 0) {
                            if (!empty($mapping['igst_id'])) {
                                $status = 'saved';
                            }
                            $is_igst = 1;
                        } else {
                            if (!empty($mapping['cgst_id']) && !empty($mapping['sgst_id'])) {
                                $status = 'saved';
                            }
                            $is_igst = 0;
                        }
                    }

                    $noteNo = explode('|', $groupKey)[0];
                    if (!$partyLedgerMatched || empty($salesLedger) || !$this->hasOnlyValidGstSlabs($rates) || $this->creditNoteVoucherExists($iPartyId, 'Credit Note', $noteNo, session('year'))) {
                        $status = 'pending';
                    }
                    
                    $roundOffSetting = $this->getRoundOffSetting($iPartyId);
                    $roundOffLedger = $roundOffSetting['ledger'];

                    $transaction = CreditNoteTransaction::create([
                        'iPartyId' => $iPartyId,
                        'upload_id' => $upload->id,
                        'note_type' => 'credit',
                        'note_no' => $noteNo,
                        'note_date' => $first['date'],
                        'gst_no' => $first['gst_no'],
                        'party_name' => $first['party_name'],
                        'place_of_supply' => $first['place_of_supply'],
                        'address' => $first['address'] ?? null,
                        'pincode' => $first['pincode'] ?? null,
                        'city' => $first['city'] ?? null,
                        'sales_ledger' => $first['sales_ledger'],
                        'sales_ledger_id' => $salesLedger?->iLedgerId,
                        'sales_ledger_name' => $salesLedger?->strCustomerName,
                        'cgst_id' => $mapping['cgst_id'],
                        'cgst_ledger_name' => $mapping['cgst_name'],
                        'sgst_id' => $mapping['sgst_id'],
                        'sgst_ledger_name' => $mapping['sgst_name'],
                        'igst_id' => $mapping['igst_id'],
                        'igst_ledger_name' => $mapping['igst_name'],
                        'gst_mode' => $gstMode,
                        'gst_rate' => $gstRate,
                        'is_igst' => $is_igst,
                        'isWithItem' => 1,
                        'strYear' => session('year'),
                        'year_from_date' => session('year_from'),
                        'year_to_date' => session('year_to'),
                        'taxable_amount' => $sumAmount,
                        'sgst' => $sumSgst,
                        'cgst' => $sumCgst,
                        'igst' => $sumIgst,
                        // 'total_amount' => $sumTotalAmount,
                        'total_amount' => $this->calculateTotalAmountWithRoundOff($sumAmount, $sumSgst, $sumCgst, $sumIgst, $roundOffSetting['side']),
                        'status' => $status,
                        'vch_type' => 'Credit Note',
                        'roundoff_id' => $roundOffLedger?->iLedgerId,
                        'roundoff_ledger_name' => $roundOffLedger?->strCustomerName,
                        'roundoff' => $this->calculateRoundOffAmount($sumAmount, $sumSgst, $sumCgst, $sumIgst, $roundOffSetting['side']),
                    ]);
                    
                    // Create items
                    foreach ($items as $item) {
                        $itemMapping = $this->getGstMapping(
                            $iPartyId,
                            $first['sales_ledger'],
                            $item['item_name']
                        );
                        
                        $itemRate = 0;
                        if ($item['amount'] > 0) {
                            $itemRate = (($item['cgst'] + $item['sgst'] + $item['igst']) * 100) / $item['amount'];
                        }
                        $stockItem = DB::table('StockItemMaster')
                            ->where('iPartyId',$iPartyId)
                            ->where('strItemName',$item['item_name'])
                            ->first();
                        CreditNoteTransactionItem::create([
                            'transaction_id' => $transaction->id,
                            'item_name' => $item['item_name'],
                            'quantity' => $item['quantity'],
                            'rate' => $item['rate'],
                            'gst_rate' => round($itemRate, 2),
                            'unit'              => $stockItem->strBaseUnits ?? 'NOS',
                            'cgst_id' => $itemMapping['cgst_id'],
                            'sgst_id' => $itemMapping['sgst_id'],
                            'igst_id' => $itemMapping['igst_id'],
                            'amount' => $item['amount'],
                            'sgst' => $item['sgst'],
                            'cgst' => $item['cgst'],
                            'igst' => $item['igst'],
                            'total_amount' => $item['total_amount'],
                        ]);
                    }
                    $totalInvoices++;
                }
            });
            
            $savedCount = CreditNoteTransaction::where('upload_id', $upload->id)
                ->where('status', 'saved')
                ->count();
                
            $pendingCount = $totalInvoices - $savedCount;
            
            $upload->update([
                'total' => $totalInvoices,
                'saved' => $savedCount,
                'pending' => $pendingCount,
                'status' => $pendingCount > 0 ? 'pending' : 'completed',
            ]);
        }
        // ================= ACCOUNTING INVOICE =================
        // ================= ACCOUNTING INVOICE =================
        elseif ($isAccountingInvoice) {
            
            $noteGroups = [];
            foreach ($sheet as $key => $row) {
                if ($key === 0) continue;
                if (empty(array_filter($row))) continue;
                if (empty($row[0]) || empty($row[1]) || empty($row[3])) continue;
                
                $noteNo = trim((string) $row[0]);
                $date      = $this->parseDate($row[1] ?? null);
                $partyName = $row[3] ?? '';

                $groupKey = $noteNo.'|'.$partyName.'|'.$date;
                $noteGroups[$groupKey][] = [
                    'note_no'         => $noteNo,
                    'date'            => $this->parseDate($row[1]),
                    'gst_no'          => $row[2] ?? null,
                    'party_name'      => $row[3] ?? null,
                    'place_of_supply' => $row[4] ?? null,
                    'address'         => null,
                    'pincode'         => null,
                    'city'            => null,
                    'sales_ledger'    => $row[5] ?? null,
                    'amount'          => $this->toNumber($row[6] ?? 0),
                    'sgst'            => $this->toNumber($row[7] ?? 0),
                    'cgst'            => $this->toNumber($row[8] ?? 0),
                    'igst'            => $this->toNumber($row[9] ?? 0),
                    'total_amount'    => $this->toNumber($row[10] ?? 0),
                ];
            }

            $total = 0;

            DB::transaction(function () use ($noteGroups, $iPartyId, $upload, &$total) {
                foreach ($noteGroups as $groupKey => $rows) {
                    $partyLedgerDetails = $this->resolveUploadPartyLedgerDetails($iPartyId, $rows[0]);
                    $first = $this->applyPartyLedgerDetails($rows[0], $partyLedgerDetails);
                    $partyLedgerMatched = $this->isPartyLedgerAcceptedForUpload($partyLedgerDetails, $rows[0]['gst_no'] ?? null);
                    
                    // Calculate totals
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

                    // Build custom GST slots from individual rows
                    $gstSlots = [];
                    foreach ($rows as $row) {
                        $amount = $row['amount'];
                        $sgst = $row['sgst'];
                        $cgst = $row['cgst'];
                        $igst = $row['igst'];
                        $gstRate = $amount > 0
                            ? round((($sgst + $cgst + $igst) * 100) / $amount, 2)
                            : 0;
                        
                        // Get mapping for this specific row's sales ledger
                        $rowMapping = $this->getGstMapping($iPartyId, $row['sales_ledger'] ?? $first['sales_ledger']);
                        
                        // Get sales ledger object for this row
                        $rowSalesLedger = null;
                        if (!empty($row['sales_ledger'])) {
                            $rowSalesLedger = Ledger::getLedgerByName($iPartyId, $row['sales_ledger']);
                        }
                        
                        // Use row's sales ledger or fallback to first row's sales ledger
                        $ledgerId = $rowSalesLedger ? ($rowSalesLedger->id ?? $rowSalesLedger->iLedgerId ?? null) : ($salesLedger->iLedgerId ?? null);
                        $ledgerName = $rowSalesLedger ? ($rowSalesLedger->name ?? $rowSalesLedger->strCustomerName ?? null) : ($salesLedger->strCustomerName ?? null);
                        
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
                    // (use the first non-zero rate or 0)
                    $gstRate = 0;
                    foreach ($gstSlots as $slot) {
                        if ($slot['gst_rate'] > 0) {
                            $gstRate = $slot['gst_rate'];
                            break;
                        }
                    }
                    
                    $mapping = $this->getGstMapping($iPartyId, $first['sales_ledger']);
                    
                    $amountMatched = $this->amountsMatchCalculatedTotal($sumAmount, $sumSgst, $sumCgst, $sumIgst, $sumTotalAmount);
                    $status = ($this->hasMappedSalesLedgers($gstSlots, $salesLedger) && $this->hasRequiredGstLedgers($gstSlots, $isIgst) && $amountMatched) ? 'saved' : 'pending';
                    $noteNo = $first['note_no'];
                    if (!$partyLedgerMatched || !$this->hasOnlyValidGstSlabs(array_column($gstSlots, 'gst_rate')) || $this->creditNoteVoucherExists($iPartyId, 'Credit Note', $noteNo, session('year'))) {
                        $status = 'pending';
                    }
                    $roundOffSetting = $this->getRoundOffSetting($iPartyId);
                    $roundOffLedger = $roundOffSetting['ledger'];

                    $transaction = CreditNoteTransaction::create([
                        'iPartyId'          => $iPartyId,
                        'upload_id'         => $upload->id,
                        'note_type'         => 'credit',
                        
                        'note_no'           => $noteNo,
                        'note_date'         => $first['date'],
                        
                        'gst_no'            => $first['gst_no'],
                        'party_name'        => $first['party_name'],
                        'place_of_supply'   => $first['place_of_supply'],
                        'address'           => $first['address'] ?? null,
                        'pincode'           => $first['pincode'] ?? null,
                        'city'              => $first['city'] ?? null,
                        
                        'sales_ledger'      => $first['sales_ledger'],
                        'sales_ledger_id'   => $salesLedger?->iLedgerId,
                        'sales_ledger_name' => $salesLedger?->strCustomerName,
                        
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
                        
                        'taxable_amount'    => $sumAmount,
                        'sgst'              => $sumSgst,
                        'cgst'              => $sumCgst,
                        'igst'              => $sumIgst,
                        // 'total_amount'      => $sumTotalAmount,
                        'total_amount'      => $this->calculateTotalAmountWithRoundOff($sumAmount, $sumSgst, $sumCgst, $sumIgst, $roundOffSetting['side']),
                        'status'            => $status,
                        'vch_type'          => 'Credit Note',
                        
                        'roundoff_id'          => $roundOffLedger?->iLedgerId,
                        'roundoff_ledger_name' => $roundOffLedger?->strCustomerName,
                        'roundoff'          => $this->calculateRoundOffAmount($sumAmount, $sumSgst, $sumCgst, $sumIgst, $roundOffSetting['side']),
                    ]);

                    // Create custom GST slots - ONE PER ROW
                    foreach ($gstSlots as $slot) {
                        if (($slot['igst_amount'] ?? 0) != 0 || 
                            ($slot['cgst_amount'] ?? 0) != 0 || 
                            ($slot['sgst_amount'] ?? 0) != 0) {
                            
                            CreditNoteCustomGst::create([
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
            
            $savedCount = CreditNoteTransaction::where('upload_id', $upload->id)
                ->where('status', 'saved')
                ->count();

            $pendingCount = $total - $savedCount;

            $upload->update([
                'total'   => $total,
                'saved'   => $savedCount,
                'pending' => $pendingCount,
                'status'  => $pendingCount > 0 ? 'pending' : 'completed',
            ]);
        }
        
        return back()->with('success', 'Credit Notes Uploaded Successfully');
    }

    public function preview($id)
    {
        $iPartyId = session('iPartyId'); // same as sales

        if (!$iPartyId) {
            return redirect()->route('cn.upload')
                ->with('error', 'Please select company first');
        }

        // ✅ Fetch pending credit notes (same like sales)
        $rows = CreditNoteTransaction::where('upload_id', $id)
            ->where('status', 'pending') // or 'pending' if using string
            ->where('iPartyId', $iPartyId)
            ->paginate(50);

        // ✅ Voucher Types
        $vchTypes = DB::table('VchHistory')
            ->where('iPartyId', $iPartyId)
            ->where('vchType', 'Credit Note')
            ->distinct()
            ->pluck('vchType');
        
        $vchTypes = $vchTypes->isEmpty()
            ? collect(['Credit Note'])
            : $vchTypes;

        // ✅ States
        $states = DB::table('state')
            ->pluck('stateName');

        // ✅ Groups
        $groups = DB::table('GroupMaster')
            ->where('iPartyId', $iPartyId)
            ->distinct()
            ->pluck('strGroupName');

        // ✅ Party Ledgers (Sundry Debtors)
        $parents = DB::table('LedgerMaster')
            ->select('strParents')
            ->where('iPartyId', $iPartyId)
            ->where('strParents', 'Sundry Debtors')
            ->distinct()
            ->get();

        $ledgers = Ledger::getAllDebtorsLedgers($iPartyId);

        // ✅ GST Ledgers
        $iGstLedgers = Ledger::getAlliGstLedgers($iPartyId);
        $cGstLedgers = Ledger::getAllcGstLedgers($iPartyId);
        $sGstLedgers = Ledger::getAllsGstLedgers($iPartyId);

        // ✅ SALES RETURN LEDGER (IMPORTANT 🔥)
        $salesLedgers = Ledger::getSalesLedgers($iPartyId);
        $stockItems = DB::table('StockItemMaster')
            ->select('*', 'CGSTLedgerId as cgst_id', 'SGSTLedgerId as sgst_id', 'IGSTLedgerId as igst_id')
            ->where('iPartyId', $iPartyId)
            ->orderBy('strItemName', 'asc') // optional (recommended)
            ->get();
        $salesGstMappings = $this->getSalesLedgerGstMappings($iPartyId);
        $ledgerDetails = $ledgers;
        $roundOffSide = $this->getRoundOffSetting($iPartyId)['side'];
        return view('admin.bulkupload.credit_note.preview', compact(
            'rows',
            'ledgers',
            'vchTypes',
            'groups',
            'states',
            'parents',
            'iGstLedgers',
            'cGstLedgers',
            'sGstLedgers',
            'salesLedgers',
            'stockItems',
            'salesGstMappings',
            'ledgerDetails'
        ));
    }

    public function storeLedger(Request $request)
    {
        $iPartyId = session('iPartyId');
        if (!$iPartyId) {
            return redirect()->route('cn.index')
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

        foreach ($request->selected as $key => $id) {

            $row = CreditNoteTransaction::find($id);
            if (!$row) continue;

            $uploadId = $row->upload_id;
            $voucherType = $request->voucher_type[$id] ?? $row->vch_type;
            $voucherNo = $request->note_no[$id] ?? $request->invoice_no[$id] ?? $row->note_no;
            if ($this->creditNoteVoucherExists($row->iPartyId, $voucherType, $voucherNo, $row->strYear ?? session('year'), $row->id)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Duplicate voucher found for the selected VnchType, VnchNo, and Year.'
                ], 422);
            }
            $partyName = $request->party_name[$id] ?: ($request->ledger[$id] ?? $row->party_name);
            $salesLedgerName = $request->sales_ledger[$id] ?? $row->sales_ledger;
            $partyLedgerDetails = $this->resolveUploadPartyLedgerDetails($row->iPartyId, [
                'gst_no' => $row->gst_no,
                'party_name' => $partyName,
            ]);
            $partyLedgerMatched = $this->isPartyLedgerAcceptedForUpload($partyLedgerDetails, $row->gst_no);
            $salesLedger = $salesLedgerName ? Ledger::getLedgerByName($row->iPartyId, $salesLedgerName) : null;
            $gstLedgersMatched = $row->is_igst == 1
                ? ((float) $row->igst <= 0 || !empty($row->igst_id))
                : (((float) $row->cgst <= 0 || !empty($row->cgst_id)) && ((float) $row->sgst <= 0 || !empty($row->sgst_id)));
            $gstRatesValid = $this->hasOnlyValidGstSlabs([$row->gst_rate]);
            $amountMatched = $this->amountsMatchCalculatedTotal((float) $row->taxable_amount, (float) $row->sgst, (float) $row->cgst, (float) $row->igst, $row->total_amount);
            $rowStatus = ($partyLedgerMatched && !empty($salesLedger) && $gstLedgersMatched && $gstRatesValid && $amountMatched) ? 'saved' : 'pending';

            $row->update([
                'note_no'         => $request->note_no[$id] ?? $request->invoice_no[$id] ?? $row->note_no,
                'note_date'       => $request->note_date[$id] ?? $request->date[$id] ?? $row->note_date,
                'party_name'      => $partyName,
                'place_of_supply' => $request->place_of_supply[$id] ?? $row->place_of_supply,

                // 🔥 IMPORTANT FOR CREDIT NOTE
                'sales_ledger' => $salesLedgerName,
                'sales_ledger_id' => $salesLedger->id ?? $salesLedger->iLedgerId ?? $row->sales_ledger_id,
                'sales_ledger_name' => $salesLedger->name ?? $salesLedger->strCustomerName ?? $row->sales_ledger_name,
                'remarks'         => $request->remarks[$id] ?? $row->remarks,

                'status'          => $rowStatus,
                'vch_type'        => $request->voucher_type[$id] ?? 'Credit Note'
            ]);
        }

        // ===============================
        // UPDATE BULK COUNTS
        // ===============================
        if ($uploadId) {

            $saved = CreditNoteTransaction::where('upload_id', $uploadId)
                ->where('status', 'saved')
                ->where('is_delete', 0)
                ->count();

            $pending = CreditNoteTransaction::where('upload_id', $uploadId)
                ->where('status', 'pending')
                ->where('is_delete', 0)
                ->count();

            $total = CreditNoteTransaction::where('upload_id', $uploadId)
                ->where('is_delete', 0)
                ->count();

            $status = ($pending == 0) ? 'completed' : 'pending';

            BulkCreditNoteUpload::where('id', $uploadId)->update([
                'total'     => $total,
                'saved' => $saved,
                'pending'    => $pending,
                'status'         => $status
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Credit Notes Saved Successfully'
        ]);
    }

    public function update(Request $request)
    {
        $request->merge([
            'note_no' => $request->input('note_no', $request->input('invoice_no')),
            'note_date' => $request->input('note_date', $request->input('date')),
            'vch_type' => $request->input('vch_type', $request->input('vchType')),
            'remarks' => $request->input('remarks', $request->input('Remarks')),
            'sales_ledger_name' => $request->input('sales_ledger_name', $request->input('sales_ledger')),
        ]);
        $data = $request->validate([
            'id' => 'required|integer',
            'note_no' => 'nullable|string',
            'note_date' => 'nullable|date',
            'party_name' => 'nullable|string',
            'gst_no' => 'nullable|string',
            'place_of_supply' => 'nullable|string',
            'remarks' => 'nullable|string',
            'sales_ledger_name' => 'nullable|string',
            'vch_type' => 'nullable|string',
            'is_igst' => 'nullable|numeric',
            'gst_mode' => 'nullable|string',

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
            'custom_slots.*.sales_ledger_id' => 'nullable',
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
        $invoiceDate = $request->note_date;

        if ($invoiceDate && ($invoiceDate < session('year_from') || $invoiceDate > session('year_to'))) {
            return response()->json([
                'status' => false,
                'message' => 'Invoice date must be within selected financial year'
            ]);
        }
        try {
            DB::transaction(function () use ($data, $request) {

            $transaction = CreditNoteTransaction::findOrFail($data['id']);
            $voucherType = $request->vch_type ?? $transaction->vch_type;
            $voucherNo = $request->note_no ?? $transaction->note_no;
            if ($this->creditNoteVoucherExists($transaction->iPartyId, $voucherType, $voucherNo, session('year'), $transaction->id)) {
                throw new \InvalidArgumentException('Duplicate voucher found for the selected VnchType, VnchNo, and Year.');
            }

            // ===============================
            // SALES LEDGER
            // ===============================
            $salesLedgerName = isset($request['sales_ledger_name']) && $request['sales_ledger_name'] != "Select Ledger" ? $request['sales_ledger_name'] : $transaction->sales_ledger;
            // $request->sales_ledger_name ?: $transaction->sales_ledger_name;

            $salesLedger = $salesLedgerName
                ? Ledger::getLedgerByName($transaction->iPartyId, $salesLedgerName)
                : null;

            // ===============================
            // HEADER UPDATE (SAFE)
            // ===============================
            $transaction->update([
                'note_no' => $request->note_no ?: $transaction->note_no,
                //'against_invoice' => $request->invoice_no ?: $transaction->invoice_no,
                'against_invoice' => $request->against_invoice,
                'note_date' => $request->note_date ?: $transaction->note_date,
                'party_name' => $request->party_name ?: $transaction->party_name,
                //'party_ledger' => $request->party_name ?: $transaction->party_name,
                'gst_no' => $request->gst_no ?: $transaction->gst_no,
                'place_of_supply' => $request->place_of_supply ?: $transaction->place_of_supply,
                'address'    => $request->address ?: $transaction->address,
                'pincode'    => $request->pincode ?: $transaction->pincode,
                'city'  => $request->city ?: $transaction->city,
                'remarks' => $request->remarks ?? $transaction->remarks,
                'vch_type' => $request->vch_type ?? $transaction->vch_type,

                'is_igst' => $request->is_igst ?? $transaction->is_igst,
                'gst_mode' => $request->gst_mode ?? $transaction->gst_mode ?? 'standard',

                'sales_ledger_id' => $salesLedger->id ?? $transaction->sales_ledger_id,
                'sales_ledger_name' => $salesLedger->name ?? $transaction->sales_ledger_name,
                // 'party_ledger'    => isset($request['sales_ledger_name']) && $request['sales_ledger_name'] != "Select Ledger" ? $request['sales_ledger_name'] : $transaction->sales_ledger,
                'strYear'       => session('year'),
                'year_from_date' => session('year_from'),
                'year_to_date'  => session('year_to'),
                'isWithItem'    => $request->entry_mode == 'noitem' ? 0 : 1,
                'gst_rate'      => $request->gst_rate ?? 0
            ]);

            $gstMode = $transaction->gst_mode;
            $gstMapping = $this->getGstMapping(
                $transaction->iPartyId,
                $salesLedger->name ?? $salesLedgerName,
                $this->firstItemNameFromRequest($request)
            );

            // ===============================
            // ITEMS HANDLING
            // ===============================
            $submittedIds = [];
            $sumAmount = $sumCgst = $sumSgst = $sumIgst = $sumSubmittedTotal = 0;

            if (!empty($data['items'])) {
                CreditNoteTransactionItem::where('transaction_id', $transaction->id)
                    ->delete();
                foreach ($data['items'] as $itemData) {
                    if (isset($itemData['item_name'])) {
                        $itemData['item_name'] = trim((string) $itemData['item_name']);
                    }

                    $itemId = $itemData['id'] ?? null;
                    $itemData['transaction_id'] = $transaction->id;

                    if ($itemId) {
                        $item = CreditNoteTransactionItem::find($itemId);

                        if ($item && $item->transaction_id == $transaction->id) {
                            $item->update($itemData);
                            $submittedIds[] = $itemId;
                        } else {
                            $itemId = null;
                        }
                    }

                    if (!$itemId) {
                        CreditNoteTransactionItem::create($itemData);
                    }

                    $sumAmount += (float)($itemData['amount'] ?? 0);
                    $sumCgst   += (float)($itemData['cgst'] ?? 0);
                    $sumSgst   += (float)($itemData['sgst'] ?? 0);
                    $sumIgst   += (float)($itemData['igst'] ?? 0);
                    $sumSubmittedTotal += (float)($itemData['total_amount'] ?? 0);
                }

                
            } else {

                if (!empty($request->noitem_rows)) {
                    foreach ($request->noitem_rows as $row) {
                        $amount = (float)($row['amount'] ?? 0);
                        $gstRate = (float)($row['gst'] ?? 0);
                        $gstAmount = ($amount * $gstRate) / 100;

                        $sumAmount += $amount;
                        if ($transaction->is_igst == 1) {
                            $sumIgst += $this->roundCurrency($gstAmount);
                        } else {
                            $sumCgst += $this->roundCurrency(($amount * $gstRate) / 200);
                            $sumSgst += $this->roundCurrency(($amount * $gstRate) / 200);
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

                CreditNoteTransactionItem::where('transaction_id', $transaction->id)->delete();
            }

            // ===============================
            // GST LEDGER (STANDARD)
            // ===============================
            if ($gstMode === 'standard') {

                $transaction->igst_id = $request->igst_ledger ?: ($gstMapping['igst_id'] ?? $transaction->igst_id);
                $transaction->cgst_id = $request->cgst_ledger ?: ($gstMapping['cgst_id'] ?? $transaction->cgst_id);
                $transaction->sgst_id = $request->sgst_ledger ?: ($gstMapping['sgst_id'] ?? $transaction->sgst_id);

                $transaction->igst_ledger_name = optional(Ledger::getLedgerById($transaction->iPartyId, $transaction->igst_id))->name;
                $transaction->cgst_ledger_name = optional(Ledger::getLedgerById($transaction->iPartyId, $transaction->cgst_id))->name;
                $transaction->sgst_ledger_name = optional(Ledger::getLedgerById($transaction->iPartyId, $transaction->sgst_id))->name;
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
            // ✅ CUSTOM GST (CREDIT)
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

                CreditNoteCustomGst::where('transaction_id', $transaction->id)->delete();

                foreach ($request->custom_slots as $slot) {

                    if (
                        ($slot['igst_amount'] ?? 0) != 0 ||
                        ($slot['cgst_amount'] ?? 0) != 0 ||
                        ($slot['sgst_amount'] ?? 0) != 0
                    ) {
                        $slot = $this->applyGstMappingToCustomSlot($slot, $gstMapping);
                        $slotSalesLedger = !empty($slot['sales_ledger_id'])
                            ? Ledger::getLedgerById($transaction->iPartyId, $slot['sales_ledger_id'])
                            : null;

                        CreditNoteCustomGst::create([
                            'transaction_id' => $transaction->id,

                            'gst_rate' => $slot['rate'] ?? 0,
                            'taxable'  => $slot['taxable'] ?? 0,
                            'ledger_id' => $slotSalesLedger?->id,
                            'ledger_name' => $slotSalesLedger?->name,

                            'igst_ledger_id'   => $slot['igst_ledger_id'] ?? null,
                            'igst_ledger_name' => optional(Ledger::getLedgerById($transaction->iPartyId, $slot['igst_ledger_id']))->name,
                            'igst_amount'      => $slot['igst_amount'] ?? 0,

                            'cgst_ledger_id'   => $slot['cgst_ledger_id'] ?? null,
                            'cgst_ledger_name' => optional(Ledger::getLedgerById($transaction->iPartyId, $slot['cgst_ledger_id']))->name,
                            'cgst_amount'      => $slot['cgst_amount'] ?? 0,

                            'sgst_ledger_id'   => $slot['sgst_ledger_id'] ?? null,
                            'sgst_ledger_name' => optional(Ledger::getLedgerById($transaction->iPartyId, $slot['sgst_ledger_id']))->name,
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
            $partyLedgerDetails = $this->resolveUploadPartyLedgerDetails($transaction->iPartyId, [
                'gst_no' => $request->gst_no ?: $transaction->gst_no,
                'party_name' => $request->party_name ?: $transaction->party_name,
            ]);
            $partyLedgerMatched = $this->isPartyLedgerAcceptedForUpload($partyLedgerDetails, $request->gst_no ?: $transaction->gst_no);
            $gstRatesValid = $this->hasOnlyValidGstSlabs($this->extractCreditNoteRequestGstRates($request, $sumAmount, $sumCgst, $sumSgst, $sumIgst));
            $submittedTotal = $sumSubmittedTotal > 0 ? $sumSubmittedTotal : $request->input('total_amount');
            $amountMatched = $this->amountsMatchCalculatedTotal($sumAmount, $sumSgst, $sumCgst, $sumIgst, $submittedTotal);
            $gstLedgersMatched = true;
            $salesLedgersMatched = !empty($salesLedger);

            if ($transaction->gst_mode === 'custom') {
                $customSlots = CreditNoteCustomGst::where('transaction_id', $transaction->id)->get()->map(fn ($slot) => [
                    'amount' => (float) $slot->amount,
                    'ledger_id' => $slot->ledger_id,
                    'igst_amount' => (float) $slot->igst_amount,
                    'igst_ledger_id' => $slot->igst_ledger_id,
                    'cgst_amount' => (float) $slot->cgst_amount,
                    'cgst_ledger_id' => $slot->cgst_ledger_id,
                    'sgst_amount' => (float) $slot->sgst_amount,
                    'sgst_ledger_id' => $slot->sgst_ledger_id,
                ])->all();
                $salesLedgersMatched = $this->hasMappedSalesLedgers($customSlots, $salesLedger);
                $gstLedgersMatched = $this->hasRequiredGstLedgers($customSlots, (bool) $transaction->is_igst);
            } elseif ($transaction->is_igst == 1) {
                $gstLedgersMatched = $sumIgst <= 0 || !empty($transaction->igst_id);
            } else {
                $gstLedgersMatched = ($sumCgst <= 0 || !empty($transaction->cgst_id)) && ($sumSgst <= 0 || !empty($transaction->sgst_id));
            }

            $finalStatus = ($partyLedgerMatched && $salesLedgersMatched && $amountMatched && $gstRatesValid && $gstLedgersMatched) ? 'saved' : 'pending';
            $transaction->update([
                'taxable_amount' => $sumAmount,
                'cgst' => $sumCgst,
                'sgst' => $sumSgst,
                'igst' => $sumIgst,
                // 'total_amount' => $sumAmount + $sumCgst + $sumSgst + $sumIgst,
                'total_amount' => $this->calculateTotalAmountWithRoundOff($sumAmount, $sumSgst, $sumCgst, $sumIgst, $roundOffSetting['side']),
                'roundoff_id' => $roundOffLedger?->iLedgerId,
                'roundoff_ledger_name' => $roundOffLedger?->strCustomerName,
                'roundoff' => $this->calculateRoundOffAmount($sumAmount, $sumSgst, $sumCgst, $sumIgst, $roundOffSetting['side']),
                'status' => $finalStatus
            ]);

            // ===============================
            // BULK UPDATE
            // ===============================
            $saved = CreditNoteTransaction::where('upload_id', $transaction->upload_id)
                ->where('status', 'saved')
                ->where('is_delete', 0)
                ->count();

            $pending = CreditNoteTransaction::where('upload_id', $transaction->upload_id)
                ->where('status', 'pending')
                ->where('is_delete', 0)
                ->count();

            $total = CreditNoteTransaction::where('upload_id', $transaction->upload_id)
                ->where('is_delete', 0)
                ->count();

            $status = ($pending == 0) ? 'completed' : 'pending';

            BulkCreditNoteUpload::where('id', $transaction->upload_id)->update([
                'total'     => $total,
                'saved'     => $saved,
                'pending'   => $pending,
                'status'    => $status
            ]);
          });
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 422);
        }


        return response()->json([
            'status' => true,
            'message' => 'Credit Note Updated Successfully'
        ]);
    }
    

    public function show($id)
    {
        $transaction = CreditNoteTransaction::with(['items','customGst'])->findOrFail($id);
        $firstItemName = $transaction->items->first()?->item_name;
        $gstMapping = $this->getGstMapping($transaction->iPartyId, $transaction->sales_ledger, $firstItemName);
        $itemGstMappingsByRate = $transaction->items
            ->mapWithKeys(function ($item) use ($transaction) {
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
                    (string) round((float) $gstRate, 2) => $this->getGstMapping(
                        $transaction->iPartyId,
                        $transaction->sales_ledger,
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
                    'ledger' => $slot->ledger_id ?? $transaction->sales_ledger_id,
                    'ledger_name' => $slot->ledger_name ?? $transaction->sales_ledger_name ?? $transaction->sales_ledger,
                    'gst' => $slot->gst_rate,
                    'amount' => $slot->amount ?? $slot->taxable,
                ];
            })->values()
            : collect();

        if ($transaction->items->isEmpty() && $noItemRows->isEmpty()) {
            $noItemRows = collect([[
                'ledger' => $transaction->sales_ledger_id,
                'ledger_name' => $transaction->sales_ledger_name ?? $transaction->sales_ledger,
                'gst' => $transaction->gst_rate,
                'amount' => $transaction->taxable_amount,
            ]]);
        }

        return response()->json([
            'id'              => $transaction->id,
            'note_no'         => $transaction->note_no,
            'note_date'       => date('Y-m-d',strtotime($transaction->note_date)),
            'gst_no'          => $transaction->gst_no,
            'party_name'      => $transaction->party_name,
            'place_of_supply' => $transaction->place_of_supply,
            'vch_type'        => $transaction->vch_type,
            'sales_ledger' => $transaction->sales_ledger,
            'sales_ledger_id' => $transaction->sales_ledger_id,
            'sales_ledger_name' => $transaction->sales_ledger_name,
            'remarks'         => $transaction->remarks,
            'taxable_amount'  => $transaction->taxable_amount,
            'sgst'            => $transaction->sgst,
            'cgst'            => $transaction->cgst,
            'igst'            => $transaction->igst,
            'total_amount'    => $transaction->total_amount,
            'roundoff'        => $transaction->roundoff ?? 0,
            'roundoff_id'     => $transaction->roundoff_id,
            'roundoff_ledger_name' => $transaction->roundoff_ledger_name,
            'is_igst' => $transaction->is_igst,
            'sgst_id' => $standardSgstId,
            'sgst_ledger_name'    => $this->gstLedgerName($transaction->iPartyId, $standardSgstId),
            'cgst_id'   => $standardCgstId,
            'cgst_ledger_name'  => $this->gstLedgerName($transaction->iPartyId, $standardCgstId),
            'igst_id'   => $standardIgstId,
            'igst_ledger_name'  => $this->gstLedgerName($transaction->iPartyId, $standardIgstId),
            'gst_mode'  => $transaction->gst_mode,
            'address'    => $transaction->address,
            'pincode'    => $transaction->pincode,
            'city'  => $transaction->city,
            'status'          => $transaction->status,
            'gst_rate' => $transaction->gst_rate,
            'against_invoice' => $transaction->against_invoice,
            'noitem_rows' => $noItemRows,
            'custom_gst' => $transaction->customGst->map(function ($slot) use ($gstMapping, $itemGstMappingsByRate, $transaction) {
                $slotMapping = $itemGstMappingsByRate->get((string) round((float) $slot->gst_rate, 2), $gstMapping);
                $igstLedgerId = $slot->igst_ledger_id ?: ($slotMapping['igst_id'] ?? null);
                $cgstLedgerId = $slot->cgst_ledger_id ?: ($slotMapping['cgst_id'] ?? null);
                $sgstLedgerId = $slot->sgst_ledger_id ?: ($slotMapping['sgst_id'] ?? null);

                return [
                    'id' => $slot->id,
                    'gst_rate' => $slot->gst_rate,
                    'taxable' => $slot->taxable,
                    'amount' => $slot->amount ?? $slot->taxable,
                    'ledger_id' => $slot->ledger_id,
                    'ledger_name' => $slot->ledger_name,
                    'igst_ledger_id' => $igstLedgerId,
                    'igst_ledger_name' => $slot->igst_ledger_name
                        ?: ($slotMapping['igst_name'] ?? $this->gstLedgerName($transaction->iPartyId, $igstLedgerId)),
                    'igst_amount' => $slot->igst_amount,
                    'cgst_ledger_id' => $cgstLedgerId,
                    'cgst_ledger_name' => $slot->cgst_ledger_name
                        ?: ($slotMapping['cgst_name'] ?? $this->gstLedgerName($transaction->iPartyId, $cgstLedgerId)),
                    'cgst_amount' => $slot->cgst_amount,
                    'sgst_ledger_id' => $sgstLedgerId,
                    'sgst_ledger_name' => $slot->sgst_ledger_name
                        ?: ($slotMapping['sgst_name'] ?? $this->gstLedgerName($transaction->iPartyId, $sgstLedgerId)),
                    'sgst_amount' => $slot->sgst_amount,
                ];
            }),
            'items' => $transaction->items->map(function ($item) {

                // 🔥 Derive rate if missing
                $rate = $item->rate;
                if (!$rate && $item->quantity > 0) {
                    $rate = $item->amount / $item->quantity;
                }

                // 🔥 Derive GST rate if missing
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
                    'amount'       => $item->amount,
                    'sgst'         => $item->sgst,
                    'cgst'         => $item->cgst,
                    'igst'         => $item->igst,
                    'total_amount' => $item->total_amount,
                ];
            }),
        ]);
    }

    private function creditNoteVoucherExists($partyId, ?string $vchType, ?string $vchNo, ?string $year, ?int $ignoreId = null): bool
    {
        if (blank($vchType) || blank($vchNo) || blank($year)) {
            return false;
        }

        $transactionExists = CreditNoteTransaction::where('iPartyId', $partyId)
            ->where('is_delete', 0)
            ->whereRaw('LOWER(TRIM(vch_type)) = ?', [strtolower(trim($vchType))])
            ->whereRaw('LOWER(TRIM(note_no)) = ?', [strtolower(trim($vchNo))])
            ->whereRaw('LOWER(TRIM(strYear)) = ?', [strtolower(trim($year))])
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->where('status','=','saved')
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
        return in_array($rate, [0.0, 0.05, 0.1, 0.125, 0.25, 0.5, 1.0, 1.5, 2.5, 3.0, 5.0, 6.0, 7.5, 9.0, 12.0, 14.0, 18.0, 28.0], true);
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

    private function extractCreditNoteRequestGstRates(Request $request, float $sumAmount = 0, float $sumCgst = 0, float $sumSgst = 0, float $sumIgst = 0): array
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
        $lineRates = array_values(array_filter($lineRates, fn ($rate) => $rate !== null && $rate !== '' && (float) $rate > 0));

        if (!empty($lineRates)) {
            return $lineRates;
        }

        $headerRate = $request->input('gst_rate');
        return ($headerRate !== null && $headerRate !== '' && (float) $headerRate > 0) ? [$headerRate] : [];
    }

    private function getCellValue($cell)
    {
        try {
            return $cell->getCalculatedValue();
        } catch (\Exception $e) {
            return $cell->getValue();
        }
    }

    private function toNumber($value)
    {
        if ($value === null || $value === '') {
            return 0;
        }

        $clean = str_replace(',', '', (string) $value);
        return is_numeric($clean) ? (float) $clean : 0;
    }

    private function parseDate(mixed $value): string
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }
        if (is_numeric($value)) {
            return Date::excelToDateTimeObject((float) $value)->format('Y-m-d');
        }
        
        $value = trim((string) $value);
        if ($value === '') {
            return '';
        }

        foreach (['d/m/Y', 'd-m-Y', 'd.m.Y', 'Y-m-d', 'Y/m/d'] as $format) {
            $date = \DateTime::createFromFormat('!' . $format, $value);
            if ($date && $date->format($format) === $value) {
                return $date->format('Y-m-d');
            }
        }

        $timestamp = strtotime($value);
        return $timestamp ? date('Y-m-d', $timestamp) : '';
    }

    public function changeUploadStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'status' => 'required|string'
        ]);

        $upload = BulkCreditNoteUpload::find($request->id);

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
        $ids = $request->input('ids', []);
        $ids = is_array($ids) ? $ids : [$ids];
        $ids = array_values(array_filter($ids));

        if (count($ids) === 0) {
            return response()->json([
                'status' => false,
                'message' => 'No records selected'
            ]);
        }
        DB::beginTransaction();
        try {
            foreach ($ids as $id) {
                $transactions = CreditNoteTransaction::where('upload_id', $id)->pluck('id');
                // GST delete
                CreditNoteCustomGst::whereIn('transaction_id', $transactions)->delete();
                // items delete
                CreditNoteTransactionItem::whereIn('transaction_id', $transactions)->delete();
                // main delete
                CreditNoteTransaction::where('upload_id', $id)->delete();
                // upload delete
                BulkCreditNoteUpload::where('id', $id)->delete();
            }
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => count($ids) > 1 ? 'Selected uploads deleted successfully' : 'Upload deleted successfully'
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

        if ($this->creditNoteVoucherExists($iPartyId, $request->voucher_type ?? 'Credit Note', $request->invoice, session('year'))) {
            return response()->json([
                'status' => false,
                'message' => 'Duplicate voucher found for the selected VnchType, VnchNo, and Year.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            // ✅ CREATE UPLOAD
            $upload = BulkCreditNoteUpload::where('iPartyId', $iPartyId)
                ->where('type', 'Manual')
                ->first();

            if ($upload) {
                $upload->update([
                    'pending' => $upload->pending + 1,
                    'total'   => $upload->total + 1,
                    'status'    => 'pending',
                ]);
            } else {
                $upload = BulkCreditNoteUpload::create([
                    'iPartyId'  => $iPartyId,
                    'batch_id'  => Str::uuid(),
                    'file_name' => 'Manual Entry',
                    'file_path' => 'manual',
                    'note_type' => 'credit',
                    'type'      => 'Manual',
                    'status'    => 'pending',
                    'total'     => 1,
                    'pending'   => 1,
                    'saved'     => 0,
                    'uploaded_by' => auth()->user()->id
                ]);
            }
            $sales_ledger = isset($request['sales_ledger']) && $request['sales_ledger'] != "Select Ledger" ? $request['sales_ledger'] : null;
            $sales_ledger_id = Ledger::getLedgerByName($iPartyId, $sales_ledger);
            $gstMapping = $this->getGstMapping(
                $iPartyId,
                $sales_ledger_id->name ?? $sales_ledger,
                $this->firstItemNameFromRequest($request)
            );
            // ✅ CREATE TRANSACTION
            $transaction = CreditNoteTransaction::create([
                'iPartyId'     => $iPartyId,
                'upload_id'    => $upload->id,
                'note_type' => 'credit',
                'note_no'   => $request->invoice,
                'note_date'         => $request->date ?? now(),
                'against_invoice' => $request->against_invoice,
                'party_name'   => $request->party,
                'gst_no'       => $request->gst,
                'place_of_supply' => $request->place,
                'vch_type'      => $request->voucher_type ?? 'Sales',
                'status'       => 'pending',
                'source'       => 'manual',
                'gst_mode'     => $request->gst_mode ?? 'standard',
                'remarks'      => $request->remarks,
                'is_igst'      => $request->is_igst,
                'sales_ledger' => $sales_ledger_id->name,
                'address'      => $request->address,
                'pincode'      => $request->pincode,
                'city'         => $request->city,
                // ✅ Ledger store (without item case)
                'sales_ledger_id'   => $sales_ledger_id->id, // $request->sales_ledger_id ?? null,
                'sales_ledger_name' => $sales_ledger_id->name,

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
                    CreditNoteTransactionItem::create([
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
                            $sumIgst += $this->roundCurrency($gstAmount);
                        } else {
                            $sumCgst += $this->roundCurrency(($amount * $gstRate) / 200);
                            $sumSgst += $this->roundCurrency(($amount * $gstRate) / 200);
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
                        $slotSalesLedger = !empty($slot['sales_ledger_id']) ? Ledger::getLedgerById($iPartyId, $slot['sales_ledger_id']) : null;
                        CreditNoteCustomGst::create([
                            'transaction_id' => $transaction->id,
                            'gst_rate'       => $slot['rate'] ?? 0,
                            'taxable'        => $slot['taxable'] ?? 0,
                            'ledger_id'      => $slotSalesLedger?->id,
                            'ledger_name'    => $slotSalesLedger?->name,

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
                // 'total_amount' => $sumAmount + $sumSgst + $sumCgst + $sumIgst,
                'total_amount' => $this->calculateTotalAmountWithRoundOff($sumAmount, $sumSgst, $sumCgst, $sumIgst, $roundOffSetting['side']),
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

    public function destroy($id)
    {
        $iPartyId = session('iPartyId');

        if (!$iPartyId) {
            return response()->json([
                'status' => false,
                'message' => 'Please select company first'
            ]);
        }

        $row = CreditNoteTransaction::find($id);

        if (!$row) {
            return response()->json([
                'status' => false,
                'message' => 'Record not found'
            ]);
        }

        $batchId = $row->upload_id;

        // ✅ Soft delete (as per your structure)
        $row->update(['is_delete' => 1]);

        // ✅ Delete items also (IMPORTANT 🔥)
        CreditNoteTransactionItem::where('transaction_id', $id)->delete();

        // ✅ Recalculate counts
        $saved = CreditNoteTransaction::where('upload_id', $batchId)
            ->where('status', 'Saved')
            ->where('is_delete', 0)
            ->count();

        $pending = CreditNoteTransaction::where('upload_id', $batchId)
            ->where('status', 'pending')
            ->where('is_delete', 0)
            ->count();

        $total = CreditNoteTransaction::where('upload_id', $batchId)
            ->where('is_delete', 0)
            ->count();

        // ✅ Update bulk upload table
        BulkCreditNoteUpload::where('id', $batchId)->update([
            'total'     => $total,
            'saved' => $saved,
            'pending'    => $pending,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Credit Note deleted successfully'
        ]);
    }


}

