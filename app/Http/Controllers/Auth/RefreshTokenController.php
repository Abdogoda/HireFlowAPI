<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class RefreshTokenController extends Controller
{
    /**
     * Refresh user's API token
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

        $result = $authService->refreshToken($user);

        // If result is a JsonResponse, it's an error
        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(
            ['token' => $result],
            'Token refreshed successfully'
        );
    }
}