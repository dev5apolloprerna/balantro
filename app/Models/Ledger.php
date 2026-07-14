<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class Ledger extends Model
{
    private const PREVIEW_CACHE_SECONDS = 60;

    private static function rememberPreviewLookup(int|string $companyId, string $lookup, callable $callback)
    {
        return Cache::remember(
            "preview_lookup:{$companyId}:{$lookup}",
            self::PREVIEW_CACHE_SECONDS,
            $callback
        );
    }

    protected $table = 'ledgers';

    protected $fillable = [
        'iPartyId',
        'Name',
        'Parent',
        'MailingName',
        'AddressLine1',
        'AddressLine2',
        'City',
        'State',
        'Country',
        'Pincode',
        'GstNo',
        'GstRegistrationType',
        'OpeningBalance',
        'OpeningType'
    ];

    public static function getAllLedgers($companyId)
    {
        return self::rememberPreviewLookup($companyId, 'all_ledgers', fn () => DB::select("
            SELECT iLedgerId AS id, strCustomerName AS name
                FROM LedgerMaster
            WHERE iPartyId = ?
                        UNION

            SELECT id, name
                FROM ledgers
            WHERE iPartyId = ?
            ORDER BY name
        ", [$companyId, $companyId]));
    }

    public static function getLedgerById($companyId, $ledgerId)
    {
        return DB::selectOne("
            SELECT id, name FROM (
                SELECT iLedgerId AS id, strCustomerName AS name
                FROM LedgerMaster
                WHERE iPartyId = ?

                UNION

                SELECT id, name
                FROM ledgers
                WHERE iPartyId = ?
            ) AS L
            WHERE id = ?
        ", [$companyId, $companyId, $ledgerId]);
    }

    public static function getAllDebtorsLedgers($companyId)
    {
        return self::rememberPreviewLookup($companyId, 'debtors_ledgers', fn () => DB::select("
            SELECT 
                iLedgerId AS id,
                strCustomerName AS name,
                GSTNo AS gst_no,
                LedgerAddress AS address,
                Pincode AS pincode,
                '' AS city,
                StateName AS state
            FROM LedgerMaster
            WHERE iPartyId = ? AND iPrimaryGroupId IN (
                    SELECT iGroupId
                    FROM GroupMaster
                    WHERE IsReserved = 1
                    AND IsRevenue = 0
                    AND iPartyId = ?
            )
            UNION

            SELECT 
                id,
                name,
                GstNo AS gst_no,
                CONCAT_WS(', ', NULLIF(AddressLine1, ''), NULLIF(AddressLine2, '')) AS address,
                Pincode AS pincode,
                City AS city,
                State AS state
            FROM ledgers
            WHERE Parent like 'Sundry Debtors' and  iPartyId = ?
            ORDER BY name
        ", [$companyId, $companyId, $companyId]));
    }
    
    public static function getAllCreditorsLedgers($companyId)
    {
        return self::rememberPreviewLookup($companyId, 'creditors_ledgers', fn () => DB::select("
            SELECT
                iLedgerId AS id,
                strCustomerName AS name,
                GSTNo AS gst_no,
                LedgerAddress AS address,
                Pincode AS pincode,
                '' AS city,
                StateName AS state
            FROM LedgerMaster
            WHERE iPartyId = ? AND iPrimaryGroupId IN (
                    SELECT iGroupId
                    FROM GroupMaster
                    WHERE IsReserved = 1
                    AND IsRevenue = 0
                    AND iPartyId = ?
            )
            UNION

            SELECT
                id,
                name,
                GstNo AS gst_no,
                TRIM(CONCAT_WS(' ', AddressLine1, AddressLine2)) AS address,
                Pincode AS pincode,
                City AS city,
                State AS state
            FROM ledgers
            WHERE Parent like 'Sundry Creditors' and iPartyId = ?
            ORDER BY name
        ", [$companyId, $companyId, $companyId]));
    }

    public static function getAllPartyLedgers($companyId)
    {
        return self::rememberPreviewLookup($companyId, 'party_ledgers', fn () => DB::select("
            SELECT
                iLedgerId AS id,
                strCustomerName AS name,
                GSTNo AS gst_no,
                LedgerAddress AS address,
                Pincode AS pincode,
                '' AS city,
                StateName AS state
            FROM LedgerMaster
            WHERE iPartyId = ? AND iPrimaryGroupId IN (
                    SELECT iGroupId
                    FROM GroupMaster
                    WHERE IsReserved = 1
                    AND IsRevenue = 0
                    AND iPartyId = ?
            )
            UNION

            SELECT
                id,
                name,
                GstNo AS gst_no,
                TRIM(CONCAT_WS(' ', AddressLine1, AddressLine2)) AS address,
                Pincode AS pincode,
                City AS city,
                State AS state
            FROM ledgers
            WHERE Parent IN ('Sundry Creditors', 'Sundry Debtors') and iPartyId = ?
            ORDER BY name
        ", [$companyId, $companyId, $companyId]));
    }

    public static function getAllBankLedgers($companyId)
    {
        return self::rememberPreviewLookup($companyId, 'bank_ledgers', fn () => DB::select("
            SELECT iLedgerId AS id, strCustomerName AS name
            FROM LedgerMaster
            WHERE strParents like 'Bank Accounts' and iPartyId = ?
            UNION

            SELECT id, name
            FROM ledgers
            WHERE Parent like 'Bank Accounts' and iPartyId = ?
            ORDER BY name
        ", [$companyId, $companyId]));
    }

    public static function getAllBankCashLedgers($companyId)
    {
        return self::rememberPreviewLookup($companyId, 'bank_cash_ledgers', fn () => DB::select("
            SELECT iLedgerId AS id, strCustomerName AS name
            FROM LedgerMaster
            WHERE (strParents in ('Bank Accounts','Cash-in-hand')) and iPartyId = ?
            UNION

            SELECT id, name
            FROM ledgers
            WHERE (Parent in ('Bank Accounts','Cash-in-hand')) and iPartyId = ?
            ORDER BY name
        ", [$companyId, $companyId]));
    }

    public static function getAlliGstLedgers($companyId)
    {
        return self::rememberPreviewLookup($companyId, 'igst_ledgers', fn () => DB::select("
            SELECT iLedgerId AS id, strCustomerName AS name
            FROM LedgerMaster
            WHERE iPartyId = ? AND (
                LOWER(strCustomerName) like '%igst%' OR
                LOWER(strCustomerName) like '%integrated%'
            )
            UNION
            SELECT id, name
            FROM ledgers
            WHERE iPartyId = ? AND (
                LOWER(Name) like '%igst%' OR
                LOWER(Name) like '%integrated%'
            )
            ORDER BY name
        ", [$companyId, $companyId]));
    }

    public static function getAllcGstLedgers($companyId)
    {
        return self::rememberPreviewLookup($companyId, 'cgst_ledgers', fn () => DB::select("
            SELECT iLedgerId AS id, strCustomerName AS name
            FROM LedgerMaster
            WHERE iPartyId = ? AND (
                LOWER(strCustomerName) like '%cgst%' OR
                LOWER(strCustomerName) like '%central%'
            )
            UNION

            SELECT id, name
            FROM ledgers
            WHERE iPartyId = ? AND (
                LOWER(Name) like '%cgst%' OR
                LOWER(Name) like '%central%'
            )
            ORDER BY name
        ", [$companyId, $companyId]));
    }

    public static function getAllsGstLedgers($companyId)
    {
        return self::rememberPreviewLookup($companyId, 'sgst_ledgers', fn () => DB::select("
            SELECT iLedgerId AS id, strCustomerName AS name
            FROM LedgerMaster
            WHERE iPartyId = ? AND (
                LOWER(strCustomerName) like '%sgst%' OR
                LOWER(strCustomerName) like '%state%' OR
                LOWER(strCustomerName) like '%utgst%' OR
                LOWER(strCustomerName) like '%union%'
            )
            UNION

            SELECT id, name
            FROM ledgers
            WHERE iPartyId = ? AND (
                LOWER(Name) like '%sgst%' OR
                LOWER(Name) like '%state%' OR
                LOWER(Name) like '%utgst%' OR
                LOWER(Name) like '%union%'
            )
            ORDER BY name
        ", [$companyId, $companyId]));
    }

    public static function mergeLedgersByIds($companyId, $ledgers, array $ledgerIds): array
    {
        $ledgerIds = collect($ledgerIds)
            ->filter()
            ->map(fn ($id) => (string) $id)
            ->unique()
            ->values();

        if ($ledgerIds->isEmpty()) {
            return collect($ledgers)->values()->all();
        }

        $existingIds = collect($ledgers)
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->all();

        $missingIds = $ledgerIds
            ->reject(fn ($id) => in_array($id, $existingIds, true))
            ->values()
            ->all();

        if (empty($missingIds)) {
            return collect($ledgers)->values()->all();
        }

        $missingLedgers = DB::table('LedgerMaster')
            ->selectRaw('iLedgerId AS id, strCustomerName AS name')
            ->where('iPartyId', $companyId)
            ->whereIn('iLedgerId', $missingIds)
            ->get()
            ->merge(
                DB::table('ledgers')
                    ->select('id', 'name')
                    ->where('iPartyId', $companyId)
                    ->whereIn('id', $missingIds)
                    ->get()
            );

        return collect($ledgers)
            ->merge($missingLedgers)
            ->unique(fn ($ledger) => (string) $ledger->id)
            ->sortBy('name')
            ->values()
            ->all();
    }

    public static function getPurchaseLedgers($companyId)
    {
        return self::rememberPreviewLookup($companyId, 'purchase_ledgers', fn () => DB::select("
            SELECT iLedgerId AS id, strCustomerName AS name
            FROM LedgerMaster
            WHERE iPartyId = ? and (strParents like 'Purchase Accounts'
            or iPrimaryGroupId IN (
                    SELECT iGroupId
                    FROM GroupMaster
                    WHERE IsReserved = 1
                    AND IsRevenue = 1
                    AND iPartyId = ?
            ))
            UNION

            SELECT id, name
            FROM ledgers
            WHERE Parent like 'Purchase Accounts' and iPartyId = ?
            ORDER BY name
        ", [$companyId, $companyId, $companyId]));
    }

    public static function getSalesLedgers($companyId)
    {
        return self::rememberPreviewLookup($companyId, 'sales_ledgers', fn () => DB::select("
            SELECT iLedgerId AS id, strCustomerName AS name
            FROM LedgerMaster
            WHERE  iPartyId = ? and (iPrimaryGroupId IN (
                    SELECT iGroupId
                    FROM GroupMaster
                    WHERE IsReserved = 1
                    AND IsRevenue = 1
                    AND iPartyId = ?
            ))
            UNION

            SELECT id, name
            FROM ledgers
            WHERE Parent like 'Sales Accounts' and iPartyId = ?
            ORDER BY name
        ", [$companyId, $companyId, $companyId]));
    }

    public static function getStockItemsForPreview($companyId)
    {
        return self::rememberPreviewLookup($companyId, 'stock_items_preview', fn () => DB::table('StockItemMaster')
            ->select(
                'iStockIdtemId',
                'strItemName',
                'strBaseUnits',
                'CGSTLedgerId',
                'SGSTLedgerId',
                'IGSTLedgerId',
                'CGSTLedgerId as cgst_id',
                'SGSTLedgerId as sgst_id',
                'IGSTLedgerId as igst_id'
            )
            ->where('iPartyId', $companyId)
            ->orderBy('strItemName', 'asc')
            ->get());
    }

    public static function getLedgerByName($companyId, $ledgerName)
    {
        $ledgerName = trim($ledgerName);

        return DB::selectOne("
            SELECT id, name FROM (
                SELECT iLedgerId AS id, strCustomerName AS name
                FROM LedgerMaster
                WHERE iPartyId = ?

                UNION

                SELECT id, name
                FROM ledgers
                WHERE iPartyId = ?
            ) AS L
            WHERE 
                LOWER(REPLACE(REPLACE(REPLACE(name, ' ', ''), '\"', ''), '''', '')) = 
                LOWER(REPLACE(REPLACE(REPLACE(?, ' ', ''), '\"', ''), '''', ''))
        ", [$companyId, $companyId, $ledgerName]);
    }

    // getSalesReturnLedgers
    public static function getLedgerDetailsForAutofill($companyId)
    {
        return self::rememberPreviewLookup($companyId, 'ledger_details_autofill', fn () => DB::select("
            SELECT
                iLedgerId AS id,
                strCustomerName AS name,
                GSTNo AS gst_no,
                LedgerAddress AS address,
                Pincode AS pincode,
                '' AS city,
                StateName AS state
            FROM LedgerMaster
            WHERE iPartyId = ?

            UNION

            SELECT
                id,
                name,
                GstNo AS gst_no,
                TRIM(CONCAT(COALESCE(AddressLine1, ''), ' ', COALESCE(AddressLine2, ''))) AS address,
                Pincode AS pincode,
                City AS city,
                State AS state
            FROM ledgers
            WHERE iPartyId = ?
            ORDER BY name
        ", [$companyId, $companyId]));
    }
}
