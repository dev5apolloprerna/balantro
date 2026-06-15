<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check(); // Or more specific authorization logic
    }

    public function rules(): array
    {
        $rules = [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            // 'name' => ['required', 'string', 'max:255'], // Uncomment if you have a name field
        ];

        if (Auth::user() && Auth::user()->isSuperAdmin()) {
            $rules['role'] = ['required', 'string', Rule::in(['client', 'super_admin', 'manager', 'supervisor', 'data_entry_operator'])];
        } else {
            // If not super_admin, role might be implicitly set or default
            // Or, if role is not allowed to be set by non-super_admin, remove it from request
            $rules['role'] = ['sometimes', 'string', Rule::in(['client'])]; // Example: non-super_admin can only create 'client'
        }

        return $rules;
    }
}

