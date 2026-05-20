<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class ResendVerificationEmailController extends Controller
{
    /**
     * Resend verification email to authenticated user
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

        // Check if already verified
        if ($user->hasVerifiedEmail()) {
            return $this->successResponse(null, 'Email already verified');
        }

        $result = $authService->sendEmailVerification($user);

        // If result is a JsonResponse, it's an error
        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(null, 'Verification email sent');
    }
}