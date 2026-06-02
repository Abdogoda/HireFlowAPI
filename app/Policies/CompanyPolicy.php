<?php

namespace App\Policies;

use App\Enums\Authorization\CompanyRoles;
use App\Models\Company;
use App\Models\User;

class CompanyPolicy
{
    public function view(User $user, Company $company): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Company $company): bool
    {
        return $company->created_by === $user->id
            || $company->memberships()
                ->where('member_id', $user->id)
                ->where('company_role', CompanyRoles::ADMIN->value)
                ->where('is_current_position', true)
                ->exists();
    }

    public function delete(User $user, Company $company): bool
    {
        return $this->update($user, $company);
    }

    public function managePeople(User $user, Company $company): bool
    {
        return $this->update($user, $company);
    }
}