<?php

namespace App\Enums\Documents;

use App\Traits\Enums\BaseEnum;

enum DocumentType: string
{
    use BaseEnum;

    case PROFILE_PICTURE = 'profile_picture';
    case THUMBNAIL       = 'thumbnail';
    case AVATAR          = 'avatar';
    case RESUME          = 'resume';
    case CERTIFICATE     = 'certificate';
    case IMAGE           = 'image';
    case VIDEO           = 'video';
    case PDF             = 'pdf';
    case OTHER           = 'other';
}