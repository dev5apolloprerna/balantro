<?php

namespace App\Services\Registration;

use RuntimeException;

/**
 * A registration step failed for a reason the user can act on. The message is
 * written for the user and never contains the OTP or the password.
 */
class RegistrationException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly string $field = 'email',
        public readonly string $reason = 'invalid',
        public readonly int $status = 422,
    ) {
        parent::__construct($message);
    }
}
