<?php

use App\Models\User;
use App\Models\Role;
use App\Enums\Users\SocialProfileType;

describe('Social Profile Endpoints', function () {
    beforeEach(function () {
        $this->createDefaultRoles();
    });

    describe('List Social Profiles', function () {
        it('retrieves all social profiles for authenticated user', function () {
            $user = $this->createUser();

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
            $user = $this->createUser();
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
            $user = $this->createUser();

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
            $user = $this->createUser();
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
            $user = $this->createUser();
            $response = $this->actingAs($user)
                ->postJson('/api/profile/social-profiles', [
                    'social_profile_type' => SocialProfileType::LINKEDIN->value,
                ]);

            $response->assertStatus(422);
        });

        it('fails with invalid social profile type', function () {
            $user = $this->createUser();
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
            $user = $this->createUser();
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
            $user1 = $this->createUser(['email' => 'user1@example.com']);
            $user2 = $this->createUser(['email' => 'user2@example.com']);

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
            $user = $this->createUser();

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
            $user = $this->createUser();
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
            $user = $this->createUser();
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
            $user1 = $this->createUser(['email' => 'user1@example.com']);
            $user2 = $this->createUser(['email' => 'user2@example.com']);

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
            $user = $this->createUser();
            
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
            $user = $this->createUser();
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
            $user1 = $this->createUser(['email' => 'user1@example.com']);
            $user2 = $this->createUser(['email' => 'user2@example.com']);

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
            $user = $this->createUser();
            
            $socialProfile = $user->socialProfiles()->create([
                'social_profile_type' => SocialProfileType::LINKEDIN->value,
                'profile_url' => 'https://linkedin.com/in/test',
            ]);

            $response = $this->deleteJson("/api/profile/social-profiles/{$socialProfile->id}");

            $response->assertStatus(401);
        });
    });
});