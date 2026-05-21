<?php

namespace App\Enums\Users;

use App\Traits\BaseEnum;

enum Religion: int
{
    use BaseEnum;

    case OTHER     = 0;
    case MUSLIM    = 1;
    case CHRISTIAN = 2;
    case JEWISH    = 3;
}
