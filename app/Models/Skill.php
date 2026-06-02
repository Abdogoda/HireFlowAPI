<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\Skill\ProficiencyLevel;

#[Fillable(['user_id', 'name', 'proficiency_level'])]
class Skill extends Model
{
    protected function casts(): array
    {
        return [
            'proficiency_level' => ProficiencyLevel::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
