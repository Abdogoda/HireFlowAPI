<?php

use App\Enums\Post\PostCategory;
use App\Enums\Post\PostStatus;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->createDefaultRoles();
    Storage::fake('public');
});

describe('List Posts', function () {
    it('lists posts for authenticated users', function () {
        $author = $this->createUser(['email' => 'author@example.com']);
        $viewer = $this->createUser(['email' => 'viewer@example.com']);

        $company = $author->ownedCompanies()->create([
            'slug' => 'post-list-company',
            'name' => 'Post List Company',
        ]);

        $company->posts()->create([
            'user_id' => $author->id,
            'title' => 'Company Announcement',
            'content' => '<p>We shipped a new release.</p>',
            'category' => PostCategory::ANNOUNCEMENT->value,
            'status' => PostStatus::PUBLISHED->value,
            'post_date' => now()->toDateString(),
            'post_time' => now()->format('H:i:s'),
        ]);

        $author->posts()->create([
            'title' => 'Personal Update',
            'content' => '<p>Building a better hiring flow.</p>',
            'category' => PostCategory::UPDATE->value,
            'status' => PostStatus::DRAFT->value,
        ]);

        $response = $this->actingAs($viewer)->getJson('/api/posts?search=Update');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Posts retrieved successfully',
            ])
            ->assertJsonCount(1, 'data.posts')
            ->assertJsonPath('data.posts.0.title', 'Personal Update');
    });
});