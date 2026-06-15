<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesTransactionItem extends Model
{
    protected $table = 'sales_transaction_items';

    protected $fillable = [
        'iPartyId',
        'transaction_id',
        'upload_id',
        'item_name',
        'quantity',
        'rate',
        'amount',
        'sgst',
        'cgst',
        'igst',
        'total_amount',
        'unit',
        'hsn',
        'gst_rate'
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function transaction()
    {
        return $this->belongsTo(SalesTransaction::class, 'transaction_id');
    }
}
