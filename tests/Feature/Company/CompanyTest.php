<?php

use App\Enums\Authorization\CompanyRoles;
use App\Policies\CompanyPolicy;

describe('Company Endpoints', function () {
    beforeEach(function () {
        $this->createDefaultRoles();
    });

    describe('Create Company', function () {
        it('creates a company for the authenticated user', function () {
            $user = $this->createUser();

            $response = $this->actingAs($user)
                ->postJson('/api/companies', [
                    'name' => 'Acme Ltd',
                    'description' => 'A sample company',
                    'slug' => 'acme-ltd',
                    'logo' => 'logos/acme.png',
                    'website' => 'https://acme.test',
                    'industry' => 'Technology',
                    'company_size' => '51-200',
                    'founded_year' => 2020,
                    'location' => 'London, UK',
                    'email' => 'hello@acme.test',
                    'phone' => '123456789',
                    'is_verified' => true,
                ]);

            $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'company' => [
                            'id',
                            'slug',
                            'name',
                            'description',
                            'logo',
                            'website',
                            'industry',
                            'company_size',
                            'founded_year',
                            'location',
                            'email',
                            'phone',
                            'created_by',
                            'is_verified',
                            'people',
                        ],
                    ],
                ])
                ->assertJson([
                    'data' => [
                        'company' => [
                            'name' => 'Acme Ltd',
                            'slug' => 'acme-ltd',
                            'created_by' => $user->id,
                        ],
                    ],
                ]);

            $this->assertDatabaseHas('companies', [
                'created_by' => $user->id,
                'slug' => 'acme-ltd',
                'name' => 'Acme Ltd',
            ]);

            $this->assertDatabaseHas('company_memberships', [
                'company_role' => 'company_owner',
                'position' => 'Company Owner',
                'user_id' => $user->id,
            ]);
        });

        it('fails without authentication', function () {
            $response = $this->postJson('/api/companies', [
                'name' => 'Acme Ltd',
            ]);

            $response->assertStatus(401);
        });
    });

    describe('Company Listing', function () {
        it('lists companies owned by and assigned to the user', function () {
            $owner = $this->createUser(['email' => 'owner@example.com']);
            $member = $this->createUser(['email' => 'member@example.com']);

            $ownedCompany = $owner->ownedCompanies()->create([
                'name' => 'Owned Company',
                'slug' => 'owned-company',
            ]);

            $sharedCompany = $this->createUser(['email' => 'other-owner@example.com'])
                ->ownedCompanies()
                ->create([
                    'name' => 'Shared Company',
                    'slug' => 'shared-company',
                ]);

            $ownedCompany->memberships()->create([
                'user_id' => $owner->id,
                'company_role' => CompanyRoles::OWNER->value,
                'position' => 'Company Owner',
                'start_date' => now()->toDateString(),
                'is_current_position' => true,
            ]);

            $sharedCompany->memberships()->create([
                'user_id' => $member->id,
                'company_role' => CompanyRoles::RECRUITER->value,
                'position' => 'Recruiter',
                'start_date' => now()->toDateString(),
                'is_current_position' => true,
            ]);

            $response = $this->actingAs($member)
                ->getJson('/api/companies');

            $response->assertStatus(200)
                ->assertJsonCount(1, 'data.companies')
                ->assertJson([
                    'data' => [
                        'companies' => [
                            [
                                'name' => 'Shared Company',
                            ],
                        ],
                    ],
                ]);
        });
    });

    describe('Company Policy', function () {
        it('allows owners and current members to view companies', function () {
            $policy = new CompanyPolicy();
            $owner = $this->createUser(['email' => 'owner@example.com']);
            $member = $this->createUser(['email' => 'member@example.com']);

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

            expect($policy->view($owner, $company))->toBeTrue();
            expect($policy->view($member, $company))->toBeTrue();
        });

        it('allows only owners and admins to manage companies', function () {
            $policy = new CompanyPolicy();
            $owner = $this->createUser(['email' => 'owner@example.com']);
            $admin = $this->createUser(['email' => 'admin@example.com']);
            $member = $this->createUser(['email' => 'member@example.com']);

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
                'user_id' => $admin->id,
                'company_role' => CompanyRoles::ADMIN->value,
                'position' => 'Operations Lead',
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

            expect($policy->update($owner, $company))->toBeTrue();
            expect($policy->update($admin, $company))->toBeTrue();
            expect($policy->update($member, $company))->toBeFalse();
            expect($policy->delete($admin, $company))->toBeTrue();
            expect($policy->managePeople($admin, $company))->toBeTrue();
            expect($policy->managePeople($member, $company))->toBeFalse();
        });
    });
});