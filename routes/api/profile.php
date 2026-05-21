<?php

use App\Http\Controllers\Profile\AvatarController;
use App\Http\Controllers\Profile\CvController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Profile\ProjectController;
use App\Http\Controllers\Profile\SkillController;
use App\Http\Controllers\Profile\SocialProfileController;
use Illuminate\Support\Facades\Route;

// All profile routes are protected - require authentication
Route::middleware('auth:sanctum')->group(function () {
    // Profile basic information
    Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/', [ProfileController::class, 'show'])->name('profile.show');

    // Avatar management
    Route::post('avatar', [AvatarController::class, 'store'])->name('avatar.store');
    Route::delete('avatar', [AvatarController::class, 'destroy'])->name('avatar.destroy');

    // Profile picture/thumbnail management
    Route::post('picture', [ProfileController::class, 'storePicture'])->name('profile.picture.store');
    Route::delete('picture', [ProfileController::class, 'destroyPicture'])->name('profile.picture.destroy');

    // CV management
    Route::get('cvs', [CvController::class, 'index'])->name('cvs.index');
    Route::post('cvs', [CvController::class, 'store'])->name('cvs.store');
    Route::get('cvs/{cv}', [CvController::class, 'show'])->name('cvs.show');
    Route::delete('cvs/{cv}', [CvController::class, 'destroy'])->name('cvs.destroy');

    // Projects management
    Route::get('projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::post('projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::patch('projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

    // Skills management
    Route::get('skills', [SkillController::class, 'index'])->name('skills.index');
    Route::post('skills', [SkillController::class, 'store'])->name('skills.store');
    Route::get('skills/{skill}', [SkillController::class, 'show'])->name('skills.show');
    Route::patch('skills/{skill}', [SkillController::class, 'update'])->name('skills.update');
    Route::delete('skills/{skill}', [SkillController::class, 'destroy'])->name('skills.destroy');

    // Social profiles management
    Route::get('social-profiles', [SocialProfileController::class, 'index'])->name('social-profiles.index');
    Route::post('social-profiles', [SocialProfileController::class, 'store'])->name('social-profiles.store');
    Route::get('social-profiles/{socialProfile}', [SocialProfileController::class, 'show'])->name('social-profiles.show');
    Route::patch('social-profiles/{socialProfile}', [SocialProfileController::class, 'update'])->name('social-profiles.update');
    Route::delete('social-profiles/{socialProfile}', [SocialProfileController::class, 'destroy'])->name('social-profiles.destroy');
});
