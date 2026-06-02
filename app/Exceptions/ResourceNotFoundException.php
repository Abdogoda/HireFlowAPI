<?php

namespace App\Exceptions;

class ResourceNotFoundException extends BaseException
{
    protected int $statusCode = 404;
}
