<?php

namespace App\Models;

use App\Enums\Vacancy\EmploymentType;
use App\Enums\Vacancy\ExperienceLevel;
use App\Enums\Vacancy\VacancyStatus;
use App\Enums\Vacancy\WorkMode;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([ 'title', 'description', 'requirements', 'responsibilities', 'created_by', 'company_id', 'published_at', 'closed_at', 'employment_type', 'experience_level', 'salary_min', 'salary_max', 'location', 'work_mode', 'status', 'application_deadline'])]
class Vacancy extends Model
{
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'closed_at' => 'datetime',
            'application_deadline' => 'date',
            'employment_type' => EmploymentType::class,
            'experience_level' => ExperienceLevel::class,
            'work_mode' => WorkMode::class,
            'status' => VacancyStatus::class,
            'salary_min' => 'decimal:2',
            'salary_max' => 'decimal:2',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}