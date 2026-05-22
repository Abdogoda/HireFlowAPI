<?php

use App\Enums\Authorization\CompanyRoles;
use App\Notifications\CompanyMemberAddedNotification;
use App\Notifications\CompanyMemberRemovedNotification;
use Illuminate\Support\Facades\Notification;

describe('Company Member Endpoints', function () {
    beforeEach(function () {
        $this->createDefaultRoles();
    });

    it('adds a person with position, role, and information to a company', function () {
        Notification::fake();

        $owner = $this->createUser(['email' => 'owner@example.com']);
        $person = $this->createUser(['email' => 'person@example.com']);

        $company = $owner->ownedCompanies()->create([
            'name' => 'Acme Ltd',
            'description' => 'A sample company',
            'slug' => 'acme-ltd',
            'is_verified' => true,
        ]);

        $company->memberships()->create([
            'user_id' => $owner->id,
            'company_role' => CompanyRoles::OWNER->value,
            'position' => 'Company Owner',
            'start_date' => now()->toDateString(),
            'is_current_position' => true,
        ]);

        $response = $this->actingAs($owner)
            ->postJson("/api/companies/{$company->id}/people", [
                'user_id' => $person->id,
                'company_role' => CompanyRoles::RECRUITER->value,
                'position' => 'Developer',
                'information' => 'Works on backend features',
                'start_date' => '2026-01-01',
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'person' => [
                        'company_role' => 'recruiter',
                        'position' => 'Developer',
                        'information' => 'Works on backend features',
                    ],
                ],
            ]);

        $this->assertDatabaseHas('company_memberships', [
            'company_id' => $company->id,
            'user_id' => $person->id,
            'company_role' => 'recruiter',
            'position' => 'Developer',
            'is_current_position' => 1,
        ]);

        Notification::assertSentTo($person, CompanyMemberAddedNotification::class);
    });

    it('updates a company member membership record', function () {
        $owner = $this->createUser(['email' => 'owner@example.com']);
        $person = $this->createUser(['email' => 'person@example.com']);

        $company = $owner->ownedCompanies()->create([
            'name' => 'Acme Ltd',
            'slug' => 'acme-ltd',
        ]);

        $company->memberships()->create([
            'user_id' => $owner->id,
            'company_role' => CompanyRoles::OWNER->value,
            'position' => 'Company Owner',
            'start_date' => now()->toDateString(),
            'is_current_position' => true,
        ]);

        $membership = $company->memberships()->create([
            'user_id' => $person->id,
            'company_role' => CompanyRoles::CANDIDATE->value,
            'position' => 'Junior Developer',
            'start_date' => '2026-01-01',
            'is_current_position' => true,
        ]);

        $response = $this->actingAs($owner)
            ->patchJson("/api/companies/{$company->id}/people/{$membership->id}", [
                'company_role' => CompanyRoles::RECRUITER->value,
                'position' => 'Senior Developer',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'person' => [
                        'company_role' => 'recruiter',
                        'position' => 'Senior Developer',
                    ],
                ],
            ]);

        $this->assertDatabaseHas('company_memberships', [
            'id' => $membership->id,
            'company_role' => 'recruiter',
            'position' => 'Senior Developer',
        ]);
    });

    it('removes a company member by archiving the membership', function () {
        Notification::fake();

        $owner = $this->createUser(['email' => 'owner@example.com']);
        $person = $this->createUser(['email' => 'person@example.com']);

        $company = $owner->ownedCompanies()->create([
            'name' => 'Acme Ltd',
            'slug' => 'acme-ltd',
        ]);

        $company->memberships()->create([
            'user_id' => $owner->id,
            'company_role' => CompanyRoles::OWNER->value,
            'position' => 'Company Owner',
            'start_date' => now()->toDateString(),
            'is_current_position' => true,
        ]);

        $membership = $company->memberships()->create([
            'user_id' => $person->id,
            'company_role' => CompanyRoles::CANDIDATE->value,
            'position' => 'Analyst',
            'start_date' => '2026-01-01',
            'is_current_position' => true,
        ]);

        $response = $this->actingAs($owner)
            ->deleteJson("/api/companies/{$company->id}/people/{$membership->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Person removed from company successfully',
            ]);

        $this->assertDatabaseHas('company_memberships', [
            'id' => $membership->id,
            'is_current_position' => 0,
        ]);

        Notification::assertSentTo($person, CompanyMemberRemovedNotification::class);
    });

    it('prevents a company member without admin role from managing company people', function () {
        $owner = $this->createUser(['email' => 'owner@example.com']);
        $member = $this->createUser(['email' => 'member@example.com']);
        $target = $this->createUser(['email' => 'target@example.com']);

        $company = $owner->ownedCompanies()->create([
            'name' => 'Acme Ltd',
            'slug' => 'acme-ltd',
        ]);

        $company->memberships()->create([
            'user_id' => $owner->id,
            'company_role' => CompanyRoles::OWNER->value,
            'position' => 'Company Owner',
            'start_date' => now()->toDateString(),
            'is_current_position' => true,
        ]);

        $company->memberships()->create([
            'user_id' => $member->id,
            'company_role' => CompanyRoles::CANDIDATE->value,
            'position' => 'Analyst',
            'start_date' => now()->toDateString(),
            'is_current_position' => true,
        ]);

        $response = $this->actingAs($member)
            ->postJson("/api/companies/{$company->id}/people", [
                'user_id' => $target->id,
                'company_role' => CompanyRoles::RECRUITER->value,
                'position' => 'Recruiter',
                'start_date' => '2026-05-01',
            ]);

        $response->assertStatus(403);
    });

    it('archives the previous current position when a user gets a new one in the same company', function () {
        $owner = $this->createUser(['email' => 'owner@example.com']);
        $person = $this->createUser(['email' => 'person@example.com']);

        $company = $owner->ownedCompanies()->create([
            'name' => 'Acme Ltd',
            'slug' => 'acme-ltd',
        ]);

        $company->memberships()->create([
            'user_id' => $owner->id,
            'company_role' => CompanyRoles::OWNER->value,
            'position' => 'Company Owner',
            'start_date' => now()->toDateString(),
            'is_current_position' => true,
        ]);

        $first = $company->memberships()->create([
            'user_id' => $person->id,
            'company_role' => CompanyRoles::CANDIDATE->value,
            'position' => 'Junior Developer',
            'start_date' => '2026-01-01',
            'is_current_position' => true,
        ]);

        $this->actingAs($owner)
            ->postJson("/api/companies/{$company->id}/people", [
                'user_id' => $person->id,
                'company_role' => CompanyRoles::RECRUITER->value,
                'position' => 'Senior Developer',
                'start_date' => '2026-05-01',
                'is_current_position' => true,
            ])
            ->assertStatus(201);

        $this->assertDatabaseHas('company_memberships', [
            'id' => $first->id,
            'is_current_position' => 0,
        ]);

        $this->assertDatabaseHas('company_memberships', [
            'company_id' => $company->id,
            'user_id' => $person->id,
            'company_role' => 'recruiter',
            'position' => 'Senior Developer',
            'is_current_position' => 1,
        ]);
    });
});