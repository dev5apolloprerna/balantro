<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseTransactionItem extends Model
{
    protected $table = 'purchase_transaction_items';

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
        return $this->belongsTo(PurchaseTransaction::class, 'transaction_id');
    }
}
