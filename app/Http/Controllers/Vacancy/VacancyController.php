<?php

namespace App\Http\Controllers\Vacancy;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vacancy\IndexVacancyRequest;
use App\Http\Requests\Vacancy\StoreVacancyRequest;
use App\Http\Requests\Vacancy\UpdateVacancyRequest;
use App\Http\Resources\Vacancy\VacancyResource;
use App\Models\Company;
use App\Models\Vacancy;
use App\Services\VacancyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VacancyController extends Controller
{
    public function index(IndexVacancyRequest $request, VacancyService $vacancyService): JsonResponse
    {
        $vacancies = $vacancyService->getVacancies($request->validated());

        return $this->successResponse([
            'vacancies' => VacancyResource::collection($vacancies->getCollection()),
            'pagination' => [
                'current_page' => $vacancies->currentPage(),
                'total' => $vacancies->total(),
                'per_page' => $vacancies->perPage(),
                'last_page' => $vacancies->lastPage(),
                'from' => $vacancies->firstItem(),
                'to' => $vacancies->lastItem(),
            ],
        ], 'Vacancies retrieved successfully');
    }

    public function show(Request $request, Vacancy $vacancy, VacancyService $vacancyService): JsonResponse
    {
        $this->authorize('view', $vacancy);

        return $this->successResponse(
            ['vacancy' => $vacancyService->getVacancy($vacancy)],
            'Vacancy retrieved successfully'
        );
    }

    public function store(StoreVacancyRequest $request, VacancyService $vacancyService): JsonResponse
    {
        $company = Company::query()->findOrFail($request->validated()['company_id']);

        $this->authorize('create', [Vacancy::class, $company]);

        $result = $vacancyService->createVacancy($request->user(), $company, $request->validated());

        return $this->createdResponse(
            ['vacancy' => $result],
            'Vacancy created successfully'
        );
    }

    public function update(UpdateVacancyRequest $request, Vacancy $vacancy, VacancyService $vacancyService): JsonResponse
    {
        $this->authorize('update', $vacancy);

        $result = $vacancyService->updateVacancy($vacancy, $request->validated());

        return $this->successResponse(
            ['vacancy' => $result],
            'Vacancy updated successfully'
        );
    }

    public function destroy(Request $request, Vacancy $vacancy, VacancyService $vacancyService): JsonResponse
    {
        $this->authorize('delete', $vacancy);

        $vacancyService->deleteVacancy($vacancy);

        return $this->successResponse(null, 'Vacancy deleted successfully');
    }
}