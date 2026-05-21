<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\StoreAvatarRequest;
use App\Services\ProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AvatarController extends Controller
{
    /**
     * Upload avatar
     *
     * @param StoreAvatarRequest $request
     * @param ProfileService $profileService
     * @return JsonResponse
     */
    public function store(StoreAvatarRequest $request, ProfileService $profileService): JsonResponse
    {
        $user = $request->user();
        $result = $profileService->uploadAvatar($user, $request->file('avatar'));

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->createdResponse(
            ['url' => $result],
            'Avatar uploaded successfully'
        );
    }

    /**
     * Delete avatar
     *
     * @param Request $request
     * @param ProfileService $profileService
     * @return JsonResponse
     */
    public function destroy(Request $request, ProfileService $profileService): JsonResponse
    {
        $user = $request->user();
        $result = $profileService->deleteAvatar($user);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            null,
            'Avatar deleted successfully'
        );
    }
}
