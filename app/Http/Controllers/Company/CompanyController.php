<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\IndexCompanyRequest;
use App\Http\Requests\Company\StoreCompanyRequest;
use App\Http\Requests\Company\UpdateCompanyRequest;
use App\Http\Resources\Company\CompanyResource;
use App\Models\Company;
use App\Services\CompanyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index(IndexCompanyRequest $request, CompanyService $companyService): JsonResponse
    {
        $companies = $companyService->getCompanies($request->validated());

        return $this->successResponse(
            ['companies' => CompanyResource::collection($companies)],
            'Companies retrieved successfully'
        );
    }

    public function myCompanies(IndexCompanyRequest $request, CompanyService $companyService): JsonResponse
    {
        $companies = $companyService->getUserCompanies($request->user(), $request->validated());

        return $this->successResponse(
            ['companies' => CompanyResource::collection($companies)],
            'User companies retrieved successfully'
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

}