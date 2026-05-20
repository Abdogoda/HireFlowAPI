<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|string|email|exists:users',
        ];
    }

    public function messages(): array
    {
        return [
            'email.exists' => 'We could not find a user with that email address.',
        ];
    }
}
