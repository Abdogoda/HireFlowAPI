<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class LogoutController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout successful',
        ], Response::HTTP_OK);
    }
}