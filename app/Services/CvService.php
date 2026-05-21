<?php

namespace App\Services;

use App\Models\User;
use App\Models\Resume;
use App\Http\Resources\ResumeResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CvService
{
    /**
     * Upload a new CV/Resume for user
     *
     * @param User $user
     * @param UploadedFile $file
     * @param string|null $title
     * @return ResumeResource|JsonResponse
     */
    public function uploadCv(User $user, UploadedFile $file, ?string $title = null): ResumeResource|JsonResponse
    {
        try {
            $path = $file->store('cvs', 'public');
            $url = Storage::url($path);

            $resume = $user->resumes()->create([
                'title' => $title ?? $file->getClientOriginalName(),
                'file_path' => $path,
                'file_url' => $url,
            ]);

            return new ResumeResource($resume);
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to upload CV',
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * Delete a CV/Resume
     *
     * @param Resume $resume
     * @return bool|JsonResponse
     */
    public function deleteCv(Resume $resume): bool|JsonResponse
    {
        try {
            if ($resume->file_path && Storage::exists($resume->file_path)) {
                Storage::delete($resume->file_path);
            }

            $resume->delete();
            return true;
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to delete CV',
                ['error' => $e->getMessage()],
                500
            );
        }
    }
}
