<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class SystemFormValidationTest extends TestCase
{
    public function test_validation_partial_is_loaded_by_every_form_layout(): void
    {
        $layouts = [
            'resources/views/layouts/app.blade.php',
            'resources/views/layouts/application.blade.php',
            'resources/views/layouts/client.blade.php',
            'resources/views/layouts/data_entry_operator.blade.php',
            'resources/views/layouts/front.blade.php',
            'resources/views/layouts/mailer.blade.php',
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

    public function test_validation_script_guards_submission_and_displays_server_errors(): void
    {
        $script = file_get_contents(dirname(__DIR__, 2).'/public/js/form-validation.js');

        $this->assertStringContainsString('event.preventDefault()', $script);
        $this->assertStringContainsString('system-validation-errors', $script);
        $this->assertStringContainsString('document.addEventListener("invalid"', $script);
        $this->assertStringContainsString('document.addEventListener("submit"', $script);
    }
}