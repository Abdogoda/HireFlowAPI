<?php

namespace App\Enums\Users;

use App\Traits\BaseEnum;

enum Gender: string
{
    use BaseEnum;

    case OTHER  = 'other';
    case MALE   = 'male';
    case FEMALE = 'female';
}
