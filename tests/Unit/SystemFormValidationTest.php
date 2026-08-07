<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class SystemFormValidationTest extends TestCase
{
    public function test_validation_partial_is_loaded_by_every_browser_form_layout(): void
    {
        $layouts = [
            'resources/views/layouts/app.blade.php',
            'resources/views/layouts/application.blade.php',
            'resources/views/layouts/client.blade.php',
            'resources/views/layouts/data_entry_operator.blade.php',
            'resources/views/layouts/front.blade.php',
            'resources/views/layouts/manager.blade.php',
            'resources/views/layouts/super_admin.blade.php',
            'resources/views/layouts/supervisor.blade.php',
            'resources/views/auth/layouts/app.blade.php',
            'resources/views/auth/layouts/newApp.blade.php',
        ];

        foreach ($layouts as $layout) {
            $contents = file_get_contents(dirname(__DIR__, 2).'/'.$layout);

            $this->assertStringContainsString(
                "@include('shared.form_validation')",
                $contents,
                "The global validation partial is missing from {$layout}."
            );
        }
    }

    public function test_mailer_layout_does_not_load_browser_form_validation(): void
    {
        $contents = file_get_contents(dirname(__DIR__, 2).'/resources/views/layouts/mailer.blade.php');

        $this->assertStringNotContainsString("@include('shared.form_validation')", $contents);
    }

    public function test_registration_uses_only_field_level_validation_feedback(): void
    {
        $layout = file_get_contents(dirname(__DIR__, 2).'/resources/views/auth/layouts/app.blade.php');
        $registration = file_get_contents(dirname(__DIR__, 2).'/resources/views/auth/register.blade.php');

        $this->assertStringContainsString("View::hasSection('field_validation_only')", $layout);
        $this->assertStringContainsString("@section('field_validation_only', true)", $registration);
        $this->assertStringNotContainsString('$errors->all()', $registration);
        $this->assertStringContainsString("@error('email')", $registration);
        $this->assertStringContainsString("@error('password')", $registration);
    }
    
    public function test_validation_script_guards_submission_and_displays_server_errors(): void
    {
        $script = file_get_contents(dirname(__DIR__, 2).'/public/js/form-validation.js');

        $this->assertStringContainsString('event.preventDefault()', $script);
        $this->assertStringContainsString('system-validation-errors', $script);
        $this->assertStringContainsString('document.addEventListener("invalid"', $script);
        $this->assertStringContainsString('document.addEventListener("submit"', $script);
        $this->assertStringContainsString('errorForm.insertAdjacentElement("beforebegin", summary)', $script);
    }

    public function test_validation_script_handles_all_native_form_submission_paths_safely(): void
    {
        $script = file_get_contents(dirname(__DIR__, 2).'/public/js/form-validation.js');

        $this->assertStringContainsString('event.submitter?.formNoValidate', $script);
        $this->assertStringContainsString('!field.validity.valid', $script);
        $this->assertStringNotContainsString('!field.checkValidity()', $script);
        $this->assertStringContainsString('field.type === "radio"', $script);
        $this->assertStringContainsString('form.querySelectorAll(`.${errorClass}`).forEach', $script);
        $this->assertStringContainsString('scrollIntoView({ behavior: "smooth", block: "center" })', $script);
        $this->assertStringContainsString('syncConfirmationValidity(form)', $script);
        $this->assertStringContainsString('confirmation.setCustomValidity(', $script);
    }

    public function test_password_recovery_forms_use_system_frontend_validation(): void
    {
        $reset = file_get_contents(dirname(__DIR__, 2).'/resources/views/auth/passwords/reset.blade.php');
        $forgot = file_get_contents(dirname(__DIR__, 2).'/resources/views/auth/passwords/email.blade.php');

        $this->assertStringContainsString("@extends('auth.layouts.app')", $reset);
        $this->assertStringNotContainsString('novalidate', $reset);
        $this->assertStringNotContainsString('novalidate', $forgot);
        $this->assertStringContainsString('name="password" required minlength="8"', $reset);
        $this->assertStringContainsString('name="password_confirmation" required minlength="8"', $reset);
    }
}