<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ResendVerificationEmailController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], Response::HTTP_UNAUTHORIZED);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email already verified',
            ], Response::HTTP_OK);
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Verification email sent',
        ], Response::HTTP_OK);
    }
}