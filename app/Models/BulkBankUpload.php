<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BulkBankUpload extends Model
{
    protected $table = 'bulk_bank_uploads';

    protected $fillable = [
        'iPartyId',
        'file_name',
        'file_path',
        'statement_date',
        'synced_date',
        'total',
        'pending',
        'saved',
        'synced',
        'bank_name',
        'status'
    ];

    public function transactions()
    {
        return $this->hasMany(BankTransaction::class, 'upload_id');
    }
}
