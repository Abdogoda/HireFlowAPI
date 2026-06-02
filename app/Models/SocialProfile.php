<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\SocialProfileType;

#[Fillable(['user_id', 'social_profile_type', 'profile_url'])]
class SocialProfile extends Model
{

    protected function casts(): array
    {
        return [
            'social_profile_type' => SocialProfileType::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
