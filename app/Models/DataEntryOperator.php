<?php

namespace App\Models;

class DataEntryOperator extends User
{
    const ROLE_DATA_ENTRY_OPERATOR = 4;

    protected $table = 'users';

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope('role', function ($builder) {
            // ✅ Use User::ROLES['data_entry_operator'] instead of undefined constant
            $builder->where('role', User::ROLES['data_entry_operator']);
        });

        static::creating(function ($deo) {
            // ✅ Use User::ROLES['data_entry_operator'] instead of undefined constant
            $deo->role = User::ROLES['data_entry_operator'];
        });
    }

    /*public function user()
	{
		return $this->belongsTo(User::class);
	}*/
    public function supervisors()
    {
        return $this->belongsToMany(
            User::class,
            'data_entry_operators_supervisors',
            'data_entry_operator_id',
            'supervisor_id'
        );
    }

    public function managers()
    {
        return $this->belongsToMany(Manager::class, 'data_entry_operators_managers', 'data_entry_operator_id', 'manager_id');
    }

    /*public function supervisors()
    {
        return $this->belongsToMany(Supervisor::class, 'data_entry_operators_supervisors', 'data_entry_operator_id', 'supervisor_id');
    }*/

    public function clients()
    {
        return $this->belongsToMany(Client::class, 'clients_data_entry_operators', 'data_entry_operator_id', 'client_id');
    }

    public function permissions()
    {
        // user_permissions(user_id, permission_id, [is_negative], timestamps)
        return $this->belongsToMany(
            \App\Models\Permission::class,
            'user_permissions',
            'user_id',        // foreign key on pivot that points to THIS model
            'permission_id'   // foreign key on pivot that points to Permission
        )->withTimestamps();
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_users', 'user_id', 'group_id');
    }

    // public function groups()
    // {
    //     return $this->belongsToMany(Group::class, 'group_users', 'data_entry_operator_id', 'group_id');
    // }
}
