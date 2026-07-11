<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public object $user;
    public $plainPassword;

    public function __construct($user, ?string $plainPassword = null)
    {
         $this->user = (object) [
            'name' => $user->name,
            'email' => $user->email,
        ];
        $this->plainPassword = $plainPassword;
    }

    public function build()
    {
        return $this->subject('Your Balantro Account Setup Details')
            ->to($this->user->email)
            ->view('emails.welcome')
            ->with([
                'plainPassword' => $this->plainPassword,
            ]);
    }
}
