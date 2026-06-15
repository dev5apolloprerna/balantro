<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Ledger extends Model
{
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
        return DB::select("
            SELECT iLedgerId AS id, strCustomerName AS name
                FROM LedgerMaster
            WHERE iPartyId = ?
                        UNION

            SELECT id, name
                FROM ledgers
            WHERE iPartyId = ?
            ORDER BY name
        ", [$companyId, $companyId]);
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
        return DB::select("
            SELECT iLedgerId AS id, strCustomerName AS name
            FROM LedgerMaster
            WHERE strParents like 'Sundry Debtors' and iPartyId = ?
            UNION

            SELECT id, name
            FROM ledgers
            WHERE Parent like 'Sundry Debtors' and  iPartyId = ?
            ORDER BY name
        ", [$companyId, $companyId]);
    }
    
    public static function getAllCreditorsLedgers($companyId)
    {
        return DB::select("
            SELECT iLedgerId AS id, strCustomerName AS name
            FROM LedgerMaster
            WHERE (strParents='Sundry Creditors' or strParents='Creditor for Goods' or strParents='Creditor for Other') and iPartyId = ?
            UNION

            SELECT id, name
            FROM ledgers
            WHERE Parent like 'Sundry Creditors' and iPartyId = ?
            ORDER BY name
        ", [$companyId, $companyId]);
    }

    public static function getAllBankLedgers($companyId)
    {
        return DB::select("
            SELECT iLedgerId AS id, strCustomerName AS name
            FROM LedgerMaster
            WHERE strParents like 'Bank Accounts' and iPartyId = ?
            UNION

            SELECT id, name
            FROM ledgers
            WHERE Parent like 'Bank Accounts' and iPartyId = ?
            ORDER BY name
        ", [$companyId, $companyId]);
    }

    public static function getAllBankCashLedgers($companyId)
    {
        return DB::select("
            SELECT iLedgerId AS id, strCustomerName AS name
            FROM LedgerMaster
            WHERE (strParents in ('Bank Accounts','Cash-in-hand')) and iPartyId = ?
            UNION

            SELECT id, name
            FROM ledgers
            WHERE (Parent in ('Bank Accounts','Cash-in-hand')) and iPartyId = ?
            ORDER BY name
        ", [$companyId, $companyId]);
    }

    public static function getAlliGstLedgers($companyId)
    {
        return DB::select("
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
        ", [$companyId, $companyId]);
    }

    public static function getAllcGstLedgers($companyId)
    {
        return DB::select("
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
        ", [$companyId, $companyId]);
    }

    public static function getAllsGstLedgers($companyId)
    {
        return DB::select("
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
        ", [$companyId, $companyId]);
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
        return DB::select("
            SELECT iLedgerId AS id, strCustomerName AS name
            FROM LedgerMaster
            WHERE strParents like 'Purchase Accounts' and iPartyId = ?
            UNION

            SELECT id, name
            FROM ledgers
            WHERE Parent like 'Purchase Accounts' and iPartyId = ?
            ORDER BY name
        ", [$companyId, $companyId]);
    }

    public static function getSalesLedgers($companyId)
    {
        return DB::select("
            SELECT iLedgerId AS id, strCustomerName AS name
            FROM LedgerMaster
            WHERE strParents like 'Sales Accounts' and iPartyId = ?
            UNION

            SELECT id, name
            FROM ledgers
            WHERE Parent like 'Sales Accounts' and iPartyId = ?
            ORDER BY name
        ", [$companyId, $companyId]);
    }

    // public static function getLedgerByName($companyId, $ledgerId)
    // {
    //     return DB::selectOne("
    //         SELECT id, name FROM (
    //             SELECT iLedgerId AS id, strCustomerName AS name
    //             FROM LedgerMaster
    //             WHERE iPartyId = ?

    //             UNION

    //             SELECT id, name
    //             FROM ledgers
    //             WHERE iPartyId = ?
    //         ) AS L
    //         WHERE name = ?
    //     ", [$companyId, $companyId, $ledgerId]);
    // }

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
                LOWER(REPLACE(REPLACE(name, '\"', ''), '''', '')) = 
                LOWER(REPLACE(REPLACE(?, '\"', ''), '''', ''))
        ", [$companyId, $companyId, $ledgerName]);
    }

    // getSalesReturnLedgers
    
}
