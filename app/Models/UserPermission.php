<?php

namespace App\Models;

class UserPermission extends Model
{
    protected $fillable = [
        'user_id',
        'permission_id',
        'is_negative'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }
}