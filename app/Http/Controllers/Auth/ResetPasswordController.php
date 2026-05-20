<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    public function __invoke(ResetPasswordRequest $request): JsonResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => $password,
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'Password reset successfully',
            ], Response::HTTP_OK);
        }

        return response()->json([
            'message' => 'Unable to reset password',
            'error' => __($status),
        ], Response::HTTP_BAD_REQUEST);
    }
}