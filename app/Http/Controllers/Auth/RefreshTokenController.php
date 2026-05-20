<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class RefreshTokenController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return $this->unauthorizedResponse('Unauthenticated');
        }

        // Revoke current token
        $user->currentAccessToken()->delete();

        // Generate new token
        $newToken = $user->createToken('api-token')->plainTextToken;

        return $this->successResponse(
            ['token' => $newToken],
            'Token refreshed successfully'
        );
    }
}