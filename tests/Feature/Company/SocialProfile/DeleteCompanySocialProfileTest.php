<?php

use App\Enums\Authorization\CompanyRoles;
use App\Enums\Company\SocialProfileType;

beforeEach(function () {
    $this->createDefaultRoles();
});

describe('Delete Company Social Profile', function () {
    it('deletes a social profile as the owner', function () {
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
            ->deleteJson("/api/companies/{$company->id}/social-profiles/{$socialProfile->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Company social profile deleted successfully',
            ]);

        $this->assertDatabaseMissing('company_social_profiles', [
            'id' => $socialProfile->id,
        ]);
    });

    it('allows an admin member to delete a social profile', function () {
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
            'social_profile_type' => SocialProfileType::GITHUB->value,
            'profile_url'         => 'https://github.com/acme',
        ]);

        $this->actingAs($admin)
            ->deleteJson("/api/companies/{$company->id}/social-profiles/{$socialProfile->id}")
            ->assertStatus(200);

        $this->assertDatabaseMissing('company_social_profiles', [
            'id' => $socialProfile->id,
        ]);
    });

    it('prevents a non-owner/non-admin from deleting a social profile', function () {
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
            ->deleteJson("/api/companies/{$company->id}/social-profiles/{$socialProfile->id}")
            ->assertStatus(403);

        $this->assertDatabaseHas('company_social_profiles', [
            'id' => $socialProfile->id,
        ]);
    });

    it('returns 403 when deleting a social profile belonging to a different company', function () {
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

        // Owner of Acme tries to delete Other Co's social profile via Acme's route
        $this->actingAs($owner)
            ->deleteJson("/api/companies/{$company->id}/social-profiles/{$socialProfile->id}")
            ->assertStatus(403);

        $this->assertDatabaseHas('company_social_profiles', [
            'id' => $socialProfile->id,
        ]);
    });

    it('fails without authentication', function () {
        $owner   = $this->createUser();
        $company = $owner->ownedCompanies()->create(['name' => 'Acme Ltd', 'slug' => 'acme-ltd']);

        $socialProfile = $company->socialProfiles()->create([
            'social_profile_type' => SocialProfileType::LINKEDIN->value,
            'profile_url'         => 'https://linkedin.com/company/acme',
        ]);

        $this->deleteJson("/api/companies/{$company->id}/social-profiles/{$socialProfile->id}")
            ->assertStatus(401);
    });
});
