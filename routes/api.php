<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Authentication routes
Route::prefix('auth')->group(base_path('routes/api/auth.php'));

// Profile routes
Route::prefix('profile')->group(base_path('routes/api/profile.php'));

// Protected user route
Route::get('/user', function (Request $request) {
    return $request->user()->load('role');
})->middleware('auth:sanctum');
