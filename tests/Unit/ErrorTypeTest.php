<?php

use App\Enums\Exception\ErrorType;

it('maps error types to http status codes', function () {
    expect(ErrorType::UNKNOWN_ERROR->toHttpStatus())->toBeInt()->toBe(500);
    expect(ErrorType::MODEL_NOT_FOUND->toHttpStatus())->toBe(404);
    expect(ErrorType::INVALID_CREDENTIALS->toHttpStatus())->toBe(401);
    expect(ErrorType::OTP_EXPIRED->toHttpStatus())->toBe(410);
    expect(ErrorType::DOCUMENT_ALREADY_EXISTS->toHttpStatus())->toBe(409);
});
