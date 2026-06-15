<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseTransaction extends Model
{
    protected $table = 'purchase_transactions';

    protected $fillable = [

        'upload_id',
        'invoice_no',
        'date',
        'gst_no',
        'party_name',
        'place_of_supply',
        'amount',
        'total_amount',
        'purchase_ledger',
        'item_name',
        'quantity',
        'rate',
        'sgst',
        'cgst',
        'igst',
        'iPartyId',
        'vchType',
        'status',
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
        'gst_mode',
        'purchase_ledger_id',
        'purchase_ledger_name',
        'strYear',
        'year_from_date',
        'year_to_date',
        'isWithItem',
        'gst_rate',
        'roundoff_id',
        'roundoff_ledger_name',
        'roundoff'
    ];

    public function upload()
    {
        return $this->belongsTo(BulkPurchaseUpload::class,'upload_id');
    }

    public function items()
    {
        return $this->hasMany(PurchaseTransactionItem::class, 'transaction_id');
    }
 
    public function customGst()
    {
        return $this->hasMany(PurchaseCustomGst::class, 'transaction_id');
    }
}