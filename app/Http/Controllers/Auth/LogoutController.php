<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class LogoutController extends Controller
{
    /**
     * Logout user by revoking current token
     *
     * @param AuthService $authService
     * @return JsonResponse
     */
    public function __invoke(AuthService $authService): JsonResponse
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return $this->unauthorizedResponse('Unauthenticated');
        }

        $result = $authService->logout($user);

        // If result is a JsonResponse, it's an error
        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(null, 'Logout successful');
    }
}