<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function __invoke(LoginRequest $request): JsonResponse
    {
        if (!Auth::attempt($request->validated())) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = Auth::user();

        // Check if email is verified
        if (!$user->email_verified_at) {
            return response()->json([
                'message' => 'Please verify your email before logging in',
            ], Response::HTTP_FORBIDDEN);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user->load('role'),
            'token' => $token,
        ], Response::HTTP_OK);
    }
}