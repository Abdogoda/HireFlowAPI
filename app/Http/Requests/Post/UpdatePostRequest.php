<?php

namespace App\Http\Requests\Post;

use App\Enums\Post\PostCategory;
use App\Enums\Post\PostStatus;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'category' => 'sometimes|in:' . PostCategory::toString(),
            'status' => 'sometimes|in:' . PostStatus::toString(),
            'post_date' => 'nullable|required_if:status,scheduled|date|after_or_equal:today',
            'post_time' => 'nullable|required_if:status,scheduled|date_format:H:i,H:i:s',
            'tags' => 'sometimes|array',
            'tags.*' => 'string|max:50',
            'attachments' => 'sometimes|array',
            'attachments.*' => 'file|max:20480',
        ];
    }
}