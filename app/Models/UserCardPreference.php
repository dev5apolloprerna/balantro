<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCardPreference extends Model
{
    use HasFactory;
    protected $table = 'user_card_preferences';
    protected $fillable = [
        'user_id',
        'party_id',
        'selected_groups',
    ];

    protected $casts = [
        'selected_groups' => 'array',
    ];

    /**
     * Get the user that owns the preference
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getSelectedGroupsAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    // Manual setter for selected_groups
    public function setSelectedGroupsAttribute($value)
    {
        $this->attributes['selected_groups'] = json_encode($value ?? []);
    }
}
