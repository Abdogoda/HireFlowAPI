<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class RefreshTokenController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Revoke current token
        $user->currentAccessToken()->delete();

        // Generate new token
        $newToken = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Token refreshed successfully',
            'token' => $newToken,
        ], Response::HTTP_OK);
    }
}