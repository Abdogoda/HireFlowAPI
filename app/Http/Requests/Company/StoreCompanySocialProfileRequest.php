<?php

namespace App\Http\Requests\Company;

use App\Enums\SocialProfileType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreCompanySocialProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'social_profile_type' => ['required', new Enum(SocialProfileType::class)],
            'profile_url'         => 'required|url',
        ];
    }

    public function messages(): array
    {
        return [
            'social_profile_type.required' => 'Social profile type is required',
            'social_profile_type.in'       => 'Social profile type must be a valid type',
            'profile_url.required'         => 'Profile URL is required',
            'profile_url.url'              => 'Profile URL must be a valid URL',
        ];
    }
}
