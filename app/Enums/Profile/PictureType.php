<?php

namespace App\Enums\Profile;

use App\Traits\BaseEnum;

enum PictureType: string
{
    use BaseEnum;

    case PROFILE   = 'profile';
    case THUMBNAIL = 'thumbnail';
}
