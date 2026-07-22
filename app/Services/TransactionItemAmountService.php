<?php

namespace App\Services;

class TransactionItemAmountService
{
    public static function normalizeItem(array $item, bool $useIgst = false): array
    {
        $quantity = self::number($item['quantity'] ?? $item['qty'] ?? 0);
        if ($quantity <= 0) {
            $quantity = 1.0;
        }

        $rate = self::number($item['rate'] ?? 0);
        $amount = self::round($quantity * $rate);
        $gstRate = self::number($item['gst_rate'] ?? $item['gst'] ?? 0);

        if ($gstRate <= 0 && $amount > 0) {
            $tax = self::number($item['sgst'] ?? 0) + self::number($item['cgst'] ?? 0) + self::number($item['igst'] ?? 0);
            $gstRate = self::round(($tax / $amount) * 100);
        }

        $hasIgst = $useIgst || self::number($item['igst'] ?? 0) > 0;
        if ($hasIgst) {
            $igst = self::round($amount * $gstRate / 100);
            $cgst = 0.0;
            $sgst = 0.0;
        } else {
            $igst = 0.0;
            $cgst = self::round($amount * ($gstRate / 2) / 100);
            $sgst = self::round($amount * ($gstRate / 2) / 100);
        }

        $item['quantity'] = $quantity;
        $item['qty'] = $quantity;
        $item['rate'] = $rate;
        $item['amount'] = $amount;
        $item['gst_rate'] = $gstRate;
        $item['gst'] = $gstRate;
        $item['sgst'] = $sgst;
        $item['cgst'] = $cgst;
        $item['igst'] = $igst;
        $item['total_amount'] = self::round($amount + $sgst + $cgst + $igst);

        return $item;
    }

    public static function normalizeItems(array $items, bool $useIgst = false): array
    {
        return array_map(fn (array $item) => self::normalizeItem($item, $useIgst), $items);
    }

    public static function normalizeModel(object $item, bool $useIgst = false): object
    {
        $normalized = self::normalizeItem([
            'quantity' => $item->quantity ?? 0,
            'rate' => $item->rate ?? 0,
            'amount' => $item->amount ?? 0,
            'gst_rate' => $item->gst_rate ?? 0,
            'sgst' => $item->sgst ?? 0,
            'cgst' => $item->cgst ?? 0,
            'igst' => $item->igst ?? 0,
            'total_amount' => $item->total_amount ?? 0,
        ], $useIgst);

        foreach (['quantity', 'rate', 'amount', 'gst_rate', 'sgst', 'cgst', 'igst', 'total_amount'] as $field) {
            $item->{$field} = $normalized[$field];
        }

        return $item;
    }

    public static function normalizeCustomGstSlot(object $slot, bool $useIgst = false): object
    {
        $amount = self::round(self::number($slot->taxable ?? $slot->amount ?? 0));
        $gstRate = self::number($slot->gst_rate ?? $slot->rate ?? 0);

        if ($gstRate <= 0 && $amount > 0) {
            $tax = self::number($slot->sgst_amount ?? 0) + self::number($slot->cgst_amount ?? 0) + self::number($slot->igst_amount ?? 0);
            $gstRate = self::round(($tax / $amount) * 100);
        }

        if ($useIgst || self::number($slot->igst_amount ?? 0) > 0) {
            $slot->igst_amount = self::round($amount * $gstRate / 100);
            $slot->cgst_amount = 0.0;
            $slot->sgst_amount = 0.0;
        } else {
            $slot->igst_amount = 0.0;
            $slot->cgst_amount = self::round($amount * ($gstRate / 2) / 100);
            $slot->sgst_amount = self::round($amount * ($gstRate / 2) / 100);
        }

        $slot->taxable = $amount;
        $slot->amount = $amount;
        $slot->gst_rate = $gstRate;

        return $slot;
    }
    
    private static function number($value): float
    {
        if (is_string($value)) {
            $value = str_replace([',', '₹', ' '], '', $value);
        }
        return is_numeric($value) ? (float) $value : 0.0;
    }

    private static function round(float $value): float
    {
        return round($value, 2);
    }
}