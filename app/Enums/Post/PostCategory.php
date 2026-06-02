<?php

namespace App\Enums\Post;

use App\Traits\BaseEnum;

enum PostCategory: string
{
    use BaseEnum;

    case GENERAL = 'general';
    case ANNOUNCEMENT = 'announcement';
    case UPDATE = 'update';
    case EVENT = 'event';
    case NEWS = 'news';
}