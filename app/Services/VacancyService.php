<?php

namespace App\Services;

use App\Enums\Vacancy\VacancyStatus;
use App\Http\Resources\Vacancy\VacancyResource;
use App\Models\Company;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class VacancyService
{
    public function getVacancies(array $filters = []): LengthAwarePaginator
    {
        $query = Vacancy::query()->with(['company', 'creator.role']);

        $this->applyFilters($query, $filters);

        $sortBy = $filters['sort_by'] ?? 'published_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';
        $perPage = (int) ($filters['per_page'] ?? 15);

        return $query
            ->orderBy($sortBy, $sortDirection)
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getVacancy(Vacancy $vacancy): VacancyResource
    {
        return new VacancyResource($vacancy->load(['company', 'creator.role']));
    }

    public function createVacancy(User $user, Company $company, array $data): VacancyResource
    {
        $payload = $this->normalizePayload($data, $user, $company);

        $vacancy = $company->vacancies()->create($payload);

        return $this->getVacancy($vacancy);
    }

    public function updateVacancy(Vacancy $vacancy, array $data): VacancyResource
    {
        $payload = $this->normalizePayload($data, $vacancy->creator, $vacancy->company, $vacancy);

        $vacancy->update(array_filter([
            'company_id' => $payload['company_id'] ?? null,
            'title' => $payload['title'] ?? null,
            'description' => $payload['description'] ?? null,
            'requirements' => $payload['requirements'] ?? null,
            'responsibilities' => $payload['responsibilities'] ?? null,
            'employment_type' => $payload['employment_type'] ?? null,
            'experience_level' => $payload['experience_level'] ?? null,
            'salary_min' => $payload['salary_min'] ?? null,
            'salary_max' => $payload['salary_max'] ?? null,
            'location' => $payload['location'] ?? null,
            'work_mode' => $payload['work_mode'] ?? null,
            'status' => $payload['status'] ?? null,
            'application_deadline' => $payload['application_deadline'] ?? null,
            'published_at' => $payload['published_at'] ?? null,
            'closed_at' => $payload['closed_at'] ?? null,
        ], static fn ($value) => $value !== null));

        return $this->getVacancy($vacancy->fresh());
    }

    public function deleteVacancy(Vacancy $vacancy): bool
    {
        $vacancy->delete();

        return true;
    }

    private function normalizePayload(array $data, ?User $user = null, ?Company $company = null, ?Vacancy $vacancy = null): array
    {
        $payload = $data;

        if ($user !== null) {
            $payload['created_by'] = $user->id;
        }

        if ($company !== null) {
            $payload['company_id'] = $company->id;
        }

        $status = $payload['status'] ?? $this->currentStatus($vacancy) ?? VacancyStatus::DRAFT->value;

        // ensure payload contains a status so DB default isn't bypassed by nulls
        $payload['status'] = $status;

        if ($status === VacancyStatus::PUBLISHED->value && empty($payload['published_at']) && ($vacancy === null || $vacancy->published_at === null)) {
            $payload['published_at'] = now();
        }

        if ($status === VacancyStatus::CLOSED->value && empty($payload['closed_at']) && ($vacancy === null || $vacancy->closed_at === null)) {
            $payload['closed_at'] = now();
        }

        return $payload;
    }

    private function currentStatus(?Vacancy $vacancy): ?string
    {
        if ($vacancy === null || $vacancy->status === null) {
            return null;
        }

        return $vacancy->status?->value;
    }

    private function applyFilters(Builder $query, array $filters): void
    {
        if (!empty($filters['search'])) {
            $search = trim($filters['search']);

            $query->where(function (Builder $builder) use ($search): void {
                $builder->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhere('requirements', 'like', '%' . $search . '%')
                    ->orWhere('responsibilities', 'like', '%' . $search . '%')
                    ->orWhere('location', 'like', '%' . $search . '%')
                    ->orWhereHas('company', function (Builder $companyQuery) use ($search): void {
                        $companyQuery->where('name', 'like', '%' . $search . '%')
                            ->orWhere('slug', 'like', '%' . $search . '%');
                    });
            });
        }

        if (!empty($filters['company_id'])) {
            $query->where('company_id', $filters['company_id']);
        }

        if (!empty($filters['employment_type'])) {
            $query->where('employment_type', $filters['employment_type']);
        }

        if (!empty($filters['experience_level'])) {
            $query->where('experience_level', $filters['experience_level']);
        }

        if (!empty($filters['work_mode'])) {
            $query->where('work_mode', $filters['work_mode']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['location'])) {
            $query->where('location', 'like', '%' . $filters['location'] . '%');
        }

        if (!empty($filters['min_salary'])) {
            $query->where(function (Builder $builder) use ($filters): void {
                $builder->whereNull('salary_max')
                    ->orWhere('salary_max', '>=', $filters['min_salary']);
            });
        }

        if (!empty($filters['max_salary'])) {
            $query->where(function (Builder $builder) use ($filters): void {
                $builder->whereNull('salary_min')
                    ->orWhere('salary_min', '<=', $filters['max_salary']);
            });
        }
    }
}