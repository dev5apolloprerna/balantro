<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserMailer extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The user instance.
     *
     * @var Authenticatable
     */
    public $user;

    /**
     * Create a new message instance.
     *
     * @param Authenticatable $user
     */
    public function __construct(Authenticatable $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('users.mailer.layout');
    }
}