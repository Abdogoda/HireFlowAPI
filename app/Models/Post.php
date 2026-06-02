<?php

namespace App\Models;

use App\Enums\Post\PostCategory;
use App\Enums\Post\PostStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\PostAttachment;
use App\Models\Tag;

#[Fillable(['user_id', 'company_id', 'title', 'content', 'category', 'status', 'post_date', 'post_time'])]
class Post extends Model
{
    protected function casts(): array
    {
        return [
            'post_date' => 'date',
            'category' => PostCategory::class,
            'status' => PostStatus::class,
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(PostAttachment::class);
    }
}