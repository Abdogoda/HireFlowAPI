<?php

use App\Enums\SocialProfileType;

beforeEach(function () {
        $this->createDefaultRoles();
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
