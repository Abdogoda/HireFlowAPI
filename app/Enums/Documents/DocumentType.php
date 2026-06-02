<?php

namespace App\Enums\Documents;

use App\Traits\BaseEnum;

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

    /**
     * Category: 'image', 'video', 'pdf', 'document', or 'other'
     */
    public function category(): string
    {
        return match ($this) {
            self::PROFILE_PICTURE, self::THUMBNAIL, self::AVATAR, self::IMAGE => 'image',
            self::VIDEO => 'video',
            self::PDF => 'pdf',
            self::RESUME, self::CERTIFICATE => 'document',
            default => 'other',
        };
    }

    public function isImage(): bool
    {
        return $this->category() === 'image';
    }

    public function isVideo(): bool
    {
        return $this->category() === 'video';
    }

    public function isPdf(): bool
    {
        return $this->category() === 'pdf';
    }
}