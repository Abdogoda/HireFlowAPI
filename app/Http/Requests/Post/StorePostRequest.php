<?php

namespace App\Http\Requests\Post;

use App\Enums\Post\PostCategory;
use App\Enums\Post\PostStatus;
use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_id' => 'sometimes|nullable|exists:companies,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|in:' . PostCategory::toString(),
            'status' => 'sometimes|in:' . PostStatus::toString(),
            'post_date' => 'sometimes|nullable|required_if:status,scheduled|date|after_or_equal:today',
            'post_time' => 'sometimes|nullable|required_if:status,scheduled|date_format:H:i,H:i:s',
            'tags' => 'sometimes|array',
            'tags.*' => 'string|max:50',
            'attachments' => 'sometimes|array',
            'attachments.*' => 'file|max:20480',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Post title is required',
            'content.required' => 'Post content is required',
            'category.required' => 'Post category is required',
            'post_date.required_if' => 'Post date is required when the post is scheduled',
            'post_time.required_if' => 'Post time is required when the post is scheduled',
        ];
    }
}