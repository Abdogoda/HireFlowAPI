<?php

namespace App\Enums\Users;

use App\Traits\Enums\BaseEnum;

enum MaritalStatus: int
{
    use BaseEnum;

    case SINGLE   = 1;
    case ENGAGED  = 2;
    case MARRIED  = 3;
    case DIVORCED = 4;
    case WIDOWED  = 5;
}
