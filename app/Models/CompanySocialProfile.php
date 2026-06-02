<?php

namespace App\Models;

use App\Enums\SocialProfileType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['company_id', 'social_profile_type', 'profile_url'])]
class CompanySocialProfile extends Model
{
    protected function casts(): array
    {
        return [
            'social_profile_type' => SocialProfileType::class,
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
