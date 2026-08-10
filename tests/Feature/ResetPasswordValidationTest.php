<?php

namespace Tests\Feature;

use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ResetPasswordValidationTest extends TestCase
{
    public function test_reset_password_requires_letters_numbers_and_symbols(): void
    {
        $rules = (new TestResetPasswordController)->validationRules();

        $validator = Validator::make([
            'token' => 'reset-token',
            'email' => 'person@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ], $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    public function test_reset_password_accepts_a_password_that_meets_the_guidance(): void
    {
        $rules = (new TestResetPasswordController)->validationRules();

        $validator = Validator::make([
            'token' => 'reset-token',
            'email' => 'person@example.com',
            'password' => 'Balantro8!',
            'password_confirmation' => 'Balantro8!',
        ], $rules);

        $this->assertFalse($validator->fails());
    }
}

class TestResetPasswordController extends ResetPasswordController
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function validationRules(): array
    {
        return $this->rules();
    }
}