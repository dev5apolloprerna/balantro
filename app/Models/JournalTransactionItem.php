<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalTransactionItem extends Model
{
    use HasFactory;
    protected $table = 'journal_transaction_items';

    protected $fillable = [
        'transaction_id',
        'ledger_id',
        'ledger_name',
        'dr_cr',
        'debit',
        'credit',
        'narration'
    ];

    protected $casts = [
        'debit' => 'float',
        'credit' => 'float',
    ];

    // 🔥 BELONGS TO TRANSACTION
    public function transaction()
    {
        return $this->belongsTo(JournalTransaction::class, 'transaction_id');
    }
    
}
