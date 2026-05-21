<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class StoreSocialProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'social_profile_type' => 'required|integer|in:1,2,3,4,5,6,7,8,9,10',
            'profile_url' => 'required|url',
        ];
    }

    public function messages(): array
    {
        return [
            'social_profile_type.required' => 'Social profile type is required',
            'social_profile_type.in' => 'Social profile type must be a valid type',
            'profile_url.required' => 'Profile URL is required',
            'profile_url.url' => 'Profile URL must be a valid URL',
        ];
    }
}
