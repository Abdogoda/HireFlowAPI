<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class ForgotPasswordController extends Controller
{
    /**
     * Send password reset link to user email
     *
     * @param ForgotPasswordRequest $request
     * @param AuthService $authService
     * @return JsonResponse
     */
    public function __invoke(ForgotPasswordRequest $request, AuthService $authService): JsonResponse
    {
        $result = $authService->sendPasswordResetLink($request->validated());

        // If result is a JsonResponse, it's an error
        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(null, 'Password reset link sent to your email');
    }
}