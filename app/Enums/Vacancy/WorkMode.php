<?php

namespace App\Enums\Vacancy;

use App\Traits\BaseEnum;

enum WorkMode: string
{
    use BaseEnum;

    case REMOTE = 'remote';
    case HYBRID = 'hybrid';
    case ONSITE = 'onsite';
}