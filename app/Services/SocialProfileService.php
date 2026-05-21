<?php

namespace App\Services;

use App\Models\User;
use App\Models\SocialProfile;
use Illuminate\Http\JsonResponse;

class SocialProfileService
{
    /**
     * Create a new social profile for user
     *
     * @param User $user
     * @param array $data
     * @return SocialProfile|JsonResponse
     */
    public function createSocialProfile(User $user, array $data): SocialProfile|JsonResponse
    {
        try {
            $socialProfile = $user->socialProfiles()->create($data);
            return $socialProfile;
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
     * @return SocialProfile|JsonResponse
     */
    public function updateSocialProfile(SocialProfile $socialProfile, array $data): SocialProfile|JsonResponse
    {
        try {
            $socialProfile->update($data);
            return $socialProfile;
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
