<?php

namespace Tests\Unit;

use App\Http\Requests\Api\V1\UpdateClientProfileRequest;
use App\Models\Profile;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class UpdateClientProfileRequestTest extends TestCase
{
    public function test_every_business_type_returned_by_the_api_is_valid_for_profile_updates(): void
    {
        foreach (array_keys(Profile::BUSINESS_TYPES) as $businessType) {
            $validator = Validator::make(
                $this->validPayload($businessType),
                (new UpdateClientProfileRequest)->rules()
            );

            $this->assertFalse(
                $validator->fails(),
                "Expected business type [{$businessType}] to be valid: {$validator->errors()}"
            );
        }
    }

    public function test_unknown_business_type_is_rejected(): void
    {
        $validator = Validator::make(
            $this->validPayload('corporation'),
            (new UpdateClientProfileRequest)->rules()
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('profile.business_type', $validator->errors()->toArray());
    }

    private function validPayload(string $businessType): array
    {
        return [
            'profile' => [
                'business_type' => $businessType,
                'mobile_no' => '9876543210',
                'address' => '123 Test Street',
            ],
        ];
    }
}