<?php

namespace App\Http\Requests\Company;

use App\Enums\Company\CompanyRoles;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyPersonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_role' => 'sometimes|in:' . CompanyRoles::toString(),
            'position' => 'sometimes|required|string|max:255',
            'information' => 'sometimes|nullable|string',
            'start_date' => 'sometimes|nullable|date',
            'end_date' => 'sometimes|nullable|date|after_or_equal:start_date',
            'is_current_position' => 'sometimes|boolean',
        ];
    }
}