<?php

use App\Models\User;
use App\Models\Role;
use App\Enums\Users\SocialProfileType;

describe('Social Profile Endpoints', function () {
    beforeEach(function () {
        Role::create(['name' => 'Admin', 'description' => 'Administrator']);
        Role::create(['name' => 'Recruiter', 'description' => 'Recruiter']);
        Role::create(['name' => 'Candidate', 'description' => 'Candidate']);
    });

    describe('List Social Profiles', function () {
        it('retrieves all social profiles for authenticated user', function () {
            $role = Role::where('name', 'Candidate')->first();
            $user = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
                'email_verified_at' => now(),
            ]);

            $user->socialProfiles()->createMany([
                ['social_profile_type' => SocialProfileType::LINKEDIN->value, 'profile_url' => 'https://linkedin.com/in/johndoe'],
                ['social_profile_type' => SocialProfileType::GITHUB->value, 'profile_url' => 'https://github.com/johndoe'],
            ]);

            $response = $this->actingAs($user)
                ->getJson('/api/profile/social-profiles');

            $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => ['social_profiles' => [
                        '*' => ['id', 'social_profile_type', 'profile_url'],
                    ]],
                ])
                ->assertJsonCount(2, 'data.social_profiles');
        });

        it('returns empty array when user has no social profiles', function () {
            $role = Role::where('name', 'Candidate')->first();
            $user = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
            ]);

            $response = $this->actingAs($user)
                ->getJson('/api/profile/social-profiles');

            $response->assertStatus(200)
                ->assertJson(['data' => ['social_profiles' => []]]);
        });

        it('fails without authentication', function () {
            $response = $this->getJson('/api/profile/social-profiles');
            $response->assertStatus(401);
        });
    });

    describe('Create Social Profile', function () {
        it('creates a social profile successfully', function () {
            $role = Role::where('name', 'Candidate')->first();
            $user = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
                'email_verified_at' => now(),
            ]);

            $response = $this->actingAs($user)
                ->postJson('/api/profile/social-profiles', [
                    'social_profile_type' => SocialProfileType::LINKEDIN->value,
                    'profile_url' => 'https://linkedin.com/in/johndoe',
                ]);

            $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'social_profile' => [
                            'id',
                            'social_profile_type',
                            'profile_url',
                        ],
                    ],
                ])
                ->assertJson([
                    'success' => true,
                    'message' => 'Social profile created successfully',
                ]);

            $this->assertDatabaseHas('social_profiles', [
                'user_id' => $user->id,
                'social_profile_type' => SocialProfileType::LINKEDIN->value,
                'profile_url' => 'https://linkedin.com/in/johndoe',
            ]);
        });

        it('creates multiple social profiles for same user', function () {
            $role = Role::where('name', 'Candidate')->first();
            $user = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
            ]);

            $this->actingAs($user)
                ->postJson('/api/profile/social-profiles', [
                    'social_profile_type' => SocialProfileType::LINKEDIN->value,
                    'profile_url' => 'https://linkedin.com/in/johndoe',
                ])
                ->assertStatus(201);

            $this->actingAs($user)
                ->postJson('/api/profile/social-profiles', [
                    'social_profile_type' => SocialProfileType::GITHUB->value,
                    'profile_url' => 'https://github.com/johndoe',
                ])
                ->assertStatus(201);

            $this->assertDatabaseCount('social_profiles', 2);
        });

        it('fails without authentication', function () {
            $response = $this->postJson('/api/profile/social-profiles', [
                'social_profile_type' => SocialProfileType::LINKEDIN->value,
                'profile_url' => 'https://linkedin.com/in/johndoe',
            ]);

            $response->assertStatus(401);
        });

        it('fails with missing required fields', function () {
            $role = Role::where('name', 'Candidate')->first();
            $user = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
            ]);

            $response = $this->actingAs($user)
                ->postJson('/api/profile/social-profiles', [
                    'social_profile_type' => SocialProfileType::LINKEDIN->value,
                ]);

            $response->assertStatus(422);
        });

        it('fails with invalid social profile type', function () {
            $role = Role::where('name', 'Candidate')->first();
            $user = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
            ]);

            $response = $this->actingAs($user)
                ->postJson('/api/profile/social-profiles', [
                    'social_profile_type' => 999,
                    'profile_url' => 'https://example.com',
                ]);

            $response->assertStatus(422);
        });
    });

    describe('Get Single Social Profile', function () {
        it('retrieves a specific social profile', function () {
            $role = Role::where('name', 'Candidate')->first();
            $user = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
            ]);

            $socialProfile = $user->socialProfiles()->create([
                'social_profile_type' => SocialProfileType::LINKEDIN->value,
                'profile_url' => 'https://linkedin.com/in/johndoe',
            ]);

            $response = $this->actingAs($user)
                ->getJson("/api/profile/social-profiles/{$socialProfile->id}");

            $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'social_profile' => [
                            'id',
                            'social_profile_type',
                            'profile_url',
                        ],
                    ],
                ])
                ->assertJson([
                    'data' => [
                        'social_profile' => [
                            'id' => $socialProfile->id,
                        ],
                    ],
                ]);
        });

        it('fails when accessing another user\'s social profile', function () {
            $role = Role::where('name', 'Candidate')->first();
            $user1 = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
            ]);

            $user2 = User::create([
                'name' => 'Jane Doe',
                'email' => 'jane@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
            ]);

            $socialProfile = $user1->socialProfiles()->create([
                'social_profile_type' => SocialProfileType::LINKEDIN->value,
                'profile_url' => 'https://linkedin.com/in/johndoe',
            ]);

            $response = $this->actingAs($user2)
                ->getJson("/api/profile/social-profiles/{$socialProfile->id}");

            $response->assertStatus(403)
                ->assertJson(['message' => 'You do not have permission to view this social profile']);
        });

        it('fails without authentication', function () {
            $role = Role::where('name', 'Candidate')->first();
            $user = User::create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
            ]);
            $socialProfile = $user->socialProfiles()->create([
                'social_profile_type' => SocialProfileType::LINKEDIN->value,
                'profile_url' => 'https://linkedin.com/in/test',
            ]);

            $response = $this->getJson("/api/profile/social-profiles/{$socialProfile->id}");

            $response->assertStatus(401);
        });
    });

    describe('Update Social Profile', function () {
        it('updates a social profile successfully', function () {
            $role = Role::where('name', 'Candidate')->first();
            $user = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
            ]);

            $socialProfile = $user->socialProfiles()->create([
                'social_profile_type' => SocialProfileType::LINKEDIN->value,
                'profile_url' => 'https://linkedin.com/in/johndoe',
            ]);

            $response = $this->actingAs($user)
                ->patchJson("/api/profile/social-profiles/{$socialProfile->id}", [
                    'profile_url' => 'https://linkedin.com/in/john-doe-updated',
                ]);

            $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Social profile updated successfully',
                ]);

            $this->assertDatabaseHas('social_profiles', [
                'id' => $socialProfile->id,
                'profile_url' => 'https://linkedin.com/in/john-doe-updated',
            ]);
        });

        it('updates social profile type', function () {
            $role = Role::where('name', 'Candidate')->first();
            $user = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
            ]);

            $socialProfile = $user->socialProfiles()->create([
                'social_profile_type' => SocialProfileType::LINKEDIN->value,
                'profile_url' => 'https://linkedin.com/in/johndoe',
            ]);

            $response = $this->actingAs($user)
                ->patchJson("/api/profile/social-profiles/{$socialProfile->id}", [
                    'social_profile_type' => SocialProfileType::GITHUB->value,
                    'profile_url' => 'https://github.com/johndoe',
                ]);

            $response->assertStatus(200);

            $this->assertDatabaseHas('social_profiles', [
                'id' => $socialProfile->id,
                'social_profile_type' => SocialProfileType::GITHUB->value,
                'profile_url' => 'https://github.com/johndoe',
            ]);
        });

        it('fails when updating another user\'s social profile', function () {
            $role = Role::where('name', 'Candidate')->first();
            $user1 = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
            ]);

            $user2 = User::create([
                'name' => 'Jane Doe',
                'email' => 'jane@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
            ]);

            $socialProfile = $user1->socialProfiles()->create([
                'social_profile_type' => SocialProfileType::LINKEDIN->value,
                'profile_url' => 'https://linkedin.com/in/johndoe',
            ]);

            $response = $this->actingAs($user2)
                ->patchJson("/api/profile/social-profiles/{$socialProfile->id}", [
                    'profile_url' => 'https://linkedin.com/in/hacked',
                ]);

            $response->assertStatus(403)
                ->assertJson(['message' => 'You do not have permission to update this social profile']);
        });

        it('fails without authentication', function () {
            $role = Role::where('name', 'Candidate')->first();
            $user = User::create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
            ]);
            $socialProfile = $user->socialProfiles()->create([
                'social_profile_type' => SocialProfileType::LINKEDIN->value,
                'profile_url' => 'https://linkedin.com/in/test',
            ]);

            $response = $this->patchJson("/api/profile/social-profiles/{$socialProfile->id}", [
                'profile_url' => 'https://example.com',
            ]);

            $response->assertStatus(401);
        });
    });

    describe('Delete Social Profile', function () {
        it('deletes a social profile successfully', function () {
            $role = Role::where('name', 'Candidate')->first();
            $user = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
            ]);

            $socialProfile = $user->socialProfiles()->create([
                'social_profile_type' => SocialProfileType::LINKEDIN->value,
                'profile_url' => 'https://linkedin.com/in/johndoe',
            ]);

            $response = $this->actingAs($user)
                ->deleteJson("/api/profile/social-profiles/{$socialProfile->id}");

            $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Social profile deleted successfully',
                ]);

            $this->assertDatabaseMissing('social_profiles', [
                'id' => $socialProfile->id,
            ]);
        });

        it('fails when deleting another user\'s social profile', function () {
            $role = Role::where('name', 'Candidate')->first();
            $user1 = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
            ]);

            $user2 = User::create([
                'name' => 'Jane Doe',
                'email' => 'jane@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
            ]);

            $socialProfile = $user1->socialProfiles()->create([
                'social_profile_type' => SocialProfileType::LINKEDIN->value,
                'profile_url' => 'https://linkedin.com/in/johndoe',
            ]);

            $response = $this->actingAs($user2)
                ->deleteJson("/api/profile/social-profiles/{$socialProfile->id}");

            $response->assertStatus(403);

            $this->assertDatabaseHas('social_profiles', [
                'id' => $socialProfile->id,
            ]);
        });

        it('fails without authentication', function () {
            $role = Role::where('name', 'Candidate')->first();
            $user = User::create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
            ]);
            $socialProfile = $user->socialProfiles()->create([
                'social_profile_type' => SocialProfileType::LINKEDIN->value,
                'profile_url' => 'https://linkedin.com/in/test',
            ]);

            $response = $this->deleteJson("/api/profile/social-profiles/{$socialProfile->id}");

            $response->assertStatus(401);
        });
    });
});
