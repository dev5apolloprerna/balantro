<?php

namespace App\Models;

class SuperAdmin extends User
{
    protected $table = 'users';

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope('role', function ($builder) {
            $builder->where('role', self::ROLE_SUPER_ADMIN);
        });

        static::creating(function ($superAdmin) {
            $superAdmin->role = self::ROLE_SUPER_ADMIN;
        });
    }
}