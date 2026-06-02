<?php

namespace App\Enums\Authorization;

use App\Traits\BaseEnum;

enum CompanyRoles: string
{
    use BaseEnum;

    case OWNER = 'owner';
    case ADMIN = 'admin';
    case RECRUITER = 'recruiter';
    case EMPLOYEE = 'employee';
}