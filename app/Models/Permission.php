<?php

namespace App\Models;

use App\Models\Scopes\ActiveScope;

class Permission extends Model
{
    protected $fillable = [
        'name',
        'action',
        'subject',
        'conditions'
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new ActiveScope);
    }

    public function groupPermissions()
    {
        return $this->hasMany(GroupPermission::class);
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_permissions');
    }

    public function userPermissions()
    {
        return $this->hasMany(UserPermission::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_permissions');
    }
}
