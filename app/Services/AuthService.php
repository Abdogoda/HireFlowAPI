<?php

namespace App\Services;

use App\Models\User;
use App\Http\Resources\User\UserSimpleResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\Verified;
use App\Exceptions\BusinessLogicException;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\UnauthorizedActionException;
use App\Exceptions\InvalidCredentialsException;

class AuthService
{
    /**
     * Register a new user
     *
     * @param array $data User registration data
     * @return UserSimpleResource|JsonResponse
     */
    public function register(array $data): UserSimpleResource
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role_id' => $data['role_id'],
        ]);

        $this->sendEmailVerification($user);

        return new UserSimpleResource($user->load('role'));
    }

    /**
     * Authenticate user and create API token
     *
     * @param array $credentials
     * @return array|JsonResponse ['user' => UserSimpleResource, 'token' => string] or error response
     */
    public function login(array $credentials): array
    {
        if (!Auth::attempt($credentials)) {
            throw new InvalidCredentialsException('Invalid credentials');
        }

        $user = Auth::user();

        if (!$user->hasVerifiedEmail()) {
            $this->sendEmailVerification($user);

            throw new UnauthorizedActionException('Please verify your email before logging in');
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
    public function logout(User $user): true
    {
        $currentToken = $user->currentAccessToken();

        if (!$currentToken) {
            throw new UnauthorizedActionException('No active token found');
        }

        $currentToken->delete();
        return true;
    }

    /**
     * Refresh user's API token
     *
     * @param User $user
     * @return string|JsonResponse New token or error response
     */
    public function refreshToken(User $user): string
    {
        $currentToken = $user->currentAccessToken();

        if (!$currentToken) {
            throw new UnauthorizedActionException('No active token found');
        }

        $currentToken->delete();
        return $user->createToken('api-token')->plainTextToken;
    }

    /**
     * Send password reset link
     *
     * @param array $credentials
     * @return true|JsonResponse
     */
    public function sendPasswordResetLink(array $credentials): true
    {
        $status = Password::sendResetLink($credentials);

        if ($status !== Password::RESET_LINK_SENT) {
            throw new BusinessLogicException('Unable to send password reset link', ['error' => __($status)]);
        }

        return true;
    }

    /**
     * Reset user password
     *
     * @param array $data
     * @return true|JsonResponse
     */
    public function resetPassword(array $data): true
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
            throw new BusinessLogicException('Unable to reset password', ['error' => __($status)]);
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
    public function verifyEmail(int $userId, string $hash): array
    {
        $user = User::find($userId);

        if (!$user) {
            throw new ResourceNotFoundException('User not found');
        }

        // Verify the hash
        if (sha1($user->email) !== $hash) {
            throw new UnauthorizedActionException('Signature mismatch');
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
    public function sendEmailVerification(User $user): true
    {
        $user->sendEmailVerificationNotification();
        return true;
    }
}