<?php

use App\Http\Controllers\SwaggerController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('api/documentation', [SwaggerController::class, 'index'])->name('swagger.docs');
Route::get('api/documentation.json', [SwaggerController::class, 'json'])->name('swagger.json');
