<?php

use App\Models\User;
use App\Models\Role;
use App\Enums\Users\SocialProfileType;

beforeEach(function () {
        $this->createDefaultRoles();
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
