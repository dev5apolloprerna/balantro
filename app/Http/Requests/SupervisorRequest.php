<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupervisorRequest extends FormRequest
{
    public function authorize()
    {
        // Adjust authorization logic as needed
        return auth()->check();
    }

    public function rules()
    {
        // We just require name by default; more rules may be added
        return [
            'supervisor.name' => 'sometimes|string|max:255',
            'user.name' => 'sometimes|string|max:255',
            'name' => 'required_without_all:supervisor.name,user.name|string|max:255',
            'email' => ['required', 'email:rfc,dns', 'max:255', 'unique:users,email'],
        ];
    }

    public function messages()
    {
        return [
            'email.email' => 'Please enter an email address with a valid domain.',
        ];
    }
}
