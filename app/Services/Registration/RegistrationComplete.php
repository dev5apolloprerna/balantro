<?php

namespace App\Services\Registration;

use App\Models\User;

/** Result of a successful OTP check: the new account, and whether the password e-mail went out. */
final class RegistrationComplete
{
    public function __construct(
        public readonly User $user,
        public readonly bool $passwordEmailed,
    ) {
    }
}
