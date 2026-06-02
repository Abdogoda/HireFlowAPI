<?php

use App\Enums\Post\PostCategory;
use App\Enums\Post\PostStatus;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->createDefaultRoles();
    Storage::fake('public');
});

describe('Update Post', function () {
    it('allows the author to update a personal post', function () {
        $author = $this->createUser(['email' => 'author@example.com']);

        $post = $author->posts()->create([
            'title' => 'Original Title',
            'content' => '<p>Original content.</p>',
            'category' => PostCategory::GENERAL->value,
            'status' => PostStatus::DRAFT->value,
        ]);

        $response = $this->actingAs($author)->patchJson('/api/posts/' . $post->id, [
            'title' => 'Updated Title',
            'content' => '<p>Updated content.</p>',
            'category' => PostCategory::UPDATE->value,
            'status' => PostStatus::PUBLISHED->value,
            'post_date' => now()->toDateString(),
            'post_time' => now()->format('H:i'),
            'tags' => ['updated', 'release'],
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Post updated successfully',
                'data' => [
                    'post' => [
                        'title' => 'Updated Title',
                        'status' => PostStatus::PUBLISHED->value,
                        'category' => PostCategory::UPDATE->value,
                    ],
                ],
            ])
            ->assertJsonCount(2, 'data.post.tags');

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Updated Title',
            'status' => PostStatus::PUBLISHED->value,
        ]);
    });
});