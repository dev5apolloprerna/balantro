<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BulkCreditNoteUpload extends Model
{
    protected $table = 'bulk_credit_note_uploads';

    public $timestamps = false; // because we used uploaded_at manually

    protected $fillable = [
        'iPartyId',
        'batch_id',
        'file_name',
        'file_path',
        'note_type',
        'total',
        'pending',
        'saved',
        'status',
        'uploaded_by',
        'uploaded_at',
        'statement_date',
        'synced_date',
        'synced',
        'created_at',
        'type'
    ];

    protected $casts = [
        'total_rows' => 'integer',
        'processed_rows' => 'integer',
        'failed_rows' => 'integer',
        'uploaded_at' => 'datetime',
    ];

    // 🔥 RELATION: Upload → Transactions
    public function transactions()
    {
        return $this->hasMany(CreditNoteTransaction::class, 'upload_id', 'batch_id');
    }
}