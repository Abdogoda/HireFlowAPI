<?php

use App\Enums\SocialProfileType;

beforeEach(function () {
    $this->createDefaultRoles();
});

describe('Get Single Company Social Profile', function () {
    it('retrieves a specific company social profile', function () {
        $owner = $this->createUser();

        $company = $owner->ownedCompanies()->create([
            'name' => 'Acme Ltd',
            'slug' => 'acme-ltd',
        ]);

        $socialProfile = $company->socialProfiles()->create([
            'social_profile_type' => SocialProfileType::LINKEDIN->value,
            'profile_url'         => 'https://linkedin.com/company/acme',
        ]);

        $response = $this->actingAs($owner)
            ->getJson("/api/companies/{$company->id}/social-profiles/{$socialProfile->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'social_profile' => ['id', 'social_profile_type', 'profile_url'],
                ],
            ])
            ->assertJson([
                'data' => [
                    'social_profile' => [
                        'id'          => $socialProfile->id,
                        'profile_url' => 'https://linkedin.com/company/acme',
                    ],
                ],
            ]);
    });

    it('allows any authenticated user to view a single company social profile', function () {
        $owner  = $this->createUser(['email' => 'owner@example.com']);
        $viewer = $this->createUser(['email' => 'viewer@example.com']);

        $company = $owner->ownedCompanies()->create([
            'name' => 'Acme Ltd',
            'slug' => 'acme-ltd',
        ]);

        $socialProfile = $company->socialProfiles()->create([
            'social_profile_type' => SocialProfileType::GITHUB->value,
            'profile_url'         => 'https://github.com/acme',
        ]);

        $this->actingAs($viewer)
            ->getJson("/api/companies/{$company->id}/social-profiles/{$socialProfile->id}")
            ->assertStatus(200)
            ->assertJsonPath('data.social_profile.id', $socialProfile->id);
    });

    it('returns 403 when social profile belongs to a different company', function () {
        $owner    = $this->createUser(['email' => 'owner@example.com']);
        $otherOwner = $this->createUser(['email' => 'other@example.com']);

        $company = $owner->ownedCompanies()->create([
            'name' => 'Acme Ltd',
            'slug' => 'acme-ltd',
        ]);

        $otherCompany = $otherOwner->ownedCompanies()->create([
            'name' => 'Other Co',
            'slug' => 'other-co',
        ]);

        $socialProfile = $otherCompany->socialProfiles()->create([
            'social_profile_type' => SocialProfileType::LINKEDIN->value,
            'profile_url'         => 'https://linkedin.com/company/other',
        ]);

        // Try to access other company's social profile through acme's route
        $this->actingAs($owner)
            ->getJson("/api/companies/{$company->id}/social-profiles/{$socialProfile->id}")
            ->assertStatus(403);
    });

    it('fails without authentication', function () {
        $owner = $this->createUser();

        $company = $owner->ownedCompanies()->create([
            'name' => 'Acme Ltd',
            'slug' => 'acme-ltd',
        ]);

        $socialProfile = $company->socialProfiles()->create([
            'social_profile_type' => SocialProfileType::LINKEDIN->value,
            'profile_url'         => 'https://linkedin.com/company/acme',
        ]);

        $this->getJson("/api/companies/{$company->id}/social-profiles/{$socialProfile->id}")
            ->assertStatus(401);
    });
});
