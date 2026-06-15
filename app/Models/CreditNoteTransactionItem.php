<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditNoteTransactionItem extends Model
{
    protected $table = 'credit_note_transaction_items';

    protected $fillable = [
        'transaction_id',
        'item_name',
        'hsn_code',
        'gst_rate',
        'quantity',
        'unit',
        'rate',
        'amount',
        'cgst',
        'sgst',
        'igst',
        'total_amount'
    ];

    protected $casts = [
        'gst_rate' => 'float',
        'quantity' => 'float',
        'rate' => 'float',
        'amount' => 'float',
        'cgst' => 'float',
        'sgst' => 'float',
        'igst' => 'float',
        'total_amount' => 'float',
    ];

    // 🔥 RELATIONSHIP
    public function transaction()
    {
        return $this->belongsTo(CreditNoteTransaction::class, 'transaction_id');
    }
}