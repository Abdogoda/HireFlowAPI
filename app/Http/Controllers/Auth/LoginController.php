<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function __invoke(LoginRequest $request): JsonResponse
    {
        if (!Auth::attempt($request->validated())) {
            return $this->unauthorizedResponse('Invalid credentials');
        }

        $user = Auth::user();

        // Check if email is verified
        if (!$user->email_verified_at) {
            return $this->forbiddenResponse('Please verify your email before logging in');
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return $this->successResponse([
            'user' => $user->load('role'),
            'token' => $token,
        ], 'Login successful');
    }
}