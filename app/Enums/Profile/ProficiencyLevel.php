<?php

namespace App\Enums\Profile;

use App\Traits\BaseEnum;

enum ProficiencyLevel: string
{
    use BaseEnum;

    case BEGINNER     = 'beginner';
    case INTERMEDIATE = 'intermediate';
    case ADVANCED     = 'advanced';
    case EXPERT       = 'expert';
}
