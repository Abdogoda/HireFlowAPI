<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'sometimes|nullable|string|max:255|unique:companies,slug',
            'description' => 'sometimes|nullable|string',
            'logo' => 'sometimes|nullable|string|max:255',
            'website' => 'sometimes|nullable|url',
            'industry' => 'sometimes|nullable|string|max:255',
            'company_size' => 'sometimes|nullable|string|max:255',
            'founded_year' => 'sometimes|nullable|integer|min:1800|max:' . now()->year,
            'location' => 'sometimes|nullable|string|max:255',
            'email' => 'sometimes|nullable|email|max:255',
            'phone' => 'sometimes|nullable|string|max:255',
            'is_verified' => 'sometimes|boolean',
        ];
    }
}