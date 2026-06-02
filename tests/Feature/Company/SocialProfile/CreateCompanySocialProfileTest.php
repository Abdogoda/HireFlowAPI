<?php

use App\Enums\Authorization\CompanyRoles;
use App\Enums\Company\SocialProfileType;

beforeEach(function () {
    $this->createDefaultRoles();
});

describe('Create Company Social Profile', function () {
    it('creates a social profile for a company as the owner', function () {
        $owner = $this->createUser();

        $company = $owner->ownedCompanies()->create([
            'name' => 'Acme Ltd',
            'slug' => 'acme-ltd',
        ]);

        $response = $this->actingAs($owner)
            ->postJson("/api/companies/{$company->id}/social-profiles", [
                'social_profile_type' => SocialProfileType::LINKEDIN->value,
                'profile_url'         => 'https://linkedin.com/company/acme',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'social_profile' => ['id', 'social_profile_type', 'profile_url'],
                ],
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Company social profile created successfully',
                'data'    => [
                    'social_profile' => [
                        'social_profile_type' => SocialProfileType::LINKEDIN->value,
                        'profile_url'         => 'https://linkedin.com/company/acme',
                    ],
                ],
            ]);

        $this->assertDatabaseHas('company_social_profiles', [
            'company_id'          => $company->id,
            'social_profile_type' => SocialProfileType::LINKEDIN->value,
            'profile_url'         => 'https://linkedin.com/company/acme',
        ]);
    });

    it('allows an admin member to create a social profile', function () {
        $owner = $this->createUser(['email' => 'owner@example.com']);
        $admin = $this->createUser(['email' => 'admin@example.com']);

        $company = $owner->ownedCompanies()->create([
            'name' => 'Acme Ltd',
            'slug' => 'acme-ltd',
        ]);

        $company->memberships()->create([
            'member_id'          => $admin->id,
            'company_role'       => CompanyRoles::ADMIN->value,
            'position'           => 'Operations Lead',
            'start_date'         => now()->toDateString(),
            'is_current_position' => true,
        ]);

        $response = $this->actingAs($admin)
            ->postJson("/api/companies/{$company->id}/social-profiles", [
                'social_profile_type' => SocialProfileType::GITHUB->value,
                'profile_url'         => 'https://github.com/acme',
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('company_social_profiles', [
            'company_id'          => $company->id,
            'social_profile_type' => SocialProfileType::GITHUB->value,
        ]);
    });

    it('creates multiple social profiles with different types', function () {
        $owner = $this->createUser();

        $company = $owner->ownedCompanies()->create([
            'name' => 'Acme Ltd',
            'slug' => 'acme-ltd',
        ]);

        $this->actingAs($owner)
            ->postJson("/api/companies/{$company->id}/social-profiles", [
                'social_profile_type' => SocialProfileType::LINKEDIN->value,
                'profile_url'         => 'https://linkedin.com/company/acme',
            ])->assertStatus(201);

        $this->actingAs($owner)
            ->postJson("/api/companies/{$company->id}/social-profiles", [
                'social_profile_type' => SocialProfileType::TWITTER->value,
                'profile_url'         => 'https://twitter.com/acme',
            ])->assertStatus(201);

        $this->assertDatabaseCount('company_social_profiles', 2);
    });

    it('prevents a non-owner/non-admin member from creating a social profile', function () {
        $owner    = $this->createUser(['email' => 'owner@example.com']);
        $employee = $this->createUser(['email' => 'employee@example.com']);

        $company = $owner->ownedCompanies()->create([
            'name' => 'Acme Ltd',
            'slug' => 'acme-ltd',
        ]);

        $company->memberships()->create([
            'member_id'          => $employee->id,
            'company_role'       => CompanyRoles::EMPLOYEE->value,
            'position'           => 'Developer',
            'start_date'         => now()->toDateString(),
            'is_current_position' => true,
        ]);

        $response = $this->actingAs($employee)
            ->postJson("/api/companies/{$company->id}/social-profiles", [
                'social_profile_type' => SocialProfileType::LINKEDIN->value,
                'profile_url'         => 'https://linkedin.com/company/acme',
            ]);

        $response->assertStatus(403);
    });

    it('fails without authentication', function () {
        $owner   = $this->createUser();
        $company = $owner->ownedCompanies()->create(['name' => 'Acme Ltd', 'slug' => 'acme-ltd']);

        $this->postJson("/api/companies/{$company->id}/social-profiles", [
            'social_profile_type' => SocialProfileType::LINKEDIN->value,
            'profile_url'         => 'https://linkedin.com/company/acme',
        ])->assertStatus(401);
    });

    it('fails with missing required fields', function () {
        $owner   = $this->createUser();
        $company = $owner->ownedCompanies()->create(['name' => 'Acme Ltd', 'slug' => 'acme-ltd']);

        $this->actingAs($owner)
            ->postJson("/api/companies/{$company->id}/social-profiles", [
                'social_profile_type' => SocialProfileType::LINKEDIN->value,
                // missing profile_url
            ])->assertStatus(422);
    });

    it('fails with invalid social profile type', function () {
        $owner   = $this->createUser();
        $company = $owner->ownedCompanies()->create(['name' => 'Acme Ltd', 'slug' => 'acme-ltd']);

        $this->actingAs($owner)
            ->postJson("/api/companies/{$company->id}/social-profiles", [
                'social_profile_type' => 999,
                'profile_url'         => 'https://example.com',
            ])->assertStatus(422);
    });

    it('fails with an invalid URL', function () {
        $owner   = $this->createUser();
        $company = $owner->ownedCompanies()->create(['name' => 'Acme Ltd', 'slug' => 'acme-ltd']);

        $this->actingAs($owner)
            ->postJson("/api/companies/{$company->id}/social-profiles", [
                'social_profile_type' => SocialProfileType::LINKEDIN->value,
                'profile_url'         => 'not-a-valid-url',
            ])->assertStatus(422);
    });
});
