<?php

use App\Http\Controllers\Vacancy\VacancyController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/', [VacancyController::class, 'index'])->name('vacancies.index');
    Route::post('/', [VacancyController::class, 'store'])->name('vacancies.store');
    Route::get('{vacancy}', [VacancyController::class, 'show'])->name('vacancies.show');
    Route::patch('{vacancy}', [VacancyController::class, 'update'])->name('vacancies.update');
    Route::delete('{vacancy}', [VacancyController::class, 'destroy'])->name('vacancies.destroy');
});