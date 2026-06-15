<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PermissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Adjust according to your authorization logic.
     */
    public function authorize(): bool
    {
        // Replace with proper authorization logic, e.g. checking user roles.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'action' => 'nullable|string|max:255',
            'subject' => 'nullable|string|max:255',
            'conditions' => 'nullable|string',
        ];
    }
}
