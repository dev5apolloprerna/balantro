<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GSTLedgerMapping extends Model
{
    use HasFactory;
    protected $fillable = [
        'iPartyId',
        'MappingType',
        'ReferenceId',
        'CGSTLedgerId',
        'SGSTLedgerId',
        'IGSTLedgerId',
        'IsActive'
    ];

}
