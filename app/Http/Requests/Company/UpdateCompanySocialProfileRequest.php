<?php

namespace App\Http\Requests\Company;

use App\Enums\Company\SocialProfileType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateCompanySocialProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'social_profile_type' => ['sometimes', new Enum(SocialProfileType::class)],
            'profile_url'         => 'sometimes|url',
        ];
    }

    public function messages(): array
    {
        return [
            'social_profile_type.in' => 'Social profile type must be a valid type',
            'profile_url.url'        => 'Profile URL must be a valid URL',
        ];
    }
}
