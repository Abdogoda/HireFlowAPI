<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\IndexPostRequest;
use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Http\Resources\Post\PostResource;
use App\Models\Company;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(IndexPostRequest $request, PostService $postService): JsonResponse
    {
        $posts = $postService->getPosts($request->validated());

        return $this->successResponse([
            'posts' => PostResource::collection($posts->getCollection()),
            'pagination' => [
                'current_page' => $posts->currentPage(),
                'total' => $posts->total(),
                'per_page' => $posts->perPage(),
                'last_page' => $posts->lastPage(),
                'from' => $posts->firstItem(),
                'to' => $posts->lastItem(),
            ],
        ], 'Posts retrieved successfully');
    }

    public function show(Request $request, Post $post, PostService $postService): JsonResponse
    {
        $this->authorize('view', $post);

        return $this->successResponse(
            ['post' => $postService->getPost($post)],
            'Post retrieved successfully'
        );
    }

    public function store(StorePostRequest $request, PostService $postService): JsonResponse
    {
        $company = null;
        $validated = $request->validated();

        if (!empty($validated['company_id'])) {
            $company = Company::query()->findOrFail($validated['company_id']);
            $this->authorize('createForCompany', [Post::class, $company]);
        } else {
            $this->authorize('create', Post::class);
        }

        $result = $postService->createPost(
            $request->user(),
            $validated,
            $request->file('attachments', []),
            $company
        );

        return $this->createdResponse(
            ['post' => $result],
            'Post created successfully'
        );
    }

    public function update(UpdatePostRequest $request, Post $post, PostService $postService): JsonResponse
    {
        $this->authorize('update', $post);

        $result = $postService->updatePost(
            $post,
            $request->validated(),
            $request->file('attachments', [])
        );

        return $this->successResponse(
            ['post' => $result],
            'Post updated successfully'
        );
    }

    public function destroy(Request $request, Post $post, PostService $postService): JsonResponse
    {
        $this->authorize('delete', $post);

        $postService->deletePost($post);

        return $this->successResponse(null, 'Post deleted successfully');
    }
}