<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\Users\Gender;
use App\Enums\Users\MaritalStatus;
use App\Enums\Users\Religion;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'bio' => 'sometimes|string|nullable',
            'phone_number' => 'sometimes|string|nullable|max:20',
            'address' => 'sometimes|string|nullable|max:255',
            'city' => 'sometimes|string|nullable|max:100',
            'state' => 'sometimes|string|nullable|max:100',
            'country' => 'sometimes|string|nullable|max:100',
            'gender' => ['sometimes', 'nullable', new Enum(Gender::class)],
            'marital_status' => ['sometimes', 'nullable', new Enum(MaritalStatus::class)],
            'religion' => ['sometimes', 'nullable', new Enum(Religion::class)],
        ];
    }
}