<?php

namespace App\Enums\Users;

use App\Traits\Enums\BaseEnum;

enum AccountStatus: int
{
    use BaseEnum;

    case ACCEPTED  = 1;
    case PENDING   = 2; // default
    case REJECTED  = 3;
    case SUSPENDED = 4;
    case BLOCKED   = 5;
}
