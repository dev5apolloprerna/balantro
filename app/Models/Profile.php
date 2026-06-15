<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; // ✅ missing import

class Profile extends Model
{
    // existing constants…
    const BUSINESS_TYPE_INDIVIDUAL       = 'individual';
    const BUSINESS_TYPE_PARTNERSHIP_FIRM = 'partnership_firm';
    const BUSINESS_TYPE_LLP              = 'llp';
    const BUSINESS_TYPE_HUF               = 'huf';
    const BUSINESS_TYPE_PVT_LTD          = 'pvt_ltd';
    const BUSINESS_TYPE_LTD              = 'ltd';
    const BUSINESS_TYPE_ONE_PERSON_COMPANY   = 'one_person_company';
    const BUSINESS_TYPE_AOP              = 'aop';
    const BUSINESS_TYPE_TRUST            = 'trust';
    const BUSINESS_TYPE_SOCIETY          = 'society';
    const BUSINESS_TYPE_OTHER            = 'other';


    const GENDER_MALE   = 'male';
    const GENDER_FEMALE = 'female';
    const LANGUAGE_EN   = 'en';

    protected $fillable = [
        'user_id',
        'mobile_no',
        'whatsapp_no',
        'gender',
        'business_type',
        'preferred_language',
        'pan_no',
        'gst_no',
        'address',
        'profile_image',
        'pan_card_file',
        'gst_certificate_file',
        'alternative_email',
        'TAN_no',
        'trade_name',
        'city',
        'state',
        'district',
        'pincode',
        'address_2',
        'roundoff_side',
        'roundoff_ledger_id',
        'roundoff_ledger_name'
    ];

    // keep your original list if other code uses it
    public static $businessTypes = [
        self::BUSINESS_TYPE_INDIVIDUAL,
        self::BUSINESS_TYPE_PARTNERSHIP_FIRM,
        self::BUSINESS_TYPE_LLP,
        self::BUSINESS_TYPE_PVT_LTD,
        self::BUSINESS_TYPE_HUF,
        self::BUSINESS_TYPE_TRUST,
        self::BUSINESS_TYPE_LTD,
        self::BUSINESS_TYPE_ONE_PERSON_COMPANY,
        self::BUSINESS_TYPE_AOP,
        self::BUSINESS_TYPE_SOCIETY,
        self::BUSINESS_TYPE_OTHER,
    ];

    //public static $genders   = [self::GENDER_MALE, self::GENDER_FEMALE];
    public const GENDERS = [
        self::GENDER_MALE,
        self::GENDER_FEMALE,
    ];
    public static $languages = [self::LANGUAGE_EN];

    // ✅ add labeled constant for Blade/UI
    public const BUSINESS_TYPES = [
        self::BUSINESS_TYPE_INDIVIDUAL       => 'Individual',
        self::BUSINESS_TYPE_PARTNERSHIP_FIRM => 'Partnership Firm',
        self::BUSINESS_TYPE_LLP              => 'LLP',
        self::BUSINESS_TYPE_HUF               => 'HUF',
        self::BUSINESS_TYPE_PVT_LTD          => 'Pvt Ltd',
        self::BUSINESS_TYPE_TRUST            => 'Trust',
        self::BUSINESS_TYPE_LTD              => 'Limited Company',
        self::BUSINESS_TYPE_ONE_PERSON_COMPANY   => 'One Person Company',
        self::BUSINESS_TYPE_AOP              => 'AOP',
        self::BUSINESS_TYPE_SOCIETY          => 'Society',
        self::BUSINESS_TYPE_OTHER            => 'Other',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function profileImage()
    {
        return $this->morphOne(File::class, 'attachable');
    }

    protected static function booted()
    {
        static::saving(function ($profile) {
            if ($profile->mobile_no && strlen($profile->mobile_no) !== 10) {
                throw new \Exception('Mobile number must be 10 digits');
            }
            if ($profile->whatsapp_no && strlen($profile->whatsapp_no) !== 10) {
                throw new \Exception('WhatsApp number must be 10 digits');
            }
            if ($profile->pan_no) {
                if (strlen($profile->pan_no) !== 10) throw new \Exception('PAN number must be 10 characters');
                if (!preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/', $profile->pan_no)) {
                    throw new \Exception('Invalid PAN number format');
                }
            }
            if ($profile->gst_no) {
                if (strlen($profile->gst_no) !== 15) throw new \Exception('GST number must be 15 characters');
                if (!preg_match('/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{3}$/', $profile->gst_no)) {
                    throw new \Exception('Invalid GST number format');
                }
            }
        });
    }
}
