<?php

namespace App\Enums\Post;

use App\Traits\BaseEnum;

enum PostStatus: string
{
    use BaseEnum;

    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case SCHEDULED = 'scheduled';
    case ARCHIVED = 'archived';
}