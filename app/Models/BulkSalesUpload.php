<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BulkSalesUpload extends Model
{
    protected $table = 'bulk_sales_uploads';

    protected $fillable = [
        'iPartyId',
        'file_name',
        'file_path',
        'type',
        'statement_date',
        'synced_date',
        'total',
        'pending',
        'saved',
        'synced',
        'status',
        'iPartyId',
        'sales_ledger'
    ];

    protected $casts = [
        'statement_date' => 'date',
        'synced_date' => 'date'
    ];

    public function transactions()
    {
        return $this->hasMany(SalesTransaction::class, 'upload_id');
    }
}
