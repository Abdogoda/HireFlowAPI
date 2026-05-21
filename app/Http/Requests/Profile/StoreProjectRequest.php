<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'sometimes|string|nullable',
            'url' => 'sometimes|url|nullable',
            'start_date' => 'sometimes|date|nullable',
            'end_date' => 'sometimes|date|nullable|after_or_equal:start_date',
            'technologies' => 'sometimes|string|nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Project title is required',
            'title.max' => 'Title must not exceed 255 characters',
            'url.url' => 'URL must be a valid URL',
            'end_date.after_or_equal' => 'End date must be after or equal to start date',
        ];
    }
}