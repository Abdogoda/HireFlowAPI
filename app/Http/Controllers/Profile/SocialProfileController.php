<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\StoreSocialProfileRequest;
use App\Http\Requests\Profile\UpdateSocialProfileRequest;
use App\Http\Resources\Profile\SocialProfileResource;
use App\Models\SocialProfile;
use App\Services\SocialProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SocialProfileController extends Controller
{
    /**
     * Get all social profiles for authenticated user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $socialProfiles = $request->user()->socialProfiles()->get();

        return $this->successResponse(
            ['social_profiles' => SocialProfileResource::collection($socialProfiles)],
            'Social profiles retrieved successfully'
        );
    }

    /**
     * Create a new social profile
     *
     * @param StoreSocialProfileRequest $request
     * @param SocialProfileService $socialProfileService
     * @return JsonResponse
     */
    public function store(StoreSocialProfileRequest $request, SocialProfileService $socialProfileService): JsonResponse
    {
        $user = $request->user();
        $result = $socialProfileService->createSocialProfile($user, $request->validated());

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->createdResponse(
            ['social_profile' => $result],
            'Social profile created successfully'
        );
    }

    /**
     * Get a specific social profile
     *
     * @param Request $request
     * @param SocialProfile $socialProfile
     * @return JsonResponse
     */
    public function show(Request $request, SocialProfile $socialProfile): JsonResponse
    {
        if ($socialProfile->user_id !== $request->user()->id) {
            return $this->forbiddenResponse('You do not have permission to view this social profile');
        }

        return $this->successResponse(
            ['social_profile' => new SocialProfileResource($socialProfile)],
            'Social profile retrieved successfully'
        );
    }

    /**
     * Update a social profile
     *
     * @param UpdateSocialProfileRequest $request
     * @param SocialProfile $socialProfile
     * @param SocialProfileService $socialProfileService
     * @return JsonResponse
     */
    public function update(UpdateSocialProfileRequest $request, SocialProfile $socialProfile, SocialProfileService $socialProfileService): JsonResponse
    {
        if ($socialProfile->user_id !== $request->user()->id) {
            return $this->forbiddenResponse('You do not have permission to update this social profile');
        }

        $result = $socialProfileService->updateSocialProfile($socialProfile, $request->validated());

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            ['social_profile' => $result],
            'Social profile updated successfully'
        );
    }

    /**
     * Delete a social profile
     *
     * @param Request $request
     * @param SocialProfile $socialProfile
     * @param SocialProfileService $socialProfileService
     * @return JsonResponse
     */
    public function destroy(Request $request, SocialProfile $socialProfile, SocialProfileService $socialProfileService): JsonResponse
    {
        if ($socialProfile->user_id !== $request->user()->id) {
            return $this->forbiddenResponse('You do not have permission to delete this social profile');
        }

        $result = $socialProfileService->deleteSocialProfile($socialProfile);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            null,
            'Social profile deleted successfully'
        );
    }
}
