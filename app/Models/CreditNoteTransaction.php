<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditNoteTransaction extends Model
{
    protected $table = 'credit_note_transactions';

    protected $fillable = [
        'iPartyId',
        'note_type',
        'note_no',
        'note_date',
        'party_name',
        'party_ledger',
        'gst_no',
        'place_of_supply',
        'against_invoice',
        'vch_type',
        'sales_ledger',
        'remarks',
        'taxable_amount',
        'cgst',
        'sgst',
        'igst',
        'total_amount',
        'is_igst',
        'upload_id',
        'status',
        'is_delete',
        'sgst_id',
        'sgst_ledger_name',
        'cgst_id',
        'cgst_ledger_name',
        'igst_id',
        'igst_ledger_name',
        'gst_mode',
        'sales_ledger_id',
        'sales_ledger_name',
        'address',
        'pincode',
        'city',
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
        'note_date' => 'date',
        'taxable_amount' => 'float',
        'cgst' => 'float',
        'sgst' => 'float',
        'igst' => 'float',
        'total_amount' => 'float',
        'is_igst' => 'boolean',
        'is_delete' => 'boolean',
    ];

    // 🔥 RELATIONSHIP
    public function items()
    {
        return $this->hasMany(CreditNoteTransactionItem::class, 'transaction_id');
    }

    public function customGst()
    {
        return $this->hasMany(CreditNoteCustomGst::class, 'transaction_id');
    }
}