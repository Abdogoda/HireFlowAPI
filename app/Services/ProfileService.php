<?php

namespace App\Services;

use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProfileService
{
    /**
     * Update user profile information
     *
     * @param User $user
     * @param array $data
     * @return UserResource|JsonResponse
     */
    public function updateProfile(User $user, array $data): UserResource|JsonResponse
    {
        try {
            $user->update($data);
            return new UserResource($user->load('role', 'skills', 'experiences', 'projects', 'resumes', 'socialProfiles'));
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to update profile',
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * Upload avatar for user
     *
     * @param User $user
     * @param UploadedFile $file
     * @return string|JsonResponse File path or error response
     */
    public function uploadAvatar(User $user, UploadedFile $file): string|JsonResponse
    {
        try {
            // Delete old avatar if exists
            if ($user->avatar && Storage::exists($user->avatar)) {
                Storage::delete($user->avatar);
            }

            $path = $file->store('avatars', 'public');
            $url = Storage::url($path);

            $user->update(['avatar' => $path]);

            return $url;
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to upload avatar',
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * Delete user avatar
     *
     * @param User $user
     * @return bool|JsonResponse
     */
    public function deleteAvatar(User $user): bool|JsonResponse
    {
        try {
            if ($user->avatar && Storage::exists($user->avatar)) {
                Storage::delete($user->avatar);
            }

            $user->update(['avatar' => null]);
            return true;
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to delete avatar',
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * Upload profile picture or thumbnail
     *
     * @param User $user
     * @param UploadedFile $file
     * @param string $type 'profile' or 'thumbnail'
     * @return string|JsonResponse File path or error response
     */
    public function uploadPicture(User $user, UploadedFile $file, string $type = 'profile'): string|JsonResponse
    {
        try {
            $column = $type === 'thumbnail' ? 'thumbnail' : 'profile_image';

            // Delete old picture if exists
            if ($user->$column && Storage::exists($user->$column)) {
                Storage::delete($user->$column);
            }

            $folder = $type === 'thumbnail' ? 'thumbnails' : 'profile-pictures';
            $path = $file->store($folder, 'public');
            $url = Storage::url($path);

            $user->update([$column => $path]);

            return $url;
        } catch (\Exception $e) {
            return ResponseService::error(
                "Failed to upload {$type} picture",
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * Delete profile picture or thumbnail
     *
     * @param User $user
     * @param string $type 'profile' or 'thumbnail'
     * @return bool|JsonResponse
     */
    public function deletePicture(User $user, string $type = 'profile'): bool|JsonResponse
    {
        try {
            $column = $type === 'thumbnail' ? 'thumbnail' : 'profile_image';

            if ($user->$column && Storage::exists($user->$column)) {
                Storage::delete($user->$column);
            }

            $user->update([$column => null]);
            return true;
        } catch (\Exception $e) {
            return ResponseService::error(
                "Failed to delete {$type} picture",
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * Get user profile with related data
     *
     * @param User $user
     * @return UserResource|JsonResponse
     */
    public function getProfile(User $user): UserResource|JsonResponse
    {
        try {
            return new UserResource($user->load([
                'role',
                'skills',
                'experiences',
                'projects',
                'resumes',
                'socialProfiles'
            ]));
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to retrieve profile',
                ['error' => $e->getMessage()],
                500
            );
        }
    }
}
