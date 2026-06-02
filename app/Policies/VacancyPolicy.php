<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;
use App\Models\Vacancy;

class VacancyPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Vacancy $vacancy): bool
    {
        return true;
    }

    public function create(User $user, Company $company): bool
    {
        // Company owner can create vacancies for their company.
        // Members with the `recruiter` role now can create vacancies for their company.
        
        return $company->created_by === $user->id
            || $company->memberships()
                ->where('member_id', $user->id)
                ->where('company_role', 'recruiter')
                ->where('is_current_position', true)
                ->exists();
    }

    public function update(User $user, Vacancy $vacancy): bool
    {
        // Vacancy creator can update.
        // Company owner can update any vacancy of their company.
        // Members with the `recruiter` role now can update vacancies of their company.

        return $vacancy->created_by === $user->id
            || $vacancy->company->created_by === $user->id
            || $vacancy->company->memberships()
                ->where('member_id', $user->id)
                ->where('company_role', 'recruiter')
                ->where('is_current_position', true)
                ->exists();
    }

    public function delete(User $user, Vacancy $vacancy): bool
    {
        return $this->update($user, $vacancy);
    }
}