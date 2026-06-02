<?php

namespace App\Policies;

use App\Enums\Company\CompanyRoles;
use App\Models\Company;
use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    public function view(User $user, Post $post): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function createForCompany(User $user, Company $company): bool
    {
        return $company->created_by === $user->id
            || $this->hasCompanyPostingAccess($user, $company);
    }

    public function update(User $user, Post $post): bool
    {
        if ($post->user_id === $user->id) {
            return true;
        }

        if ($post->company_id === null) {
            return false;
        }

        return $this->hasCompanyPostingAccess($user, $post->company);
    }

    public function delete(User $user, Post $post): bool
    {
        return $this->update($user, $post);
    }

    private function hasCompanyPostingAccess(User $user, ?Company $company): bool
    {
        if ($company === null) {
            return false;
        }

        return $company->memberships()
            ->where('member_id', $user->id)
            ->where('is_current_position', true)
            ->whereIn('company_role', [
                CompanyRoles::OWNER->value,
                CompanyRoles::ADMIN->value,
                CompanyRoles::RECRUITER->value,
            ])
            ->exists();
    }
}