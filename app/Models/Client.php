<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Client extends User
{
    const ROLE_CLIENT = 0;

    protected $table = 'users';
    protected $fillable = [
        'email',
        'password',
        'role',
        'reset_password_token',
        'reset_password_sent_at',
        'remember_created_at',
        'confirmation_token',
        'confirmed_at',
        'confirmation_sent_at',
        'unconfirmed_email',
        'created_at',
        'updated_at',
        'type',
        'name',
        'company_id',
        'guid',
        'deleted_at',
        'is_active',
        'remember_token',
        'token',
        'device',
        'origin',
        'iGroupAlterID',
        'iLedgerAlterID',
        'iStockAlterID',
        'iVchAlterID',
        'isStockManagement'
    ];
    protected $casts = [
        'is_active' => 'boolean',
        'isStockManagement' => 'integer', // or 'boolean'

    ];
    public function getIsStockManagementAttribute()
    {
        return (int) ($this->attributes['isStockManagement'] ?? 0);
    }

    /**
     * ✅ When you set camelCase, save to snake_case column:
     * $client->isStockManagement = 1;
     */
    public function setIsStockManagementAttribute($value)
    {
        $this->attributes['isStockManagement'] = (int) ($value ?? 0);
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function scopeInactive($q)
    {
        return $q->where('is_active', false);
    }

    protected static function booted(): void
    {
        // Scope all Client queries to role = client
        static::addGlobalScope('role', function (Builder $builder) {
            $builder->where('role', User::ROLES['client']);
        });

        static::creating(function (self $client) {
            $client->role = User::ROLES['client'];
        });
    }

    /**
     * Managers assigned to this client.
     */
    public function managers(): BelongsToMany
    {
        return $this->belongsToMany(
            Manager::class,
            'clients_managers',
            'client_id',
            'manager_id'
        );
    }

    /**
     * Supervisors assigned to this client.
     */
    public function supervisors(): BelongsToMany
    {
        return $this->belongsToMany(
            Supervisor::class,
            'clients_supervisors',
            'client_id',
            'supervisor_id'
        );
    }

    /**
     * Data Entry Operators assigned to this client.
     */
    public function dataEntryOperators(): BelongsToMany
    {
        return $this->belongsToMany(
            DataEntryOperator::class,
            'clients_data_entry_operators',
            'client_id',
            'data_entry_operator_id'
        );
    }

    /**
     * Groups for this client (via group_users pivot).
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(
            Group::class,
            'group_users',
            'user_id',   // this client's user id
            'group_id'
        );
    }

    /**
     * Direct user permission rows (user_permissions).
     */
    public function userPermissions(): HasMany
    {
        return $this->hasMany(UserPermission::class, 'user_id');
    }

    /**
     * Profile for this client.
     */
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class, 'user_id');
    }

    // IMPORTANT: do NOT override assignPermission here — use User::assignPermission(...)
}
