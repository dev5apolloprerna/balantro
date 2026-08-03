<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegistrationOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $name,
        public string $otp,
        public int $expiryMinutes = 10
    ) {}

    public function build()
    {
        return $this->subject('Verify your Balantro account')
            ->view('emails.registration_otp');
    }
}