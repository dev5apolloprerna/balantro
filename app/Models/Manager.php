<?php

namespace App\Models;

class Manager extends User
{
    protected $table = 'users';

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('role', function ($builder) {
            $builder->where('role', User::ROLES['manager']); // ✅ use integer mapping
        });

        static::creating(function ($manager) {
            $manager->role = User::ROLES['manager']; // ✅ assign numeric role
        });
    }

    public function clients()
    {
        return $this->belongsToMany(
            Client::class,
            'clients_managers',    // ✅ pivot table
            'manager_id',
            'client_id'
        );
    }

    public function supervisors()
    {
        return $this->belongsToMany(
            User::class,
            'managers_supervisors',
            'manager_id',
            'supervisor_id'
        );
    }

    public function dataEntryOperators()
    {
        return $this->belongsToMany(DataEntryOperator::class, 'data_entry_operators_managers', 'manager_id', 'data_entry_operator_id');
    }
}
