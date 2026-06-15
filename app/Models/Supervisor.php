<?php

namespace App\Models;

class Supervisor extends User
{
    const ROLE_SUPERVISOR = 3; // ✅ Add this constant

    protected $table = 'users';

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('role', function ($builder) {
            $builder->where('role', User::ROLES['supervisor']);
        });

        static::creating(function ($supervisor) {
            $supervisor->role = User::ROLES['supervisor'];
        });
    }

    // Remove this problematic relationship
    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }
    public function clients()
    {
        return $this->belongsToMany(
            Client::class,
            'clients_supervisors',   // ✅ pivot table
            'supervisor_id',         // foreign key for supervisor
            'client_id'              // foreign key for client
        );
    }

    // public function managers()
    // {
    //     return $this->belongsToMany(
    //         Manager::class,
    //         'managers_supervisors',
    //         'supervisor_id',
    //         'manager_id'
    //     );
    // }

    public function managers()
    {
        return $this->belongsToMany(
            \App\Models\User::class,
            'managers_supervisors',
            'supervisor_id',
            'manager_id'
        )->where('users.role', 2); // 2 = manager
    }

    public function dataEntryOperators()
    {
        return $this->belongsToMany(DataEntryOperator::class, 'data_entry_operators_supervisors', 'supervisor_id', 'data_entry_operator_id');
    }
}
