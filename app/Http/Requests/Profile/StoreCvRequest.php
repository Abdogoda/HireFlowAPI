<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class StoreCvRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:pdf,doc,docx|max:10240', // 10MB
            'title' => 'sometimes|string|nullable|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'CV file is required',
            'file.file' => 'Invalid file',
            'file.mimes' => 'CV must be pdf, doc, or docx',
            'file.max' => 'CV size must not exceed 10MB',
            'title.string' => 'Title must be a string',
            'title.max' => 'Title must not exceed 255 characters',
        ];
    }
}