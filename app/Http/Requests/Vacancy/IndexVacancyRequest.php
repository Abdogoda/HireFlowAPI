<?php

namespace App\Http\Requests\Vacancy;

use App\Enums\Vacancy\EmploymentType;
use App\Enums\Vacancy\ExperienceLevel;
use App\Enums\Vacancy\VacancyStatus;
use App\Enums\Vacancy\WorkMode;
use Illuminate\Foundation\Http\FormRequest;

class IndexVacancyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => 'sometimes|nullable|string|max:255',
            'company_id' => 'sometimes|nullable|exists:companies,id',
            'employment_type' => 'sometimes|nullable|in:' . EmploymentType::toString(),
            'experience_level' => 'sometimes|nullable|in:' . ExperienceLevel::toString(),
            'work_mode' => 'sometimes|nullable|in:' . WorkMode::toString(),
            'status' => 'sometimes|nullable|in:' . VacancyStatus::toString(),
            'location' => 'sometimes|nullable|string|max:255',
            'min_salary' => 'sometimes|nullable|numeric|min:0',
            'max_salary' => 'sometimes|nullable|numeric|min:0',
            'sort_by' => 'sometimes|nullable|in:title,created_at,published_at,application_deadline,salary_min,salary_max,updated_at',
            'sort_direction' => 'sometimes|nullable|in:asc,desc',
            'per_page' => 'sometimes|nullable|integer|min:1|max:100',
        ];
    }
}