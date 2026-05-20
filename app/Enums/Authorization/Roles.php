<?php

namespace App\Enums\Authorization;

use App\Traits\Enums\BaseEnum;

enum Roles: string
{
    use BaseEnum;

    case ADMIN       = 'admin';
    case CANDIDATE   = 'candidate';
    case RECRUTER    = 'recruter';
}