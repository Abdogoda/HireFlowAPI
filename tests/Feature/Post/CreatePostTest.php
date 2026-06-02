<?php

use App\Enums\Company\CompanyRoles;
use App\Enums\Post\PostCategory;
use App\Enums\Post\PostStatus;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->createDefaultRoles();
    Storage::fake('public');
});

describe('Create Post', function () {
    it('allows an authenticated user to create a personal post with tags and attachments', function () {
        $user = $this->createUser(['email' => 'poster@example.com']);

        $response = $this->actingAs($user)->post('/api/posts', [
            'title' => 'Weekly Hiring Update',
            'content' => '<p>We are <strong>hiring</strong> engineers.</p>',
            'category' => PostCategory::UPDATE->value,
            'status' => PostStatus::PUBLISHED->value,
            'post_date' => now()->toDateString(),
            'post_time' => now()->format('H:i'),
            'tags' => ['hiring', 'engineering'],
            'attachments' => [
                UploadedFile::fake()->create('brief.pdf', 120, 'application/pdf'),
                UploadedFile::fake()->image('banner.jpg'),
            ],
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Post created successfully',
                'data' => [
                    'post' => [
                        'title' => 'Weekly Hiring Update',
                        'content' => '<p>We are <strong>hiring</strong> engineers.</p>',
                        'category' => PostCategory::UPDATE->value,
                        'status' => PostStatus::PUBLISHED->value,
                    ],
                ],
            ])
            ->assertJsonCount(2, 'data.post.tags')
            ->assertJsonCount(2, 'data.post.attachments');

        $this->assertDatabaseHas('posts', [
            'user_id' => $user->id,
            'title' => 'Weekly Hiring Update',
            'category' => PostCategory::UPDATE->value,
            'status' => PostStatus::PUBLISHED->value,
        ]);

        $this->assertDatabaseCount('tags', 2);
        $this->assertDatabaseCount('post_attachments', 2);
    });

    it('allows a company recruiter to create a company post', function () {
        $owner = $this->createUser(['email' => 'owner@example.com']);
        $company = $owner->ownedCompanies()->create([
            'slug' => 'acme-jobs',
            'name' => 'Acme Jobs',
        ]);

        $recruiter = $this->createUserWithRole('Recruiter', [
            'email' => 'recruiter@example.com',
        ]);

        $company->memberships()->create([
            'member_id' => $recruiter->id,
            'company_role' => CompanyRoles::RECRUITER->value,
            'position' => 'Recruiter',
            'is_current_position' => true,
        ]);

        $response = $this->actingAs($recruiter)->postJson('/api/posts', [
            'company_id' => $company->id,
            'title' => 'New Engineering Roles',
            'content' => '<p>Join our team this quarter.</p>',
            'category' => PostCategory::ANNOUNCEMENT->value,
            'tags' => ['jobs', 'team'],
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'post' => [
                        'title' => 'New Engineering Roles',
                        'company' => [
                            'id' => $company->id,
                            'name' => 'Acme Jobs',
                        ],
                    ],
                ],
            ]);

        $this->assertDatabaseHas('posts', [
            'company_id' => $company->id,
            'user_id' => $recruiter->id,
            'title' => 'New Engineering Roles',
        ]);
    });

    it('forbids a candidate from creating a company post', function () {
        $owner = $this->createUser(['email' => 'owner@example.com']);
        $company = $owner->ownedCompanies()->create([
            'slug' => 'candidate-company',
            'name' => 'Candidate Company',
        ]);

        $candidate = $this->createUserWithRole('Candidate', [
            'email' => 'candidate@example.com',
        ]);

        $response = $this->actingAs($candidate)->postJson('/api/posts', [
            'company_id' => $company->id,
            'title' => 'Blocked Company Post',
            'content' => 'Should not be allowed.',
            'category' => PostCategory::GENERAL->value,
        ]);

        $response->assertStatus(403);
    });
});
