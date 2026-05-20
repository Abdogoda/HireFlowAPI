<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VerifyEmailController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = User::find($request->route('id'));

        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], Response::HTTP_NOT_FOUND);
        }

        // Verify the hash
        if (sha1($user->email) !== $request->route('hash')) {
            return response()->json([
                'message' => 'Signature mismatch',
            ], Response::HTTP_FORBIDDEN);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email already verified',
            ], Response::HTTP_OK);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return response()->json([
            'message' => 'Email verified successfully',
        ], Response::HTTP_OK);
    }
}