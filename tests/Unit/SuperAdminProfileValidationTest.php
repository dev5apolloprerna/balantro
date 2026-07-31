<?php

namespace Tests\Unit;

use App\Models\UserProfile;
use PHPUnit\Framework\TestCase;

class SuperAdminProfileValidationTest extends TestCase
{
    public function test_every_gender_offered_by_the_profile_form_is_accepted(): void
    {
        $this->assertSame(['male', 'female', 'other'], UserProfile::GENDERS);

        $controller = file_get_contents(
            dirname(__DIR__, 2).'/app/Http/Controllers/ProfilesController.php'
        );

        $this->assertStringContainsString(
            'Rule::in(UserProfile::GENDERS)',
            $controller,
            'Profile updates must accept every gender option shown in the form.',
        );
    }
}