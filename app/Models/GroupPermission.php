<?php

namespace App\Models;

class GroupPermission extends Model
{
    protected $fillable = [
        'group_id',
        'permission_id'
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }
}