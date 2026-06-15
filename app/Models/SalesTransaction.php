<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesTransaction extends Model
{
    protected $table = 'sales_transactions';

    protected $fillable = [
        'iPartyId',
        'upload_id',
        'invoice_no',
        'date',
        'gst_no',
        'party_name',
        'place_of_supply',
        'sales_ledger',
        'item_name',
        'quantity',
        'rate',
        'amount',
        'sgst',
        'cgst',
        'igst',
        'total_amount',
        'status',
        'vchType',
        'address',
        'pincode',
        'city',
        'is_igst',
        'Remarks',
        'sgst_id',
        'sgst_ledger_name',
        'cgst_id',
        'cgst_ledger_name',
        'igst_id',
        'igst_ledger_name',
        'sales_ledger_id',
        'sales_ledger_name',
        'gst_mode',
        'strYear',
        'year_from_date',
        'year_to_date',
        'isWithItem',
        'gst_rate',
        'roundoff_id',
        'roundoff_ledger_name',
        'roundoff'
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
        'total_amount' => 'decimal:2'
    ];

    public function upload()
    {
        return $this->belongsTo(BulkSalesUpload::class, 'upload_id');
    }

    public function items()
    {
        return $this->hasMany(SalesTransactionItem::class, 'transaction_id');
    }

    public function customGst()
    {
        return $this->hasMany(SalesCustomGst::class, 'transaction_id');
    }
}
