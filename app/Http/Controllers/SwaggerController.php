<?php

namespace App\Http\Controllers;

use App\Support\OpenApiSpec;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class SwaggerController extends Controller
{
    public function index(): View
    {
        return view('swagger.index', [
            'specUrl' => route('swagger.json'),
        ]);
    }

    public function json(): JsonResponse
    {
        return response()->json(OpenApiSpec::make());
    }
}