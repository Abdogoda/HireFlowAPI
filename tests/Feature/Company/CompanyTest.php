<?php

use App\Enums\Authorization\CompanyRoles;
use App\Policies\CompanyPolicy;
use App\Notifications\CompanyMemberAddedNotification;
use Illuminate\Support\Facades\Notification;

describe('Company Endpoints', function () {
    beforeEach(function () {
        $this->createDefaultRoles();
    });

    describe('Create Company', function () {
        it('creates a company for the authenticated user', function () {
            Notification::fake();

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

            Notification::assertSentTo($user, CompanyMemberAddedNotification::class);
        });

        it('fails without authentication', function () {
            $response = $this->postJson('/api/companies', [
                'name' => 'Acme Ltd',
            ]);

            $response->assertStatus(401);
        });
    });

    describe('Company Listing', function () {
        it('lists all companies for any authenticated user', function () {
            $viewer = $this->createUser(['email' => 'viewer@example.com']);

            $this->createUser(['email' => 'owner-one@example.com'])
                ->ownedCompanies()
                ->create([
                    'name' => 'Owned Company',
                    'slug' => 'owned-company',
                ]);

            $this->createUser(['email' => 'owner-two@example.com'])
                ->ownedCompanies()
                ->create([
                    'name' => 'Shared Company',
                    'slug' => 'shared-company',
                ]);

            $response = $this->actingAs($viewer)
                ->getJson('/api/companies');

            $response->assertStatus(200)
                ->assertJsonCount(2, 'data.companies');
        });

        it('supports search and filter query parameters', function () {
            $viewer = $this->createUser(['email' => 'viewer@example.com']);
            $owner = $this->createUser(['email' => 'owner@example.com']);

            $owner->ownedCompanies()->create([
                'name' => 'Alpha Tech',
                'slug' => 'alpha-tech',
                'industry' => 'Technology',
                'location' => 'Cairo',
                'is_verified' => true,
            ]);

            $owner->ownedCompanies()->create([
                'name' => 'Beta Foods',
                'slug' => 'beta-foods',
                'industry' => 'Food',
                'location' => 'Alexandria',
                'is_verified' => false,
            ]);

            $response = $this->actingAs($viewer)
                ->getJson('/api/companies?search=Alpha&industry=Technology&is_verified=1');

            $response->assertStatus(200)
                ->assertJsonCount(1, 'data.companies')
                ->assertJson([
                    'data' => [
                        'companies' => [
                            [
                                'name' => 'Alpha Tech',
                                'industry' => 'Technology',
                                'is_verified' => true,
                            ],
                        ],
                    ],
                ]);
        });
    });

    describe('Company Show and My Companies', function () {
        it('allows any authenticated user to view company details', function () {
            $viewer = $this->createUser(['email' => 'viewer@example.com']);
            $owner = $this->createUser(['email' => 'owner@example.com']);

            $company = $owner->ownedCompanies()->create([
                'name' => 'Acme Ltd',
                'slug' => 'acme-ltd',
            ]);

            $response = $this->actingAs($viewer)
                ->getJson("/api/companies/{$company->id}");

            $response->assertStatus(200)
                ->assertJsonPath('data.company.id', $company->id)
                ->assertJsonPath('data.company.name', 'Acme Ltd');
        });

        it('lists only user companies in dedicated endpoint', function () {
            $user = $this->createUser(['email' => 'member@example.com']);
            $owner = $this->createUser(['email' => 'owner@example.com']);

            $ownedCompany = $user->ownedCompanies()->create([
                'name' => 'Owned Co',
                'slug' => 'owned-co',
            ]);

            $memberCompany = $owner->ownedCompanies()->create([
                'name' => 'Member Co',
                'slug' => 'member-co',
            ]);

            $otherCompany = $owner->ownedCompanies()->create([
                'name' => 'Other Co',
                'slug' => 'other-co',
            ]);

            $memberCompany->memberships()->create([
                'user_id' => $user->id,
                'company_role' => CompanyRoles::RECRUITER->value,
                'position' => 'Recruiter',
                'start_date' => now()->toDateString(),
                'is_current_position' => true,
            ]);

            $response = $this->actingAs($user)
                ->getJson('/api/companies/my');

            $response->assertStatus(200)
                ->assertJsonCount(2, 'data.companies');

            $companyIds = collect($response->json('data.companies'))->pluck('id');

            expect($companyIds)->toContain($ownedCompany->id);
            expect($companyIds)->toContain($memberCompany->id);
            expect($companyIds)->not->toContain($otherCompany->id);
        });
    });

    describe('Company Policy', function () {
        it('allows any authenticated user to view companies', function () {
            $policy = new CompanyPolicy();
            $owner = $this->createUser(['email' => 'owner@example.com']);
            $member = $this->createUser(['email' => 'member@example.com']);
            $viewer = $this->createUser(['email' => 'viewer@example.com']);

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
            expect($policy->view($viewer, $company))->toBeTrue();
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