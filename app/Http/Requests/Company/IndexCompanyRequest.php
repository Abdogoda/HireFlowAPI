<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class IndexCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => 'sometimes|nullable|string|max:255',
            'industry' => 'sometimes|nullable|string|max:255',
            'location' => 'sometimes|nullable|string|max:255',
            'company_size' => 'sometimes|nullable|string|max:255',
            'is_verified' => 'sometimes|boolean',
            'founded_year' => 'sometimes|nullable|integer|min:1800|max:' . now()->year,
            'sort_by' => 'sometimes|nullable|in:name,created_at,founded_year',
            'sort_direction' => 'sometimes|nullable|in:asc,desc',
        ];
    }
}
