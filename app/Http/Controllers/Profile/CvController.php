<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\StoreCvRequest;
use App\Http\Resources\ResumeResource;
use App\Models\Resume;
use App\Services\CvService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CvController extends Controller
{
    /**
     * Get all CVs/Resumes for authenticated user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request, CvService $cvService): JsonResponse
    {
        $cvs = $cvService->getUserCvs($request->user());

        return $this->successResponse(
            ['cvs' => ResumeResource::collection($cvs)],
            'CVs retrieved successfully'
        );
    }

    /**
     * Upload a new CV/Resume
     *
     * @param StoreCvRequest $request
     * @param CvService $cvService
     * @return JsonResponse
     */
    public function store(StoreCvRequest $request, CvService $cvService): JsonResponse
    {
        $user = $request->user();
        $result = $cvService->uploadCv($user, $request->file('file'), $request->input('title'));

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->createdResponse(
            ['cv' => $result],
            'CV uploaded successfully'
        );
    }

    /**
     * Get a specific CV/Resume
     *
     * @param Request $request
     * @param Resume $cv
     * @return JsonResponse
     */
    public function show(Request $request, Resume $cv): JsonResponse
    {
        if ($cv->user_id !== $request->user()->id) {
            return $this->forbiddenResponse('You do not have permission to view this CV');
        }

        return $this->successResponse(
            ['cv' => new ResumeResource($cv)],
            'CV retrieved successfully'
        );
    }

    /**
     * Delete a CV/Resume
     *
     * @param Request $request
     * @param Resume $cv
     * @param CvService $cvService
     * @return JsonResponse
     */
    public function destroy(Request $request, Resume $cv, CvService $cvService): JsonResponse
    {
        if ($cv->user_id !== $request->user()->id) {
            return $this->forbiddenResponse('You do not have permission to delete this CV');
        }

        $result = $cvService->deleteCv($cv);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            null,
            'CV deleted successfully'
        );
    }
}
