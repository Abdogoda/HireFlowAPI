<?php

namespace App\Http\Resources\Post;

use App\Http\Resources\User\UserSimpleResource;
use App\Http\Resources\Post\PostAttachmentResource;
use App\Http\Resources\Post\TagResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'company_id' => $this->company_id,
            'title' => $this->title,
            'content' => $this->content,
            'category' => $this->category?->value,
            'status' => $this->status?->value,
            'post_date' => $this->post_date?->toDateString(),
            'post_time' => $this->post_time,
            'author' => $this->relationLoaded('author') ? new UserSimpleResource($this->author) : null,
            'company' => $this->relationLoaded('company') && $this->company !== null ? [
                'id' => $this->company->id,
                'name' => $this->company->name,
                'slug' => $this->company->slug,
            ] : null,
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'attachments' => PostAttachmentResource::collection($this->whenLoaded('attachments')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}