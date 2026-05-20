<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class LogoutController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return $this->unauthorizedResponse('Unauthenticated');
        }

        $user->currentAccessToken()->delete();

        return $this->successResponse(null, 'Logout successful');
    }
}