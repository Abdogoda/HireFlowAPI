<?php

namespace App\Exceptions;

class BusinessLogicException extends BaseException
{
    protected int $statusCode = 400;
}
