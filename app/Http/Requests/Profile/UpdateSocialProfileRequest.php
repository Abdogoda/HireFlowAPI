<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSocialProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'platform' => 'sometimes|string|max:100',
            'username' => 'sometimes|string|max:255',
            'profile_url' => 'sometimes|url|nullable',
            'is_primary' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'platform.max' => 'Platform must not exceed 100 characters',
            'username.max' => 'Username must not exceed 255 characters',
            'profile_url.url' => 'Profile URL must be a valid URL',
            'is_primary.boolean' => 'Is primary must be true or false',
        ];
    }
}