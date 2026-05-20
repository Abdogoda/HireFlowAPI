<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function __invoke(ForgotPasswordRequest $request): JsonResponse
    {
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => 'Password reset link sent to your email',
            ], Response::HTTP_OK);
        }

        return response()->json([
            'message' => 'Unable to send password reset link',
            'error' => __($status),
        ], Response::HTTP_BAD_REQUEST);
    }
}