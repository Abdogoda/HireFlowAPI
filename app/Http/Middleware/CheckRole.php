<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): SymfonyResponse
    {
        if (!$request->user()) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], Response::HTTP_UNAUTHORIZED);
        }

        if (!in_array($request->user()->role?->name, $roles)) {
            return response()->json([
                'message' => 'Forbidden - Insufficient permissions',
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
