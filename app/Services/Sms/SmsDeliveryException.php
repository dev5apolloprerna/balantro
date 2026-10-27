<?php

namespace App\Services\Sms;

use RuntimeException;

/**
 * The SMS gateway did not accept a message. The message is safe to show in
 * logs: it never contains the OTP, the mobile number or gateway credentials.
 */
class SmsDeliveryException extends RuntimeException
{
}
