<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankTransaction extends Model
{
    protected $table = 'bank_transactions';

    protected $fillable = [
        'upload_id',
        'iPartyId',
        'txn_date',
        'value_date',
        'narration',
        'ref_no',
        'debit',
        'credit',
        'balance',
        'ledger_name',
        'bank_name',
        'status',
        'amount',
        'txn_type',
        'running_balance',
        'unique_key',
        'is_reconciled',
        'reconciled_at',
        'source',
        'raw_data',
        'cheque_no',
        'cost_center',
        'vch_type',
        'strYear',
        'is_suspense',
        'suspense_reason',
        'resolution_remark',
        'resolved_at',
        'resolution_remark_new'
    ];

    public function upload()
    {
        return $this->belongsTo(BulkBankUpload::class, 'upload_id');
    }
}
