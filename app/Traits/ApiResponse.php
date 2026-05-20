<?php

namespace App\Traits;

use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Success response (200 OK)
     */
    protected function successResponse(
        ?array $data = null,
        string $message = 'Operation successful',
        int $statusCode = 200
    ): JsonResponse {
        return ResponseService::success($data, $message, $statusCode);
    }

    /**
     * Error response
     */
    protected function errorResponse(
        string $message = 'An error occurred',
        ?array $errors = null,
        int $statusCode = 500,
        ?array $data = null
    ): JsonResponse {
        return ResponseService::error($message, $errors, $statusCode, $data);
    }

    /**
     * Created response (201 Created)
     */
    protected function createdResponse(
        array $data,
        string $message = 'Resource created successfully'
    ): JsonResponse {
        return ResponseService::created($data, $message);
    }

    /**
     * Validation error response (422 Unprocessable Entity)
     */
    protected function validationErrorResponse(
        array $errors,
        string $message = 'Validation failed'
    ): JsonResponse {
        return ResponseService::validationError($errors, $message);
    }

    /**
     * Unauthorized response (401 Unauthorized)
     */
    protected function unauthorizedResponse(
        string $message = 'Unauthorized'
    ): JsonResponse {
        return ResponseService::unauthorized($message);
    }

    /**
     * Forbidden response (403 Forbidden)
     */
    protected function forbiddenResponse(
        string $message = 'Forbidden'
    ): JsonResponse {
        return ResponseService::forbidden($message);
    }

    /**
     * Not found response (404 Not Found)
     */
    protected function notFoundResponse(
        string $message = 'Resource not found'
    ): JsonResponse {
        return ResponseService::notFound($message);
    }

    /**
     * Conflict response (409 Conflict)
     */
    protected function conflictResponse(
        string $message = 'Resource conflict'
    ): JsonResponse {
        return ResponseService::conflict($message);
    }

    /**
     * Paginated response (200 OK with pagination metadata)
     */
    protected function paginatedResponse(
        $items,
        string $message = 'Data retrieved successfully'
    ): JsonResponse {
        return ResponseService::paginated($items, $message);
    }
}