<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RefreshTokenController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResendVerificationEmailController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public authentication routes
Route::prefix('auth')->group(function () {
    Route::post('register', RegisterController::class)->name('register');
    Route::post('login', LoginController::class)->name('login');
    Route::post('forgot-password', ForgotPasswordController::class)->name('forgot-password');
    Route::post('reset-password', ResetPasswordController::class)->name('reset-password');
});

// Email verification routes (public but requires email verification token)
Route::prefix('auth')->group(function () {
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verify-email');
});

// Protected routes (requires authentication)
Route::middleware('auth:sanctum')->prefix('auth')->group(function () {
    Route::post('logout', LogoutController::class)->name('logout');
    Route::post('refresh-token', RefreshTokenController::class)->name('refresh-token');
    Route::post('resend-verification-email', ResendVerificationEmailController::class)->name('resend-verification-email');
});

// Protected user route
Route::get('/user', function (Request $request) {
    return $request->user()->load('role');
})->middleware('auth:sanctum');
