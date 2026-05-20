<?php

namespace App\Enums\Users;

use App\Traits\Enums\BaseEnum;

enum Gender: int
{
    use BaseEnum;

    case OTHER  = 0;
    case MALE   = 1;
    case FEMALE = 2;
}
