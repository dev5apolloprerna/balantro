<?php

namespace Tests\Feature;

use App\Http\Controllers\Auth\RegisterController;
use App\Mail\RegistrationOtpMail;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RegistrationOtpMailTest extends TestCase
{
    public function test_registration_otp_email_contains_code_and_expiry(): void
    {
        $html = (new RegistrationOtpMail('Test Client', '123456'))->render();

        $this->assertStringContainsString('Hi Test Client', $html);
        $this->assertStringContainsString('123456', $html);
        $this->assertStringContainsString('expires in 10 minutes', $html);
    }

    public function test_registration_otp_routes_are_available_to_guests(): void
    {
        $this->assertTrue(route('registration.otp.show') !== '');
        $this->assertTrue(route('registration.otp.verify') !== '');
        $this->assertTrue(route('registration.otp.resend') !== '');

        $this->assertSame(
            RegisterController::class.'@showOtpForm',
            Route::getRoutes()->getByName('registration.otp.show')->getActionName()
        );
        $this->assertSame(
            RegisterController::class.'@verifyOtp',
            Route::getRoutes()->getByName('registration.otp.verify')->getActionName()
        );
        $this->assertSame(
            RegisterController::class.'@resendOtp',
            Route::getRoutes()->getByName('registration.otp.resend')->getActionName()
        );
    }
}