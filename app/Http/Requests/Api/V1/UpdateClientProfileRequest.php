<?php
namespace App\Http\Requests\Api\V1;

use App\Models\Profile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClientProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'short_name' => ['nullable', 'string', 'max:100'],
            'profile.business_type' => [
                'required',
                Rule::in(array_keys(Profile::BUSINESS_TYPES)),
            ],
            'profile.pan_no' => ['nullable', 'string', 'max:20'],
            'profile.gst_no' => ['nullable', 'string', 'max:30'],
            'profile.mobile_no' => ['required', 'digits:10'],
            'profile.whatsapp_no' => ['nullable', 'digits:10'],
            'profile.gender' => ['nullable', Rule::in(Profile::GENDERS)],
            'profile.preferred_language' => ['nullable', Rule::in(Profile::$languages)],
            'profile.address' => ['nullable', 'string', 'max:500'],
            'profile.alternative_email' => ['nullable', 'email', 'max:255'],
            'profile.profile_image' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,heic,heif',
                'max:2048',
            ],
        ];
    }
}
