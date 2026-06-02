<?php

use App\Enums\Company\CompanyRoles;
use App\Enums\Post\PostCategory;
use App\Enums\Post\PostStatus;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->createDefaultRoles();
    Storage::fake('public');
});

describe('Delete Post', function () {
    it('allows a company admin to delete a company post', function () {
        $owner = $this->createUser(['email' => 'owner@example.com']);
        $company = $owner->ownedCompanies()->create([
            'slug' => 'post-delete-company',
            'name' => 'Post Delete Company',
        ]);

        $admin = $this->createUserWithRole('Admin', [
            'email' => 'admin@example.com',
        ]);

        $company->memberships()->create([
            'member_id' => $admin->id,
            'company_role' => CompanyRoles::ADMIN->value,
            'position' => 'Admin',
            'is_current_position' => true,
        ]);

        $post = $company->posts()->create([
            'user_id' => $admin->id,
            'title' => 'Company Update To Delete',
            'content' => '<p>Temporary post.</p>',
            'category' => PostCategory::ANNOUNCEMENT->value,
            'status' => PostStatus::PUBLISHED->value,
            'post_date' => now()->toDateString(),
            'post_time' => now()->format('H:i:s'),
        ]);

        $response = $this->actingAs($admin)->deleteJson('/api/posts/' . $post->id);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Post deleted successfully',
            ]);

        $this->assertDatabaseMissing('posts', [
            'id' => $post->id,
        ]);
    });
});