<?php

namespace App\Enums\Users;

use App\Traits\Enums\BaseEnum;

enum SocialProfileType: int
{
    use BaseEnum;

    case LINKEDIN  = 1;
    case GITHUB    = 2;
    case TWITTER   = 3;
    case FACEBOOK  = 4;
    case INSTAGRAM = 5;
    case TIKTOK    = 6;
    case DISCORD   = 7;
    case YOUTUBE   = 8;
    case OTHER     = 9;
}