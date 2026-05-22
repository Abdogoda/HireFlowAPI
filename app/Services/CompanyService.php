<?php

namespace App\Services;

use App\Enums\Authorization\CompanyRoles;
use App\Http\Resources\CompanyPersonResource;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use App\Models\CompanyMembership;
use App\Models\User;
use App\Notifications\CompanyMemberAddedNotification;
use App\Notifications\CompanyMemberRemovedNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class CompanyService
{
    public function createCompany(User $user, array $data): CompanyResource|JsonResponse
    {
        try {
            $data['slug'] = $data['slug'] ?? $this->uniqueSlug($data['name']);
            $data['created_by'] = $user->id;
            $data['is_verified'] = $data['is_verified'] ?? false;

            $company = $user->ownedCompanies()->create($data);

            $company->memberships()->create([
                'user_id' => $user->id,
                'company_role' => CompanyRoles::OWNER->value,
                'position' => 'Company Owner',
                'information' => $data['information'] ?? null,
                'start_date' => $data['start_date'] ?? now()->toDateString(),
                'end_date' => $data['end_date'] ?? null,
                'is_current_position' => true,
            ]);

            $user->notify(new CompanyMemberAddedNotification(
                $company->load('owner'),
                $company->memberships()->latest('id')->firstOrFail(),
                $user
            ));

            return new CompanyResource($company->load(['owner', 'memberships.user']));
        } catch (\Exception $exception) {
            return ResponseService::error(
                'Failed to create company',
                ['error' => $exception->getMessage()],
                500
            );
        }
    }

    public function updateCompany(Company $company, array $data): CompanyResource|JsonResponse
    {
        try {
            $company->update($data);

            return new CompanyResource($company->load(['owner', 'memberships.user']));
        } catch (\Exception $exception) {
            return ResponseService::error(
                'Failed to update company',
                ['error' => $exception->getMessage()],
                500
            );
        }
    }

    public function deleteCompany(Company $company): bool|JsonResponse
    {
        try {
            $company->delete();

            return true;
        } catch (\Exception $exception) {
            return ResponseService::error(
                'Failed to delete company',
                ['error' => $exception->getMessage()],
                500
            );
        }
    }

    public function addPerson(Company $company, array $data): CompanyPersonResource|JsonResponse
    {
        try {
            CompanyMembership::query()
                ->where('company_id', $company->id)
                ->where('user_id', $data['user_id'])
                ->where('is_current_position', true)
                ->update([
                    'is_current_position' => false,
                    'end_date' => $data['start_date'] ?? now()->toDateString(),
                ]);

            $membership = $company->memberships()->create([
                'user_id' => $data['user_id'],
                'company_role' => $data['company_role'],
                'position' => $data['position'],
                'information' => $data['information'] ?? null,
                'start_date' => $data['start_date'] ?? now()->toDateString(),
                'end_date' => $data['end_date'] ?? null,
                'is_current_position' => $data['is_current_position'] ?? true,
            ])->load('user');

            $membership->user?->notify(new CompanyMemberAddedNotification(
                $company->load('owner'),
                $membership,
                auth()->user()
            ));

            return new CompanyPersonResource($membership);
        } catch (\Exception $exception) {
            return ResponseService::error(
                'Failed to add person to company',
                ['error' => $exception->getMessage()],
                500
            );
        }
    }

    public function updatePerson(Company $company, CompanyMembership $membership, array $data): CompanyPersonResource|JsonResponse
    {
        try {
            if ($membership->company_id !== $company->id) {
                return ResponseService::notFound('Person not found in this company');
            }

            if (array_key_exists('is_current_position', $data) && $data['is_current_position'] === true) {
                CompanyMembership::query()
                    ->where('company_id', $company->id)
                    ->where('user_id', $membership->user_id)
                    ->where('id', '!=', $membership->id)
                    ->where('is_current_position', true)
                    ->update([
                        'is_current_position' => false,
                        'end_date' => $data['start_date'] ?? now()->toDateString(),
                    ]);
            }

            $membership->update(array_filter([
                'company_role' => $data['company_role'] ?? null,
                'position' => $data['position'] ?? null,
                'information' => $data['information'] ?? null,
                'start_date' => $data['start_date'] ?? null,
                'end_date' => $data['end_date'] ?? null,
                'is_current_position' => $data['is_current_position'] ?? null,
            ], static fn ($value) => $value !== null));

            return new CompanyPersonResource($membership->fresh()->load('user'));
        } catch (\Exception $exception) {
            return ResponseService::error(
                'Failed to update person in company',
                ['error' => $exception->getMessage()],
                500
            );
        }
    }

    public function removePerson(Company $company, CompanyMembership $membership): bool|JsonResponse
    {
        try {
            if ($membership->company_id !== $company->id) {
                return ResponseService::notFound('Person not found in this company');
            }

            $membership->update([
                'is_current_position' => false,
                'end_date' => $membership->end_date ?? Carbon::today()->toDateString(),
            ]);

            $membership->user?->notify(new CompanyMemberRemovedNotification(
                $company->load('owner'),
                $membership,
                auth()->user()
            ));

            return true;
        } catch (\Exception $exception) {
            return ResponseService::error(
                'Failed to remove person from company',
                ['error' => $exception->getMessage()],
                500
            );
        }
    }

    private function uniqueSlug(string $name): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $index = 1;

        while (Company::query()->where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $index;
            $index++;
        }

        return $slug;
    }
}