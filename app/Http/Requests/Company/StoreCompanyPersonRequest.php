<?php

namespace App\Http\Requests\Company;

use App\Enums\Company\CompanyRoles;
use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyPersonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'member_id' => 'required|exists:users,id',
            'company_role' => 'required|in:' . CompanyRoles::toString(),
            'position' => 'required|string|max:255',
            'information' => 'sometimes|nullable|string',
            'start_date' => 'sometimes|nullable|date',
            'end_date' => 'sometimes|nullable|date|after_or_equal:start_date',
            'is_current_position' => 'sometimes|boolean',
        ];
    }
}