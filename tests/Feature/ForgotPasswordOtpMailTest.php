<?php

namespace Tests\Feature;

use App\Mail\ForgotPasswordOtpMail;
use Tests\TestCase;

class ForgotPasswordOtpMailTest extends TestCase
{
    public function test_password_reset_otp_email_renders_without_a_validation_error_bag(): void
    {
        $html = (new ForgotPasswordOtpMail('Test User', '123456'))->render();

        $this->assertStringContainsString('Hi Test User', $html);
        $this->assertStringContainsString('123456', $html);
        $this->assertStringContainsString('expire in 10 minutes', $html);
    }
}