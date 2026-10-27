<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Sends the generated password to a new self-registered user.
 * Sent synchronously (not queued) so the password is never written to the
 * jobs table. The subject line never contains the password.
 */
class RegistrationPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $name,
        public string $email,
        public string $password,
    ) {
    }

    public function build()
    {
        return $this->subject('Your Balantro account details')
            ->view('emails.registration_password');
    }
}
