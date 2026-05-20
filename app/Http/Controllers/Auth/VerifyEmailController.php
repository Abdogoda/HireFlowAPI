<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    /**
     * Verify user email using hash
     *
     * @param Request $request
     * @param AuthService $authService
     * @return JsonResponse
     */
    public function __invoke(Request $request, AuthService $authService): JsonResponse
    {
        $userId = $request->route('id');
        $hash = $request->route('hash');

        $result = $authService->verifyEmail($userId, $hash);

        // If result is a JsonResponse, it's an error
        if ($result instanceof JsonResponse) {
            return $result;
        }

        $message = $result['verified']
            ? 'Email verified successfully'
            : 'Email already verified';

        return $this->successResponse(null, $message);
    }
}