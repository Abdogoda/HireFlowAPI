<?php

use App\Enums\SocialProfileType;

beforeEach(function () {
        $this->createDefaultRoles();
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
