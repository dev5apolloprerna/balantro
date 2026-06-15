<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;
    protected $fillable = ['path', 'original_name', 'size', 'attachable_type', 'attachable_id'];
    public function attachable()
    {
        return $this->morphTo();
    }
}
