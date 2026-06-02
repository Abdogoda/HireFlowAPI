<?php

use App\Http\Controllers\Post\PostController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/', [PostController::class, 'index'])->name('posts.index');
    Route::post('/', [PostController::class, 'store'])->name('posts.store');
    Route::get('{post}', [PostController::class, 'show'])->name('posts.show');
    Route::patch('{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('{post}', [PostController::class, 'destroy'])->name('posts.destroy');
});