<?php

use App\Models\User;
use App\Models\Role;
use App\Enums\Users\SocialProfileType;

beforeEach(function () {
        $this->createDefaultRoles();
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
