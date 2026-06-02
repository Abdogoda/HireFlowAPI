<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use App\Services\ResponseService;

abstract class BaseException extends Exception
{
    /**
     * The HTTP status code to return.
     *
     * @var int
     */
    protected int $statusCode = 500;

    /**
     * Additional data to include in the response.
     *
     * @var array|null
     */
    protected ?array $data = null;

    /**
     * Create a new exception instance.
     *
     * @param string $message
     * @param array|null $data
     * @param \Throwable|null $previous
     */
    public function __construct(string $message = "", ?array $data = null, \Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->data = $data;
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function render($request): JsonResponse
    {
        return ResponseService::error(
            $this->getMessage(),
            null,
            $this->statusCode,
            $this->data
        );
    }
}
