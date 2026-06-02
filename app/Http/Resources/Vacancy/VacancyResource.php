<?php

namespace App\Http\Resources\Vacancy;

use App\Http\Resources\User\UserSimpleResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VacancyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'requirements' => $this->requirements,
            'responsibilities' => $this->responsibilities,
            'created_by' => $this->creator?->id ?? $this->created_by,
            'creator' => $this->relationLoaded('creator') ? new UserSimpleResource($this->creator) : null,
            'company' => $this->relationLoaded('company') ? [
                'id' => $this->company->id,
                'name' => $this->company->name,
                'slug' => $this->company->slug,
                'location' => $this->company->location,
            ] : null,
            'published_at' => $this->published_at,
            'closed_at' => $this->closed_at,
            'employment_type' => $this->employment_type?->value,
            'experience_level' => $this->experience_level?->value,
            'salary_min' => $this->salary_min,
            'salary_max' => $this->salary_max,
            'location' => $this->location,
            'work_mode' => $this->work_mode?->value,
            'status' => $this->status?->value,
            'application_deadline' => $this->application_deadline,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}