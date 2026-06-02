<?php

namespace App\Enums\Vacancy;

use App\Traits\BaseEnum;

enum EmploymentType: string
{
    use BaseEnum;

    case FULL_TIME = 'full_time';
    case PART_TIME = 'part_time';
    case CONTRACT = 'contract';
    case INTERNSHIP = 'internship';
    case FREELANCE = 'freelance';
}