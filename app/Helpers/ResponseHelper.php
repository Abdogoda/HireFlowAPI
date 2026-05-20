<?php

use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;

if (!function_exists('responseSuccess')) {
    /**
     * Return a success response
     */
    function responseSuccess(
        ?array $data = null,
        string $message = 'Operation successful',
        int $statusCode = 200
    ): JsonResponse {
        return ResponseService::success($data, $message, $statusCode);
    }
}

if (!function_exists('responseError')) {
    /**
     * Return an error response
     */
    function responseError(
        string $message = 'An error occurred',
        ?array $errors = null,
        int $statusCode = 500,
        ?array $data = null
    ): JsonResponse {
        return ResponseService::error($message, $errors, $statusCode, $data);
    }
}

if (!function_exists('responseCreated')) {
    /**
     * Return a created response
     */
    function responseCreated(
        array $data,
        string $message = 'Resource created successfully'
    ): JsonResponse {
        return ResponseService::created($data, $message);
    }
}

if (!function_exists('responseValidationError')) {
    /**
     * Return a validation error response
     */
    function responseValidationError(
        array $errors,
        string $message = 'Validation failed'
    ): JsonResponse {
        return ResponseService::validationError($errors, $message);
    }
}

if (!function_exists('responseUnauthorized')) {
    /**
     * Return an unauthorized response
     */
    function responseUnauthorized(
        string $message = 'Unauthorized'
    ): JsonResponse {
        return ResponseService::unauthorized($message);
    }
}

if (!function_exists('responseForbidden')) {
    /**
     * Return a forbidden response
     */
    function responseForbidden(
        string $message = 'Forbidden'
    ): JsonResponse {
        return ResponseService::forbidden($message);
    }
}

if (!function_exists('responseNotFound')) {
    /**
     * Return a not found response
     */
    function responseNotFound(
        string $message = 'Resource not found'
    ): JsonResponse {
        return ResponseService::notFound($message);
    }
}

if (!function_exists('responseConflict')) {
    /**
     * Return a conflict response
     */
    function responseConflict(
        string $message = 'Resource conflict'
    ): JsonResponse {
        return ResponseService::conflict($message);
    }
}

if (!function_exists('responsePaginated')) {
    /**
     * Return a paginated response
     */
    function responsePaginated(
        $items,
        string $message = 'Data retrieved successfully'
    ): JsonResponse {
        return ResponseService::paginated($items, $message);
    }
}
