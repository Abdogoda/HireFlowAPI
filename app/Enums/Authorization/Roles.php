<?php

namespace App\Enums\Authorization;

use App\Traits\BaseEnum;

enum Roles: string
{
    use BaseEnum;

    case ADMIN       = 'admin';
    case CANDIDATE   = 'candidate';
    case RECRUITER   = 'recruiter';
}