<?php

use Illuminate\Support\Facades\Route;

// Authentication routes
Route::prefix('auth')->group(base_path('routes/api/auth.php'));

// Profile routes
Route::prefix('profile')->group(base_path('routes/api/profile.php'));

// Company routes
Route::prefix('companies')->group(base_path('routes/api/company.php'));