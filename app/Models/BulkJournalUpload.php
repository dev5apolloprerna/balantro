<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BulkJournalUpload extends Model
{
    use HasFactory;
    protected $table = 'bulk_journal_uploads';

    protected $fillable = [
        'iPartyId',
        'batch_id',
        'file_name',
        'file_path',
        'total_rows',
        'processed_rows',
        'total',
        'saved',
        'pending',
        'status',
        'type'
    ];

    // 🔥 Relationship
    public function transactions()
    {
        return $this->hasMany(JournalTransaction::class, 'upload_id');
    }
}
