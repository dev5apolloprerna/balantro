<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

trait VoucherValidation
{
    private function applicableGstSlabs(): array
    {
        // $settings = DB::table('gst_settings')->where('is_active', true)->get();
        // $slabs = [];
        // foreach ($settings as $setting) {
        //     $slabs[] = (float) $setting->igst;
        //     $slabs[] = (float) $setting->cgst + (float) $setting->sgst;
        // }
        // $slabs = array_merge($slabs, [0.0, 0.05, 0.1, 0.125, 0.25, 0.5, 1.0, 1.5, 2.5, 3.0, 5.0, 6.0, 7.5, 9.0, 12.0, 14.0, 18.0, 28.0]);
        // return array_values(array_unique(array_map(fn ($rate) => round((float) $rate, 2), $slabs)));
        return [0.0, 0.05, 0.1, 0.125, 0.25, 0.5, 1.0, 1.5, 2.5, 3.0, 5.0, 6.0, 7.5, 9.0, 12.0, 14.0, 18.0, 28.0];
    }

    private function isApplicableGstRate($rate): bool
    {
        $rate = round((float) $rate, 2);
        // foreach ($this->applicableGstSlabs() as $slab) {
        //     if (abs($rate - (float) $slab) < 0.01) {
        //         return true;
        //     }
        // }
        // return false;
        if ($rate <= 0) {
            return true;
        }
        return in_array($rate, $this->applicableGstSlabs(), true);
    }

    private function allGstRatesAreApplicable(array $rates): bool
    {
        foreach ($rates as $rate) {
            if (!$this->isApplicableGstRate($rate)) {
                return false;
            }
        }
        return true;
    }

    private function extractVoucherRequestGstRates(array $items = [], array $noitemRows = [], array $customSlots = [], $headerRate = null): array
    {
        $lineRates = [];
        foreach ($items as $item) {
            $lineRates[] = is_array($item) ? ($item['gst_rate'] ?? null) : ($item->gst_rate ?? null);
        }
        foreach ($noitemRows as $row) {
            $lineRates[] = is_array($row) ? ($row['gst'] ?? null) : ($row->gst ?? null);
        }
        foreach ($customSlots as $slot) {
            $lineRates[] = is_array($slot) ? ($slot['rate'] ?? null) : ($slot->rate ?? null);
        }

        $lineRates = array_values(array_filter($lineRates, fn ($rate) => $rate !== null && $rate !== '' && (float) $rate > 0));

        if (!empty($lineRates)) {
            return $lineRates;
        }

        return ($headerRate !== null && $headerRate !== '' && (float) $headerRate > 0) ? [$headerRate] : [];
    }

    private function voucherCombinationExists(string $table, array $columns, ?int $ignoreId = null): bool
    {
        $query = DB::table($table)
            ->where('iPartyId', $columns['iPartyId'])
            ->where('status','=','saved')
            ->where($columns['voucher_column'], $columns['voucher_value'])
            ->where($columns['number_column'], $columns['number_value'])
            ->where($columns['party_column'], $columns['party_value'])
            ->where($columns['date_column'], $columns['date_value'])
            ->where($columns['year_column'], $columns['year_value']);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        if (DB::getSchemaBuilder()->hasColumn($table, 'is_delete')) {
            $query->where('is_delete', 0);
        }

        return $query->exists();
    }

    private function vchHistoryCombinationExists(array $columns): bool
    {
        $yearId = DB::table('YearMaster')
            ->where('iPartyId', $columns['iPartyId'])
            ->where('strYear', $columns['year_value'])
            ->value('iYearId');
        return DB::table('VchHistory')
            ->where('iPartyId', $columns['iPartyId'])
            ->where('vchType', $columns['voucher_value'])
            ->where('vchNo', $columns['number_value'])
            ->where('trnAccount', $columns['party_value'])
            ->where('strVchDate', $columns['history_date_value'])
            ->where('iYearId', $yearId)
            ->exists();
    }

    private function historyDate($date): string
    {
        return date('j-M-y', strtotime((string) $date));
    }
}