<?php

namespace App\Models;

class Group extends Model
{
    const TYPE_ADMIN = 'admin';
    const TYPE_CLIENT = 'client';

    protected $fillable = [
        'name',
        'group_type'
    ];

    public function groupPermissions()
    {
        return $this->hasMany(GroupPermission::class);
    }

    public function permissions()
    {
        //return $this->belongsToMany(Permission::class, 'group_permissions');
        return $this->belongsToMany(
            \App\Models\Permission::class,
            'group_permissions',     // pivot table
            'group_id',              // foreign key on pivot to Group
            'permission_id'          // foreign key on pivot to Permission
        )->withTimestamps()
            ->select('permissions.*');
    }
    // public function permission()
    // {
    //     return $this->belongsToMany(Permission::class, 'group_permissions', 'group_id', 'permission_id');
    // }
    public function groupUsers()
    {
        return $this->hasMany(GroupUser::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'group_users', 'group_id', 'user_id');
    }

    // public function users(): BelongsToMany
    // {
    //     return $this->belongsToMany(User::class, 'group_users', 'group_id', 'user_id');
    // }

    // public function permissions(): BelongsToMany
    // {
    //     return $this->belongsToMany(Permission::class, 'group_permissions', 'group_id', 'permission_id');
    // }
}
