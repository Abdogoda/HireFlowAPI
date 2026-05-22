<?php

use App\Models\User;
use App\Models\Role;
use App\Enums\Users\SocialProfileType;

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
