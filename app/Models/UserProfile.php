<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;
    protected $table = 'user_profiles';

    const GENDER_MALE   = 'male';
    const GENDER_FEMALE = 'female';
    const LANGUAGE_EN   = 'en';

    protected $fillable = [
        'user_id',
        'mobile_no',
        'whatsapp_no',
        'gender',
        'address',
        'profile_image'
    ];

    public const GENDERS = [
        self::GENDER_MALE,
        self::GENDER_FEMALE,
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function profileImage()
    {
        return $this->morphOne(File::class, 'attachable');
    }

    protected static function booted()
    {
        static::saving(function ($user_profile) {
            if ($user_profile->mobile_no && strlen($user_profile->mobile_no) !== 10) {
                throw new \Exception('Mobile number must be 10 digits');
            }
            if ($user_profile->whatsapp_no && strlen($user_profile->whatsapp_no) !== 10) {
                throw new \Exception('WhatsApp number must be 10 digits');
            }
        });
    }
}
