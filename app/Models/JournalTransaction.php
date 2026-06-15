<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalTransaction extends Model
{
    use HasFactory;
    protected $table = 'journal_transactions';

    protected $fillable = [
        'iPartyId',
        'upload_id',
        'journal_no',
        'date',
        'narration',
        'total_debit',
        'total_credit',
        'status',
        'is_delete'
    ];

    protected $casts = [
        'date' => 'date',
        'total_debit' => 'float',
        'total_credit' => 'float',
        'is_delete' => 'boolean',
    ];

    // 🔥 ITEMS RELATION
    public function items()
    {
        return $this->hasMany(JournalTransactionItem::class, 'transaction_id');
    }

    // 🔥 UPLOAD RELATION
    public function upload()
    {
        return $this->belongsTo(BulkJournalUpload::class, 'upload_id');
    }
}
