<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class LoginController extends Controller
{
    /**
     * Authenticate user and generate API token
     *
     * @param LoginRequest $request
     * @param AuthService $authService
     * @return JsonResponse
     */
    public function __invoke(LoginRequest $request, AuthService $authService): JsonResponse
    {
        $result = $authService->login($request->validated());

        // If result is a JsonResponse, it's an error
        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse([
            'user' => $result['user'],
            'token' => $result['token'],
        ], 'Login successful');
    }
}