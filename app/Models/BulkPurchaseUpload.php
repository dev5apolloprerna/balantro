<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BulkPurchaseUpload extends Model
{
    protected $table = 'bulk_purchase_uploads';

    protected $fillable = [

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
        'purchase_ledger'

    ];

    public function transactions()
    {
        return $this->hasMany(PurchaseTransaction::class,'upload_id');
    }
}