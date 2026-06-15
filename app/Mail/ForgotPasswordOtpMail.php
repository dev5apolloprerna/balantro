<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ForgotPasswordOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public string $name, public string $otp, public int $expiryMinutes = 10) {}

    public function build()
    {
        return $this->subject('Your password reset OTP')
            ->view('emails.password_otp')
            ->with([
                'name' => $this->name,
                'otp'  => $this->otp,
                'expiryMinutes' => $this->expiryMinutes,
            ]);
    }
}
