<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    public function __invoke(): JsonResponse
    {
        Auth::user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout successful',
        ], Response::HTTP_OK);
    }
}