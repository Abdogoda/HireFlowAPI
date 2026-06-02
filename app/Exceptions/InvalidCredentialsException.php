<?php

namespace App\Exceptions;

class InvalidCredentialsException extends BaseException
{
    protected int $statusCode = 401;
}
