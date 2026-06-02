<?php

namespace App\Enums\Vacancy;

use App\Traits\BaseEnum;

enum VacancyStatus: string
{
    use BaseEnum;

    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case CLOSED = 'closed';
}