<?php

namespace App\Enums\Users;

use App\Traits\BaseEnum;

enum Religion: string
{
    use BaseEnum;

    case OTHER     = 'other';
    case MUSLIM    = 'muslim';
    case CHRISTIAN = 'christian';
    case JEWISH    = 'jewish';
}
