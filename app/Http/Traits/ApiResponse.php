<?php

namespace App\Http\Traits;

use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Success response
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
     * Created response
     */
    protected function createdResponse(
        array $data,
        string $message = 'Resource created successfully'
    ): JsonResponse {
        return ResponseService::created($data, $message);
    }

    /**
     * Validation error response
     */
    protected function validationErrorResponse(
        array $errors,
        string $message = 'Validation failed'
    ): JsonResponse {
        return ResponseService::validationError($errors, $message);
    }

    /**
     * Unauthorized response
     */
    protected function unauthorizedResponse(
        string $message = 'Unauthorized'
    ): JsonResponse {
        return ResponseService::unauthorized($message);
    }

    /**
     * Forbidden response
     */
    protected function forbiddenResponse(
        string $message = 'Forbidden'
    ): JsonResponse {
        return ResponseService::forbidden($message);
    }

    /**
     * Not found response
     */
    protected function notFoundResponse(
        string $message = 'Resource not found'
    ): JsonResponse {
        return ResponseService::notFound($message);
    }

    /**
     * Conflict response
     */
    protected function conflictResponse(
        string $message = 'Resource conflict'
    ): JsonResponse {
        return ResponseService::conflict($message);
    }

    /**
     * Paginated response
     */
    protected function paginatedResponse(
        $items,
        string $message = 'Data retrieved successfully'
    ): JsonResponse {
        return ResponseService::paginated($items, $message);
    }
}
