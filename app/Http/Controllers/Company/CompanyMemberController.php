<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\StoreCompanyPersonRequest;
use App\Http\Requests\Company\UpdateCompanyPersonRequest;
use App\Models\Company;
use App\Models\CompanyMembership;
use App\Services\CompanyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompanyMemberController extends Controller
{
    public function store(StoreCompanyPersonRequest $request, Company $company, CompanyService $companyService): JsonResponse
    {
        $this->authorize('managePeople', $company);

        $result = $companyService->addPerson($company, $request->validated());

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->createdResponse(
            ['person' => $result],
            'Person added to company successfully'
        );
    }

    public function update(UpdateCompanyPersonRequest $request, Company $company, CompanyMembership $membership, CompanyService $companyService): JsonResponse
    {
        $this->authorize('managePeople', $company);

        $result = $companyService->updatePerson($company, $membership, $request->validated());

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            ['person' => $result],
            'Person updated successfully'
        );
    }

    public function destroy(Request $request, Company $company, CompanyMembership $membership, CompanyService $companyService): JsonResponse
    {
        $this->authorize('managePeople', $company);

        $result = $companyService->removePerson($company, $membership);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(null, 'Person removed from company successfully');
    }
}