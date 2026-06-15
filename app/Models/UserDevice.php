<?php

namespace App\Models;

class UserDevice extends Model
{
    protected $fillable = [
        'user_id',
        'device_token',
        'device_type',
        'browser_name',
        'os_name',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope for active devices
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope by device type
    public function scopeByType($query, $type)
    {
        return $query->where('device_type', $type);
    }

    // Check if device token exists for user
    public static function tokenExists($userId, $deviceToken)
    {
        return static::where('user_id', $userId)
            ->where('device_token', $deviceToken)
            ->exists();
    }
}
