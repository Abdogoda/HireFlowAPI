<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\Profile\PictureType;

class StorePictureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'picture' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB
            'type' => ['required', new Enum(PictureType::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'picture.required' => 'Picture file is required',
            'picture.image' => 'Picture must be an image',
            'picture.mimes' => 'Picture must be jpeg, png, jpg, gif, or webp',
            'picture.max' => 'Picture size must not exceed 5MB',
            'type.required' => 'Type is required',
            'type.in' => 'Type must be profile or thumbnail',
        ];
    }
}