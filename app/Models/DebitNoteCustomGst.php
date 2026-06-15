<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DebitNoteCustomGst extends Model
{
    use HasFactory;
    protected $table = 'DebitNoteCustomGst';

    protected $fillable = [
        'transaction_id',
        'gst_rate',
        'taxable',

        'igst_ledger_id',
        'igst_ledger_name',
        'igst_amount',

        'cgst_ledger_id',
        'cgst_ledger_name',
        'cgst_amount',

        'sgst_ledger_id',
        'sgst_ledger_name',
        'sgst_amount',
        'ledger_id',
        'ledger_name',
        'amount'
    ];

    public $timestamps = true;

    public function transaction()
    {
        return $this->belongsTo(PurchaseTransaction::class, 'transaction_id');
    }

    public function igstLedger()
    {
        return $this->belongsTo(Ledger::class, 'igst_ledger_id');
    }

    public function cgstLedger()
    {
        return $this->belongsTo(Ledger::class, 'cgst_ledger_id');
    }

    public function sgstLedger()
    {
        return $this->belongsTo(Ledger::class, 'sgst_ledger_id');
    }
}
