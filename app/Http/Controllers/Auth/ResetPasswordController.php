<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class ResetPasswordController extends Controller
{
    /**
     * Reset user password
     *
     * @param ResetPasswordRequest $request
     * @param AuthService $authService
     * @return JsonResponse
     */
    public function __invoke(ResetPasswordRequest $request, AuthService $authService): JsonResponse
    {
        $result = $authService->resetPassword($request->validated());

        // If result is a JsonResponse, it's an error
        if ($result instanceof JsonResponse) {
            return $result;
        }

        return $this->successResponse(null, 'Password reset successfully');
    }
}