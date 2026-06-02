<?php

use App\Http\Controllers\Company\CompanyController;
use App\Http\Controllers\Company\CompanyMemberController;
use App\Http\Controllers\Company\CompanySocialProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/', [CompanyController::class, 'index'])->name('companies.index');
    Route::get('/my', [CompanyController::class, 'myCompanies'])->name('companies.my');
    Route::post('/', [CompanyController::class, 'store'])->name('companies.store');
    Route::get('{company}', [CompanyController::class, 'show'])->name('companies.show');
    Route::patch('{company}', [CompanyController::class, 'update'])->name('companies.update');
    Route::delete('{company}', [CompanyController::class, 'destroy'])->name('companies.destroy');

    // Company people management
    Route::get('{company}/people', [CompanyMemberController::class, 'index'])->name('companies.people.index');
    Route::post('{company}/people', [CompanyMemberController::class, 'store'])->name('companies.people.store');
    Route::patch('{company}/people/{membership}', [CompanyMemberController::class, 'update'])->name('companies.people.update');
    Route::delete('{company}/people/{membership}', [CompanyMemberController::class, 'destroy'])->name('companies.people.destroy');

    // Company social profiles management
    Route::get('{company}/social-profiles', [CompanySocialProfileController::class, 'index'])->name('companies.social-profiles.index');
    Route::post('{company}/social-profiles', [CompanySocialProfileController::class, 'store'])->name('companies.social-profiles.store');
    Route::get('{company}/social-profiles/{socialProfile}', [CompanySocialProfileController::class, 'show'])->name('companies.social-profiles.show');
    Route::patch('{company}/social-profiles/{socialProfile}', [CompanySocialProfileController::class, 'update'])->name('companies.social-profiles.update');
    Route::delete('{company}/social-profiles/{socialProfile}', [CompanySocialProfileController::class, 'destroy'])->name('companies.social-profiles.destroy');
});