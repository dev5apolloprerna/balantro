<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Ensure the user is authorized to update this specific user
        // e.g., Auth::user()->can('update', $this->route('user'));
        return Auth::check();
    }

    public function rules(): array
    {
        $userId = $this->route('user')->id;

        $rules = [
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($userId)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            // 'name' => ['required', 'string', 'max:255'], // Uncomment if you have a name field
        ];

        if (Auth::user() && Auth::user()->isSuperAdmin()) {
            $rules['role'] = ['required', 'string', Rule::in(['client', 'super_admin', 'manager', 'supervisor', 'data_entry_operator'])];
        } else {
            // If not super_admin, role should not be changeable via this request
            $rules['role'] = ['sometimes', 'string', Rule::in(['client'])];
        }

        return $rules;
    }
}