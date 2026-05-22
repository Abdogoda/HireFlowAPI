<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Requests\Profile\StorePictureRequest;
use App\Http\Resources\UserResource;
use App\Services\ProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Get authenticated user profile
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request, ProfileService $profileService): JsonResponse
    {
        $result = $profileService->getProfile($request->user());

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            ['profile' => $result],
            'Profile retrieved successfully'
        );
    }

    /**
     * Update user profile
     *
     * @param UpdateProfileRequest $request
     * @param ProfileService $profileService
     * @return JsonResponse
     */
    public function update(UpdateProfileRequest $request, ProfileService $profileService): JsonResponse
    {
        $user = $request->user();
        $result = $profileService->updateProfile($user, $request->validated());

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            ['profile' => $result],
            'Profile updated successfully'
        );
    }

    /**
     * Upload profile picture/thumbnail
     *
     * @param StorePictureRequest $request
     * @param ProfileService $profileService
     * @return JsonResponse
     */
    public function storePicture(StorePictureRequest $request, ProfileService $profileService): JsonResponse
    {
        $user = $request->user();
        $result = $profileService->uploadPicture($user, $request->file('picture'), $request->input('type', 'profile'));

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->createdResponse(
            ['url' => $result],
            'Picture uploaded successfully'
        );
    }

    /**
     * Delete profile picture/thumbnail
     *
     * @param Request $request
     * @param ProfileService $profileService
     * @return JsonResponse
     */
    public function destroyPicture(Request $request, ProfileService $profileService): JsonResponse
    {
        $user = $request->user();
        $type = $request->input('type', 'profile');
        $result = $profileService->deletePicture($user, $type);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            null,
            'Picture deleted successfully'
        );
    }
}
