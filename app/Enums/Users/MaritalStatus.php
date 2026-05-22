<?php

namespace App\Enums\Users;

use App\Traits\BaseEnum;

enum MaritalStatus: string
{
    use BaseEnum;

    case SINGLE   = 'single';
    case ENGAGED  = 'engaged';
    case MARRIED  = 'married';
    case DIVORCED = 'divorced';
    case WIDOWED  = 'widowed';
}
