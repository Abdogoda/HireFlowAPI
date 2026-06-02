<?php

use App\Enums\SocialProfileType;

beforeEach(function () {
    $this->createDefaultRoles();
});

describe('List Company Social Profiles', function () {
    it('returns all social profiles for a company', function () {
        $owner = $this->createUser(['email' => 'owner@example.com']);

        $company = $owner->ownedCompanies()->create([
            'name' => 'Acme Ltd',
            'slug' => 'acme-ltd',
        ]);

        $company->socialProfiles()->createMany([
            ['social_profile_type' => SocialProfileType::LINKEDIN->value, 'profile_url' => 'https://linkedin.com/company/acme'],
            ['social_profile_type' => SocialProfileType::GITHUB->value,   'profile_url' => 'https://github.com/acme'],
        ]);

        $response = $this->actingAs($owner)
            ->getJson("/api/companies/{$company->id}/social-profiles");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'social_profiles' => [
                        '*' => ['id', 'social_profile_type', 'profile_url'],
                    ],
                ],
            ])
            ->assertJsonCount(2, 'data.social_profiles');
    });

    it('returns empty array when company has no social profiles', function () {
        $owner = $this->createUser();

        $company = $owner->ownedCompanies()->create([
            'name' => 'Acme Ltd',
            'slug' => 'acme-ltd',
        ]);

        $response = $this->actingAs($owner)
            ->getJson("/api/companies/{$company->id}/social-profiles");

        $response->assertStatus(200)
            ->assertJson(['data' => ['social_profiles' => []]]);
    });

    it('allows any authenticated user to list a company social profiles', function () {
        $owner  = $this->createUser(['email' => 'owner@example.com']);
        $viewer = $this->createUser(['email' => 'viewer@example.com']);

        $company = $owner->ownedCompanies()->create([
            'name' => 'Acme Ltd',
            'slug' => 'acme-ltd',
        ]);

        $company->socialProfiles()->create([
            'social_profile_type' => SocialProfileType::TWITTER->value,
            'profile_url'         => 'https://twitter.com/acme',
        ]);

        $response = $this->actingAs($viewer)
            ->getJson("/api/companies/{$company->id}/social-profiles");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.social_profiles');
    });

    it('fails without authentication', function () {
        $owner   = $this->createUser();
        $company = $owner->ownedCompanies()->create(['name' => 'Acme Ltd', 'slug' => 'acme-ltd']);

        $this->getJson("/api/companies/{$company->id}/social-profiles")
            ->assertStatus(401);
    });
});
