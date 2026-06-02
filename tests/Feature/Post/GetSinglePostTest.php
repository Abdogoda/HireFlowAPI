<?php

use App\Enums\Post\PostCategory;
use App\Enums\Post\PostStatus;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->createDefaultRoles();
    Storage::fake('public');
});

describe('Get Single Post', function () {
    it('shows a single post with related data', function () {
        $author = $this->createUser(['email' => 'author@example.com']);
        $company = $author->ownedCompanies()->create([
            'slug' => 'post-show-company',
            'name' => 'Post Show Company',
        ]);

        $post = $company->posts()->create([
            'user_id' => $author->id,
            'title' => 'Launch Day',
            'content' => '<p>We are live.</p>',
            'category' => PostCategory::NEWS->value,
            'status' => PostStatus::PUBLISHED->value,
            'post_date' => now()->toDateString(),
            'post_time' => now()->format('H:i:s'),
        ]);

        $response = $this->actingAs($author)->getJson('/api/posts/' . $post->id);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Post retrieved successfully',
                'data' => [
                    'post' => [
                        'id' => $post->id,
                        'title' => 'Launch Day',
                        'company' => [
                            'id' => $company->id,
                            'name' => 'Post Show Company',
                        ],
                    ],
                ],
            ]);
    });
});