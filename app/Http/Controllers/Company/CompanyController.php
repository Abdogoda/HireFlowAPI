<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\StoreCompanyPersonRequest;
use App\Http\Requests\Company\StoreCompanyRequest;
use App\Http\Requests\Company\UpdateCompanyPersonRequest;
use App\Http\Requests\Company\UpdateCompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use App\Models\CompanyMembership;
use App\Services\CompanyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $ownedCompanies = $request->user()
            ->ownedCompanies()
            ->with(['owner', 'memberships.user'])
            ->get();

        $memberCompanies = Company::query()
            ->whereHas('memberships', fn ($query) => $query->where('user_id', $request->user()->id))
            ->with(['owner', 'memberships.user'])
            ->get();

        $companies = $ownedCompanies
            ->merge($memberCompanies)
            ->unique('id')
            ->sortByDesc('created_at')
            ->values();

        return $this->successResponse(
            ['companies' => CompanyResource::collection($companies)],
            'Companies retrieved successfully'
        );
    }

    public function store(StoreCompanyRequest $request, CompanyService $companyService): JsonResponse
    {
        $this->authorize('create', Company::class);

        $result = $companyService->createCompany($request->user(), $request->validated());

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->createdResponse(
            ['company' => $result],
            'Company created successfully'
        );
    }

    public function show(Request $request, Company $company): JsonResponse
    {
        $this->authorize('view', $company);

        return $this->successResponse(
            ['company' => new CompanyResource($company->load(['owner', 'memberships.user']))],
            'Company retrieved successfully'
        );
    }

    public function update(UpdateCompanyRequest $request, Company $company, CompanyService $companyService): JsonResponse
    {
        $this->authorize('update', $company);

        $result = $companyService->updateCompany($company, $request->validated());

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            ['company' => $result],
            'Company updated successfully'
        );
    }

    public function destroy(Request $request, Company $company, CompanyService $companyService): JsonResponse
    {
        $this->authorize('delete', $company);

        $result = $companyService->deleteCompany($company);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(null, 'Company deleted successfully');
    }

    public function storePerson(StoreCompanyPersonRequest $request, Company $company, CompanyService $companyService): JsonResponse
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

    public function updatePerson(UpdateCompanyPersonRequest $request, Company $company, CompanyMembership $membership, CompanyService $companyService): JsonResponse
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

    public function destroyPerson(Request $request, Company $company, CompanyMembership $membership, CompanyService $companyService): JsonResponse
    {
        $this->authorize('managePeople', $company);

        $result = $companyService->removePerson($company, $membership);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(null, 'Person removed from company successfully');
    }

}