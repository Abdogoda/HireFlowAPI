<?php

namespace App\Http\Requests\Vacancy;

use App\Enums\Vacancy\EmploymentType;
use App\Enums\Vacancy\ExperienceLevel;
use App\Enums\Vacancy\VacancyStatus;
use App\Enums\Vacancy\WorkMode;
use Illuminate\Foundation\Http\FormRequest;

class StoreVacancyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_id' => 'required|exists:companies,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'requirements' => 'required|string',
            'responsibilities' => 'required|string',
            'employment_type' => 'required|in:' . EmploymentType::toString(),
            'experience_level' => 'required|in:' . ExperienceLevel::toString(),
            'salary_min' => 'sometimes|nullable|numeric|min:0',
            'salary_max' => 'sometimes|nullable|numeric|min:0|gte:salary_min',
            'location' => 'required|string|max:255',
            'work_mode' => 'required|in:' . WorkMode::toString(),
            'status' => 'sometimes|in:' . VacancyStatus::toString(),
            'application_deadline' => 'required|date|after_or_equal:today',
            'published_at' => 'sometimes|nullable|date',
            'closed_at' => 'sometimes|nullable|date',
        ];
    }
}