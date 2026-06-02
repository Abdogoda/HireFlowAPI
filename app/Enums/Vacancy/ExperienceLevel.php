<?php

namespace App\Enums\Vacancy;

use App\Traits\BaseEnum;

enum ExperienceLevel: string
{
    use BaseEnum;

    case ENTRY = 'entry';
    case MID = 'mid';
    case SENIOR = 'senior';
    case LEAD = 'lead';
    case EXECUTIVE = 'executive';
}