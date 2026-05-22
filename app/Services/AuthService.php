<?php

namespace App\Services;

use App\Models\User;
use App\Http\Resources\User\UserSimpleResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\Verified;

class AuthService
{
    /**
     * Register a new user
     *
     * @param array $data User registration data
     * @return UserSimpleResource|JsonResponse
     */
    public function register(array $data): UserSimpleResource|JsonResponse
    {
        try {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role_id' => $data['role_id'],
            ]);

            $verificationResult = $this->sendEmailVerification($user);

            if ($verificationResult instanceof JsonResponse) {
                return $verificationResult;
            }

            return new UserSimpleResource($user->load('role'));
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to register user',
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * Authenticate user and create API token
     *
     * @param array $credentials
     * @return array|JsonResponse ['user' => UserSimpleResource, 'token' => string] or error response
     */
    public function login(array $credentials): array|JsonResponse
    {
        if (!Auth::attempt($credentials)) {
            return ResponseService::unauthorized('Invalid credentials');
        }

        $user = Auth::user();

        if (!$user->hasVerifiedEmail()) {
            $this->sendEmailVerification($user);

            return ResponseService::forbidden('Please verify your email before logging in');
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return [
            'user' => new UserSimpleResource($user->load('role')),
            'token' => $token,
        ];
    }

    /**
     * Logout user by revoking current token
     *
     * @param User $user
     * @return true|JsonResponse
     */
    public function logout(User $user): true|JsonResponse
    {
        try {
            $currentToken = $user->currentAccessToken();

            if (!$currentToken) {
                return ResponseService::unauthorized('No active token found');
            }

            $currentToken->delete();
            return true;
        } catch (\Exception $e) {
            return ResponseService::error('Failed to logout', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Refresh user's API token
     *
     * @param User $user
     * @return string|JsonResponse New token or error response
     */
    public function refreshToken(User $user): string|JsonResponse
    {
        try {
            $currentToken = $user->currentAccessToken();

            if (!$currentToken) {
                return ResponseService::unauthorized('No active token found');
            }

            $currentToken->delete();
            return $user->createToken('api-token')->plainTextToken;
        } catch (\Exception $e) {
            return ResponseService::error('Failed to refresh token', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Send password reset link
     *
     * @param array $credentials
     * @return true|JsonResponse
     */
    public function sendPasswordResetLink(array $credentials): true|JsonResponse
    {
        $status = Password::sendResetLink($credentials);

        if ($status !== Password::RESET_LINK_SENT) {
            return ResponseService::error(
                'Unable to send password reset link',
                ['error' => __($status)],
                400
            );
        }

        return true;
    }

    /**
     * Reset user password
     *
     * @param array $data
     * @return true|JsonResponse
     */
    public function resetPassword(array $data): true|JsonResponse
    {
        $status = Password::reset(
            $data,
            function ($user, $password) {
                $user->forceFill([
                    'password' => $password,
                ])->save();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return ResponseService::error(
                'Unable to reset password',
                ['error' => __($status)],
                400
            );
        }

        return true;
    }

    /**
     * Verify user email using hash
     *
     * @param int $userId
     * @param string $hash
     * @return array|JsonResponse ['verified' => bool] or error response
     */
    public function verifyEmail(int $userId, string $hash): array|JsonResponse
    {
        $user = User::find($userId);

        if (!$user) {
            return ResponseService::notFound('User not found');
        }

        // Verify the hash
        if (sha1($user->email) !== $hash) {
            return ResponseService::forbidden('Signature mismatch');
        }

        if ($user->hasVerifiedEmail()) {
            return ['verified' => false];
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
            return ['verified' => true];
        }

        return ['verified' => false];
    }

    /**
     * Send email verification notification
     *
     * @param User $user
     * @return true|JsonResponse
     */
    public function sendEmailVerification(User $user): true|JsonResponse
    {
        try {
            $user->sendEmailVerificationNotification();
            return true;
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to send verification email',
                ['error' => $e->getMessage()],
                500
            );
        }
    }
}