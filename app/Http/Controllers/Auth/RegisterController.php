<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class RegisterController extends Controller
{
    public function __invoke(RegisterRequest $request): JsonResponse
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
                'role_id' => $request->role_id,
            ]);

            return response()->json([
                'message' => 'User registered successfully. Please verify your email.',
                'user' => $user->load('role'),
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Registration failed',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}