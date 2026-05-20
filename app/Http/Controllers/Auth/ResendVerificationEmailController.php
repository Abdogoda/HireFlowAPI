<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class ResendVerificationEmailController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return $this->unauthorizedResponse('Unauthenticated');
        }

        if ($user->hasVerifiedEmail()) {
            return $this->successResponse(null, 'Email already verified');
        }

        $user->sendEmailVerificationNotification();

        return $this->successResponse(null, 'Verification email sent');
    }
}