<?php

namespace Tests\Unit;

use App\Models\Profile;
use PHPUnit\Framework\TestCase;

class ProfileEditValidationTest extends TestCase
{
    public function test_business_type_dropdown_and_update_validation_share_the_model_options(): void
    {
        $form = file_get_contents(dirname(__DIR__, 2).'/resources/views/profiles/_form.blade.php');
        $controller = file_get_contents(dirname(__DIR__, 2).'/app/Http/Controllers/ProfilesController.php');

        $this->assertArrayHasKey('trust', Profile::BUSINESS_TYPES);
        $this->assertStringContainsString('Profile::BUSINESS_TYPES', $form);
        $this->assertStringContainsString('Rule::in(array_keys(Profile::BUSINESS_TYPES))', $controller);
    }

    public function test_state_must_match_an_existing_dropdown_option(): void
    {
        $controller = file_get_contents(dirname(__DIR__, 2).'/app/Http/Controllers/ProfilesController.php');

        $this->assertStringContainsString("'state_name' => 'required|string|max:150'", $controller);
        $this->assertStringContainsString('Please select a state from the list.', $controller);
        $this->assertStringNotContainsString("DB::table('state')->insert", $controller);

        $form = file_get_contents(dirname(__DIR__, 2).'/resources/views/profiles/_form.blade.php');
        $this->assertStringContainsString("input.setCustomValidity(option || input.value === ''", $form);
    }

    public function test_edit_form_repopulates_profile_and_previous_submission_values(): void
    {
        $form = file_get_contents(dirname(__DIR__, 2).'/resources/views/profiles/_form.blade.php');

        $this->assertStringContainsString("old('trade_name', \$profile->trade_name", $form);
        $this->assertStringContainsString("old('address_2', \$profile->address_2)", $form);
        $this->assertStringContainsString("old('state_name'", $form);
        $this->assertStringContainsString("old('district_name'", $form);
        $this->assertStringContainsString("old('city_name'", $form);
    }
}