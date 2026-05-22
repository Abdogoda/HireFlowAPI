<?php

use App\Http\Controllers\Company\CompanyController;
use App\Http\Controllers\Company\CompanyMemberController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/', [CompanyController::class, 'index'])->name('companies.index');
    Route::post('/', [CompanyController::class, 'store'])->name('companies.store');
    Route::get('{company}', [CompanyController::class, 'show'])->name('companies.show');
    Route::patch('{company}', [CompanyController::class, 'update'])->name('companies.update');
    Route::delete('{company}', [CompanyController::class, 'destroy'])->name('companies.destroy');

    Route::post('{company}/people', [CompanyMemberController::class, 'store'])->name('companies.people.store');
    Route::patch('{company}/people/{membership}', [CompanyMemberController::class, 'update'])->name('companies.people.update');
    Route::delete('{company}/people/{membership}', [CompanyMemberController::class, 'destroy'])->name('companies.people.destroy');
});