<?php

namespace App\Services;

use App\Enums\Post\PostStatus;
use App\Http\Resources\Post\PostResource;
use App\Models\Company;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostService
{
    public function getPosts(array $filters = []): LengthAwarePaginator
    {
        $query = Post::query()->with(['author.role', 'company', 'tags', 'attachments']);

        $this->applyFilters($query, $filters);

        $sortBy = $filters['sort_by'] ?? 'post_date';
        $sortDirection = $filters['sort_direction'] ?? 'desc';
        $perPage = (int) ($filters['per_page'] ?? 15);

        return $query
            ->orderBy($sortBy, $sortDirection)
            ->orderByDesc('post_time')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getPost(Post $post): PostResource
    {
        return new PostResource($post->load(['author.role', 'company', 'tags', 'attachments']));
    }

    public function createPost(User $user, array $data, array $attachments = [], ?Company $company = null): PostResource
    {
        $payload = $this->normalizePayload($data, $user, $company);

        $post = DB::transaction(function () use ($payload, $attachments) {
            $post = Post::create($payload);

            $this->syncTags($post, $payload['tags'] ?? []);
            $this->storeAttachments($post, $attachments);

            return $post;
        });

        return $this->getPost($post);
    }

    public function updatePost(Post $post, array $data, array $attachments = []): PostResource
    {
        $payload = $this->normalizePayload($data, $post->author, $post->company, $post);

        $post->update(array_filter([
            'title' => $payload['title'] ?? null,
            'content' => $payload['content'] ?? null,
            'category' => $payload['category'] ?? null,
            'status' => $payload['status'] ?? null,
            'post_date' => $payload['post_date'] ?? null,
            'post_time' => $payload['post_time'] ?? null,
        ], static fn ($value) => $value !== null));

        DB::transaction(function () use ($post, $payload, $attachments) {
            if (array_key_exists('tags', $payload)) {
                $this->syncTags($post, $payload['tags']);
            }

            $this->storeAttachments($post, $attachments);
        });

        return $this->getPost($post->fresh());
    }

    public function deletePost(Post $post): bool
    {
        $post->loadMissing('attachments');

        foreach ($post->attachments as $attachment) {
            if ($attachment->file_path && Storage::disk('public')->exists($attachment->file_path)) {
                Storage::disk('public')->delete($attachment->file_path);
            }
        }

        $post->tags()->detach();
        $post->attachments()->delete();
        $post->delete();

        return true;
    }

    private function normalizePayload(array $data, ?User $user = null, ?Company $company = null, ?Post $post = null): array
    {
        $payload = $data;

        if ($user !== null) {
            $payload['user_id'] = $user->id;
        }

        if ($company !== null) {
            $payload['company_id'] = $company->id;
        }

        $status = $payload['status'] ?? $post?->status?->value ?? PostStatus::DRAFT->value;
        $payload['status'] = $status;

        if ($status === PostStatus::PUBLISHED->value && empty($payload['post_date']) && empty($payload['post_time'])) {
            $payload['post_date'] = now()->toDateString();
            $payload['post_time'] = now()->format('H:i:s');
        }

        if (!empty($payload['post_time']) && strlen((string) $payload['post_time']) === 5) {
            $payload['post_time'] = $payload['post_time'] . ':00';
        }

        return $payload;
    }

    private function syncTags(Post $post, array|string|null $tags): void
    {
        $normalizedTags = $this->normalizeTags($tags);

        if ($normalizedTags === []) {
            $post->tags()->detach();
            return;
        }

        $tagIds = [];

        foreach ($normalizedTags as $tagName) {
            $slug = Str::slug($tagName);

            $tagIds[] = Tag::query()->updateOrCreate([
                'slug' => $slug,
            ], [
                'name' => $tagName,
            ])->id;
        }

        $post->tags()->sync($tagIds);
    }

    private function storeAttachments(Post $post, array $attachments): void
    {
        foreach ($attachments as $attachment) {
            if (!$attachment instanceof UploadedFile) {
                continue;
            }

            $path = $attachment->store("posts/{$post->id}/attachments", 'public');

            $post->attachments()->create([
                'file_name' => $attachment->getClientOriginalName(),
                'file_path' => $path,
                'file_url' => Storage::disk('public')->url($path),
                'mime_type' => $attachment->getClientMimeType(),
                'size' => $attachment->getSize(),
            ]);
        }
    }

    private function normalizeTags(array|string|null $tags): array
    {
        if ($tags === null) {
            return [];
        }

        if (is_string($tags)) {
            $tags = explode(',', $tags);
        }

        return collect($tags)
            ->map(static fn ($tag) => trim((string) $tag))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function applyFilters(Builder $query, array $filters): void
    {
        if (!empty($filters['search'])) {
            $search = trim($filters['search']);

            $query->where(function (Builder $builder) use ($search): void {
                $builder->where('title', 'like', '%' . $search . '%')
                    ->orWhere('content', 'like', '%' . $search . '%')
                    ->orWhereHas('company', function (Builder $companyQuery) use ($search): void {
                        $companyQuery->where('name', 'like', '%' . $search . '%')
                            ->orWhere('slug', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('tags', function (Builder $tagQuery) use ($search): void {
                        $tagQuery->where('name', 'like', '%' . $search . '%')
                            ->orWhere('slug', 'like', '%' . $search . '%');
                    });
            });
        }

        if (!empty($filters['company_id'])) {
            $query->where('company_id', $filters['company_id']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['tag'])) {
            $tag = trim($filters['tag']);

            $query->whereHas('tags', function (Builder $builder) use ($tag): void {
                $builder->where('name', 'like', '%' . $tag . '%')
                    ->orWhere('slug', 'like', '%' . Str::slug($tag) . '%');
            });
        }
    }
}