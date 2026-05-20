<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;

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

            return $this->createdResponse(
                ['user' => $user->load('role')->toArray()],
                'User registered successfully. Please verify your email.'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Registration failed',
                ['error' => $e->getMessage()],
                500
            );
        }
    }
}