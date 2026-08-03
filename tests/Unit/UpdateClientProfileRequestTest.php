<?php

namespace Tests\Unit;

use App\Http\Requests\Api\V1\UpdateClientProfileRequest;
use App\Models\Profile;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class UpdateClientProfileRequestTest extends TestCase
{
    public function test_request_class_is_available_through_composer_autoloading(): void
    {
        $this->assertTrue(class_exists(UpdateClientProfileRequest::class));
    }

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

    public function test_client_profile_payload_accepts_supported_gender_language_and_empty_address(): void
    {
        $payload = $this->validPayload(Profile::BUSINESS_TYPE_AOP);
        $payload['profile']['gender'] = Profile::GENDER_MALE;
        $payload['profile']['preferred_language'] = Profile::LANGUAGE_EN;
        $payload['profile']['address'] = '';

        $validator = Validator::make(
            $payload,
            (new UpdateClientProfileRequest)->rules()
        );

        $this->assertFalse($validator->fails(), (string) $validator->errors());
    }

    public function test_profile_image_must_be_uploaded_as_a_file(): void
    {
        $payload = $this->validPayload(Profile::BUSINESS_TYPE_AOP);
        $payload['profile']['profile_image'] = 'IMG_20210123_081828_415.jpg';

        $validator = Validator::make(
            $payload,
            (new UpdateClientProfileRequest)->rules()
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('profile.profile_image', $validator->errors()->toArray());
    }

    public function test_validation_failure_returns_json_without_an_accept_header(): void
    {
        $validator = Validator::make([
            'profile' => [
                'business_type' => Profile::BUSINESS_TYPE_AOP,
                'mobile_no' => '9876543210',
                'profile_image' => 'IMG_20210123_081828_415.jpg',
            ],
        ], (new UpdateClientProfileRequest)->rules());

        try {
            (new TestableUpdateClientProfileRequest)->failWith($validator);
            $this->fail('Expected an HTTP response exception.');
        } catch (HttpResponseException $exception) {
            $response = $exception->getResponse();

            $this->assertSame(422, $response->getStatusCode());
            $this->assertSame('application/json', $response->headers->get('Content-Type'));
            $this->assertSame([
                'status' => false,
                'message' => 'Validation error',
                'errors' => [
                    'profile.profile_image' => [
                        'The profile.profile image field must be an image.',
                        'The profile.profile image field must be a file of type: jpeg, png, jpg, heic, heif.',
                    ],
                ],
                'code' => 422,
            ], json_decode($response->getContent(), true));
        }
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

class TestableUpdateClientProfileRequest extends UpdateClientProfileRequest
{
    public function failWith(ValidatorContract $validator): void
    {
        $this->failedValidation($validator);
    }
}