<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

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
            'gender' => 'sometimes|nullable|in:male,female,other',
            'marital_status' => 'sometimes|nullable|in:single,married,divorced,widowed',
            'religion' => 'sometimes|nullable|string|max:50',
        ];
    }
}