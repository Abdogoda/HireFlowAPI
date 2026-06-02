<?php

namespace App\Services;

use App\Models\Company;
use App\Models\CompanySocialProfile;
use App\Http\Resources\Company\CompanySocialProfileResource;

class CompanySocialProfileService
{
    /**
     * Create a new social profile for a company
     *
     * @param Company $company
     * @param array $data
     * @return CompanySocialProfileResource
     */
    public function createSocialProfile(Company $company, array $data): CompanySocialProfileResource
    {
        $socialProfile = $company->socialProfiles()->create($data);
        return new CompanySocialProfileResource($socialProfile);
    }

    /**
     * Update a company social profile
     *
     * @param CompanySocialProfile $socialProfile
     * @param array $data
     * @return CompanySocialProfileResource
     */
    public function updateSocialProfile(CompanySocialProfile $socialProfile, array $data): CompanySocialProfileResource
    {
        $socialProfile->update($data);
        return new CompanySocialProfileResource($socialProfile);
    }

    /**
     * Delete a company social profile
     *
     * @param CompanySocialProfile $socialProfile
     * @return bool
     */
    public function deleteSocialProfile(CompanySocialProfile $socialProfile): bool
    {
        $socialProfile->delete();
        return true;
    }
}
