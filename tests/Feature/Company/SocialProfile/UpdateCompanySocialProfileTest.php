<?php

use App\Enums\Company\CompanyRoles;
use App\Enums\SocialProfileType;

beforeEach(function () {
    $this->createDefaultRoles();
});

describe('Update Company Social Profile', function () {
    it('updates a social profile URL as the owner', function () {
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
            ->patchJson("/api/companies/{$company->id}/social-profiles/{$socialProfile->id}", [
                'profile_url' => 'https://linkedin.com/company/acme-updated',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Company social profile updated successfully',
                'data'    => [
                    'social_profile' => [
                        'profile_url' => 'https://linkedin.com/company/acme-updated',
                    ],
                ],
            ]);

        $this->assertDatabaseHas('company_social_profiles', [
            'id'          => $socialProfile->id,
            'profile_url' => 'https://linkedin.com/company/acme-updated',
        ]);
    });

    it('updates the social profile type', function () {
        $owner = $this->createUser();

        $company = $owner->ownedCompanies()->create([
            'name' => 'Acme Ltd',
            'slug' => 'acme-ltd',
        ]);

        $socialProfile = $company->socialProfiles()->create([
            'social_profile_type' => SocialProfileType::LINKEDIN->value,
            'profile_url'         => 'https://linkedin.com/company/acme',
        ]);

        $this->actingAs($owner)
            ->patchJson("/api/companies/{$company->id}/social-profiles/{$socialProfile->id}", [
                'social_profile_type' => SocialProfileType::GITHUB->value,
                'profile_url'         => 'https://github.com/acme',
            ])->assertStatus(200);

        $this->assertDatabaseHas('company_social_profiles', [
            'id'                  => $socialProfile->id,
            'social_profile_type' => SocialProfileType::GITHUB->value,
            'profile_url'         => 'https://github.com/acme',
        ]);
    });

    it('allows an admin member to update a social profile', function () {
        $owner = $this->createUser(['email' => 'owner@example.com']);
        $admin = $this->createUser(['email' => 'admin@example.com']);

        $company = $owner->ownedCompanies()->create([
            'name' => 'Acme Ltd',
            'slug' => 'acme-ltd',
        ]);

        $company->memberships()->create([
            'member_id'           => $admin->id,
            'company_role'        => CompanyRoles::ADMIN->value,
            'position'            => 'Operations Lead',
            'start_date'          => now()->toDateString(),
            'is_current_position' => true,
        ]);

        $socialProfile = $company->socialProfiles()->create([
            'social_profile_type' => SocialProfileType::TWITTER->value,
            'profile_url'         => 'https://twitter.com/acme',
        ]);

        $this->actingAs($admin)
            ->patchJson("/api/companies/{$company->id}/social-profiles/{$socialProfile->id}", [
                'profile_url' => 'https://twitter.com/acme-official',
            ])->assertStatus(200);

        $this->assertDatabaseHas('company_social_profiles', [
            'id'          => $socialProfile->id,
            'profile_url' => 'https://twitter.com/acme-official',
        ]);
    });

    it('prevents a non-owner/non-admin from updating a social profile', function () {
        $owner    = $this->createUser(['email' => 'owner@example.com']);
        $employee = $this->createUser(['email' => 'employee@example.com']);

        $company = $owner->ownedCompanies()->create([
            'name' => 'Acme Ltd',
            'slug' => 'acme-ltd',
        ]);

        $company->memberships()->create([
            'member_id'           => $employee->id,
            'company_role'        => CompanyRoles::EMPLOYEE->value,
            'position'            => 'Developer',
            'start_date'          => now()->toDateString(),
            'is_current_position' => true,
        ]);

        $socialProfile = $company->socialProfiles()->create([
            'social_profile_type' => SocialProfileType::LINKEDIN->value,
            'profile_url'         => 'https://linkedin.com/company/acme',
        ]);

        $this->actingAs($employee)
            ->patchJson("/api/companies/{$company->id}/social-profiles/{$socialProfile->id}", [
                'profile_url' => 'https://linkedin.com/company/hacked',
            ])->assertStatus(403);

        $this->assertDatabaseHas('company_social_profiles', [
            'id'          => $socialProfile->id,
            'profile_url' => 'https://linkedin.com/company/acme',
        ]);
    });

    it('returns 403 when updating a social profile belonging to a different company', function () {
        $owner      = $this->createUser(['email' => 'owner@example.com']);
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

        $this->actingAs($owner)
            ->patchJson("/api/companies/{$company->id}/social-profiles/{$socialProfile->id}", [
                'profile_url' => 'https://linkedin.com/company/hijacked',
            ])->assertStatus(403);
    });

    it('fails without authentication', function () {
        $owner   = $this->createUser();
        $company = $owner->ownedCompanies()->create(['name' => 'Acme Ltd', 'slug' => 'acme-ltd']);

        $socialProfile = $company->socialProfiles()->create([
            'social_profile_type' => SocialProfileType::LINKEDIN->value,
            'profile_url'         => 'https://linkedin.com/company/acme',
        ]);

        $this->patchJson("/api/companies/{$company->id}/social-profiles/{$socialProfile->id}", [
            'profile_url' => 'https://example.com',
        ])->assertStatus(401);
    });

    it('fails with an invalid URL', function () {
        $owner   = $this->createUser();
        $company = $owner->ownedCompanies()->create(['name' => 'Acme Ltd', 'slug' => 'acme-ltd']);

        $socialProfile = $company->socialProfiles()->create([
            'social_profile_type' => SocialProfileType::LINKEDIN->value,
            'profile_url'         => 'https://linkedin.com/company/acme',
        ]);

        $this->actingAs($owner)
            ->patchJson("/api/companies/{$company->id}/social-profiles/{$socialProfile->id}", [
                'profile_url' => 'not-a-url',
            ])->assertStatus(422);
    });
});
