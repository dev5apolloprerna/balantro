<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as EloquentModel;

/**
 * A self-registration waiting for its mobile OTP. Becomes a `users` row only
 * after verification, then is deleted.
 */
class RegistrationRequest extends EloquentModel
{
    protected $table = 'registration_requests';

    protected $fillable = [
        'name',
        'email',
        'mobile',
        'token_hash',
        'otp_hash',
        'otp_expires_at',
        'otp_attempts',
        'otp_sends',
        'last_sent_at',
        'locked_until',
        'ip',
    ];

    protected $hidden = ['token_hash', 'otp_hash'];

    protected $casts = [
        'otp_expires_at' => 'datetime',
        'last_sent_at' => 'datetime',
        'locked_until' => 'datetime',
        'otp_attempts' => 'integer',
        'otp_sends' => 'integer',
    ];
}
