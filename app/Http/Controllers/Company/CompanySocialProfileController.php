<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\StoreCompanySocialProfileRequest;
use App\Http\Requests\Company\UpdateCompanySocialProfileRequest;
use App\Http\Resources\Company\CompanySocialProfileResource;
use App\Models\Company;
use App\Models\CompanySocialProfile;
use App\Services\CompanySocialProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompanySocialProfileController extends Controller
{
    /**
     * Get all social profiles for a company
     *
     * @param Request $request
     * @param Company $company
     * @return JsonResponse
     */
    public function index(Request $request, Company $company): JsonResponse
    {
        $socialProfiles = $company->socialProfiles()->get();

        return $this->successResponse(
            ['social_profiles' => CompanySocialProfileResource::collection($socialProfiles)],
            'Company social profiles retrieved successfully'
        );
    }

    /**
     * Create a new social profile for a company
     *
     * @param StoreCompanySocialProfileRequest $request
     * @param Company $company
     * @param CompanySocialProfileService $companySocialProfileService
     * @return JsonResponse
     */
    public function store(StoreCompanySocialProfileRequest $request, Company $company, CompanySocialProfileService $companySocialProfileService): JsonResponse
    {
        $this->authorize('update', $company);

        $result = $companySocialProfileService->createSocialProfile($company, $request->validated());

        return $this->createdResponse(
            ['social_profile' => $result],
            'Company social profile created successfully'
        );
    }

    /**
     * Get a specific company social profile
     *
     * @param Request $request
     * @param Company $company
     * @param CompanySocialProfile $socialProfile
     * @return JsonResponse
     */
    public function show(Request $request, Company $company, CompanySocialProfile $socialProfile): JsonResponse
    {
        if ($socialProfile->company_id !== $company->id) {
            return $this->forbiddenResponse('This social profile does not belong to this company');
        }

        return $this->successResponse(
            ['social_profile' => new CompanySocialProfileResource($socialProfile)],
            'Company social profile retrieved successfully'
        );
    }

    /**
     * Update a company social profile
     *
     * @param UpdateCompanySocialProfileRequest $request
     * @param Company $company
     * @param CompanySocialProfile $socialProfile
     * @param CompanySocialProfileService $companySocialProfileService
     * @return JsonResponse
     */
    public function update(UpdateCompanySocialProfileRequest $request, Company $company, CompanySocialProfile $socialProfile, CompanySocialProfileService $companySocialProfileService): JsonResponse
    {
        $this->authorize('update', $company);

        if ($socialProfile->company_id !== $company->id) {
            return $this->forbiddenResponse('This social profile does not belong to this company');
        }

        $result = $companySocialProfileService->updateSocialProfile($socialProfile, $request->validated());

        return $this->successResponse(
            ['social_profile' => $result],
            'Company social profile updated successfully'
        );
    }

    /**
     * Delete a company social profile
     *
     * @param Request $request
     * @param Company $company
     * @param CompanySocialProfile $socialProfile
     * @param CompanySocialProfileService $companySocialProfileService
     * @return JsonResponse
     */
    public function destroy(Request $request, Company $company, CompanySocialProfile $socialProfile, CompanySocialProfileService $companySocialProfileService): JsonResponse
    {
        $this->authorize('update', $company);

        if ($socialProfile->company_id !== $company->id) {
            return $this->forbiddenResponse('This social profile does not belong to this company');
        }

        $companySocialProfileService->deleteSocialProfile($socialProfile);

        return $this->successResponse(null, 'Company social profile deleted successfully');
    }
}
