<?php

namespace App\Http\Requests\Vacancy;

use App\Enums\Vacancy\EmploymentType;
use App\Enums\Vacancy\ExperienceLevel;
use App\Enums\Vacancy\VacancyStatus;
use App\Enums\Vacancy\WorkMode;
use Illuminate\Foundation\Http\FormRequest;

class UpdateVacancyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_id' => 'sometimes|exists:companies,id',
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'requirements' => 'sometimes|string',
            'responsibilities' => 'sometimes|string',
            'employment_type' => 'sometimes|in:' . EmploymentType::toString(),
            'experience_level' => 'sometimes|in:' . ExperienceLevel::toString(),
            'salary_min' => 'sometimes|nullable|numeric|min:0',
            'salary_max' => 'sometimes|nullable|numeric|min:0|gte:salary_min',
            'location' => 'sometimes|string|max:255',
            'work_mode' => 'sometimes|in:' . WorkMode::toString(),
            'status' => 'sometimes|in:' . VacancyStatus::toString(),
            'application_deadline' => 'sometimes|date|after_or_equal:today',
            'published_at' => 'sometimes|nullable|date',
            'closed_at' => 'sometimes|nullable|date',
        ];
    }
}