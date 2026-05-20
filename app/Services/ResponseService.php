<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ResponseService
{
    /**
     * Success response
     */
    public static function success(
        ?array $data = null,
        string $message = 'Operation successful',
        int $statusCode = Response::HTTP_OK
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => now()->toIso8601String(),
        ], $statusCode);
    }

    /**
     * Error response
     */
    public static function error(
        string $message = 'An error occurred',
        ?array $errors = null,
        int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR,
        ?array $data = null
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'data' => $data,
            'timestamp' => now()->toIso8601String(),
        ], $statusCode);
    }

    /**
     * Created response (201)
     */
    public static function created(
        array $data,
        string $message = 'Resource created successfully'
    ): JsonResponse {
        return self::success($data, $message, Response::HTTP_CREATED);
    }

    /**
     * Validation error response (422)
     */
    public static function validationError(
        array $errors,
        string $message = 'Validation failed'
    ): JsonResponse {
        return self::error(
            $message,
            $errors,
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    /**
     * Unauthorized response (401)
     */
    public static function unauthorized(
        string $message = 'Unauthorized'
    ): JsonResponse {
        return self::error(
            $message,
            null,
            Response::HTTP_UNAUTHORIZED
        );
    }

    /**
     * Forbidden response (403)
     */
    public static function forbidden(
        string $message = 'Forbidden'
    ): JsonResponse {
        return self::error(
            $message,
            null,
            Response::HTTP_FORBIDDEN
        );
    }

    /**
     * Not found response (404)
     */
    public static function notFound(
        string $message = 'Resource not found'
    ): JsonResponse {
        return self::error(
            $message,
            null,
            Response::HTTP_NOT_FOUND
        );
    }

    /**
     * Conflict response (409)
     */
    public static function conflict(
        string $message = 'Resource conflict'
    ): JsonResponse {
        return self::error(
            $message,
            null,
            Response::HTTP_CONFLICT
        );
    }

    /**
     * Paginated response
     */
    public static function paginated(
        $items,
        string $message = 'Data retrieved successfully'
    ): JsonResponse {
        return self::success([
            'items' => $items->items(),
            'pagination' => [
                'current_page' => $items->currentPage(),
                'total' => $items->total(),
                'per_page' => $items->perPage(),
                'last_page' => $items->lastPage(),
                'from' => $items->firstItem(),
                'to' => $items->lastItem(),
            ],
        ], $message);
    }
}
