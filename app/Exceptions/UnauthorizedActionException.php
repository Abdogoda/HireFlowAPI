<?php

namespace App\Exceptions;

class UnauthorizedActionException extends BaseException
{
    protected int $statusCode = 403;
}
