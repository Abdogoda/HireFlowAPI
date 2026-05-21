<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class StoreAvatarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB
        ];
    }

    public function messages(): array
    {
        return [
            'avatar.required' => 'Avatar file is required',
            'avatar.image' => 'Avatar must be an image',
            'avatar.mimes' => 'Avatar must be jpeg, png, jpg, gif, or webp',
            'avatar.max' => 'Avatar size must not exceed 5MB',
        ];
    }
}