<?php

namespace App\Services;

use App\Models\User;
use App\Models\SocialProfile;
use App\Http\Resources\SocialProfileResource;
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
    public function createSocialProfile(User $user, array $data): SocialProfileResource|JsonResponse
    {
        try {
            $socialProfile = $user->socialProfiles()->create($data);
            return new SocialProfileResource($socialProfile);
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to create social profile',
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * Update a social profile
     *
     * @param SocialProfile $socialProfile
     * @param array $data
     * @return SocialProfileResource|JsonResponse
     */
    public function updateSocialProfile(SocialProfile $socialProfile, array $data): SocialProfileResource|JsonResponse
    {
        try {
            $socialProfile->update($data);
            return new SocialProfileResource($socialProfile);
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to update social profile',
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * Delete a social profile
     *
     * @param SocialProfile $socialProfile
     * @return bool|JsonResponse
     */
    public function deleteSocialProfile(SocialProfile $socialProfile): bool|JsonResponse
    {
        try {
            $socialProfile->delete();
            return true;
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to delete social profile',
                ['error' => $e->getMessage()],
                500
            );
        }
    }
}
