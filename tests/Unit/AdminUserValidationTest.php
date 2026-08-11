<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class AdminUserValidationTest extends TestCase
{
    public function test_manager_and_supervisor_emails_require_a_resolvable_domain(): void
    {
        $files = [
            'app/Http/Controllers/ManagersController.php',
            'app/Http/Requests/SupervisorRequest.php',
        ];

        foreach ($files as $file) {
            $contents = file_get_contents(dirname(__DIR__, 2).'/'.$file);

            $this->assertStringContainsString(
                'email:rfc,dns',
                $contents,
                "The email validation in {$file} must check both syntax and the domain.",
            );
        }
    }

    public function test_create_forms_reject_one_character_top_level_domains_in_the_browser(): void
    {
        $forms = [
            'resources/views/admin/managers/create_modal.blade.php',
            'resources/views/admin/supervisors/create_modal.blade.php',
            'resources/views/admin/data_entry_operators/_modals.blade.php',
        ];

        foreach ($forms as $form) {
            $contents = file_get_contents(dirname(__DIR__, 2).'/'.$form);

            $this->assertStringContainsString("@include('admin.shared.user_form_modal'", $contents);
        }

        $sharedForm = file_get_contents(
            dirname(__DIR__, 2).'/resources/views/admin/shared/user_form_modal.blade.php',
        );

        $this->assertStringContainsString('pattern="[^\s@]+@[^\s@]+\.[A-Za-z]{2,}"', $sharedForm);
        $this->assertStringContainsString('maxlength="255"', $sharedForm);
    }
}
