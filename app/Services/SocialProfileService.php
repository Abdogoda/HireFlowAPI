<?php

namespace App\Services;

use App\Models\User;
use App\Models\SocialProfile;
use App\Http\Resources\Profile\SocialProfileResource;
use Illuminate\Http\JsonResponse;

class SocialProfileService
{
    /**
     * Create a new social profile for user
     *
     * @param User $user
     * @param array $data
     * @return SocialProfileResource|JsonResponse
     */
    public function createSocialProfile(User $user, array $data): SocialProfileResource
    {
        $socialProfile = $user->socialProfiles()->create($data);
        return new SocialProfileResource($socialProfile);
    }

    /**
     * Update a social profile
     *
     * @param SocialProfile $socialProfile
     * @param array $data
     * @return SocialProfileResource|JsonResponse
     */
    public function updateSocialProfile(SocialProfile $socialProfile, array $data): SocialProfileResource
    {
        $socialProfile->update($data);
        return new SocialProfileResource($socialProfile);
    }

    /**
     * Delete a social profile
     *
     * @param SocialProfile $socialProfile
     * @return bool|JsonResponse
     */
    public function deleteSocialProfile(SocialProfile $socialProfile): bool
    {
        $socialProfile->delete();
        return true;
    }
}
