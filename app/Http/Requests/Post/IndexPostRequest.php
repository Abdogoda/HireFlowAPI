<?php

namespace App\Http\Requests\Post;

use App\Enums\Post\PostCategory;
use App\Enums\Post\PostStatus;
use Illuminate\Foundation\Http\FormRequest;

class IndexPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => 'sometimes|nullable|string|max:255',
            'company_id' => 'sometimes|nullable|exists:companies,id',
            'user_id' => 'sometimes|nullable|exists:users,id',
            'category' => 'sometimes|nullable|in:' . PostCategory::toString(),
            'status' => 'sometimes|nullable|in:' . PostStatus::toString(),
            'tag' => 'sometimes|nullable|string|max:100',
            'per_page' => 'sometimes|nullable|integer|min:1|max:100',
            'sort_by' => 'sometimes|nullable|in:title,post_date,post_time,status,category,created_at,updated_at',
            'sort_direction' => 'sometimes|nullable|in:asc,desc',
        ];
    }
}