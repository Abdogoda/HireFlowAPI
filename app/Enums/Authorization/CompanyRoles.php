<?php

namespace App\Enums\Authorization;

use App\Traits\BaseEnum;

enum CompanyRoles: string
{
    use BaseEnum;

    case OWNER = 'company_owner';
    case ADMIN = 'admin';
    case RECRUITER = 'recruiter';
    case CANDIDATE = 'candidate';
}