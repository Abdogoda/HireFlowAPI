<?php

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

    describe('Company People', function () {
        it('adds a person with position, role, and information to a company', function () {
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
                'company_role' => 'company_owner',
                'position' => 'Company Owner',
                'start_date' => now()->toDateString(),
                'is_current_position' => true,
            ]);

            $response = $this->actingAs($owner)
                ->postJson("/api/companies/{$company->id}/people", [
                    'user_id' => $person->id,
                    'company_role' => 'recruiter',
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
        });

        it('shows a company with its people for an authorized user', function () {
            $owner = $this->createUser(['email' => 'owner@example.com']);
            $person = $this->createUser(['email' => 'person@example.com']);

            $company = $owner->ownedCompanies()->create([
                'name' => 'Acme Ltd',
                'description' => 'A sample company',
                'slug' => 'acme-ltd',
            ]);

            $company->memberships()->create([
                'user_id' => $owner->id,
                'company_role' => 'company_owner',
                'position' => 'Company Owner',
                'start_date' => now()->toDateString(),
                'is_current_position' => true,
            ]);

            $company->memberships()->create([
                'user_id' => $person->id,
                'company_role' => 'candidate',
                'position' => 'Designer',
                'information' => 'Handles product visuals',
                'start_date' => '2026-02-01',
                'is_current_position' => true,
            ]);

            $response = $this->actingAs($person)
                ->getJson("/api/companies/{$company->id}");

            $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'company' => [
                            'id' => $company->id,
                            'name' => 'Acme Ltd',
                            'people' => [
                                [
                                    'company_role' => 'company_owner',
                                ],
                            ],
                        ],
                    ],
                ])
                ->assertJsonCount(2, 'data.company.people');
        });

        it('rejects company access for unrelated users', function () {
            $owner = $this->createUser(['email' => 'owner@example.com']);
            $otherUser = $this->createUser(['email' => 'other@example.com']);

            $company = $owner->ownedCompanies()->create([
                'name' => 'Acme Ltd',
                'slug' => 'acme-ltd',
            ]);

            $company->memberships()->create([
                'user_id' => $owner->id,
                'company_role' => 'company_owner',
                'position' => 'Company Owner',
                'start_date' => now()->toDateString(),
                'is_current_position' => true,
            ]);

            $response = $this->actingAs($otherUser)
                ->getJson("/api/companies/{$company->id}");

            $response->assertStatus(403);
        });

        it('lets a company admin update company information', function () {
            $owner = $this->createUser(['email' => 'owner@example.com']);
            $admin = $this->createUser(['email' => 'admin@example.com']);

            $company = $owner->ownedCompanies()->create([
                'name' => 'Acme Ltd',
                'description' => 'Original description',
                'slug' => 'acme-ltd',
            ]);

            $company->memberships()->create([
                'user_id' => $owner->id,
                'company_role' => 'company_owner',
                'position' => 'Company Owner',
                'start_date' => now()->toDateString(),
                'is_current_position' => true,
            ]);

            $company->memberships()->create([
                'user_id' => $admin->id,
                'company_role' => 'admin',
                'position' => 'Operations Lead',
                'start_date' => '2026-02-01',
                'is_current_position' => true,
            ]);

            $response = $this->actingAs($admin)
                ->patchJson("/api/companies/{$company->id}", [
                    'description' => 'Updated by admin',
                ]);

            $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'company' => [
                            'description' => 'Updated by admin',
                        ],
                    ],
                ]);
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
                'company_role' => 'company_owner',
                'position' => 'Company Owner',
                'start_date' => now()->toDateString(),
                'is_current_position' => true,
            ]);

            $first = $company->memberships()->create([
                'user_id' => $person->id,
                'company_role' => 'candidate',
                'position' => 'Junior Developer',
                'start_date' => '2026-01-01',
                'is_current_position' => true,
            ]);

            $this->actingAs($owner)
                ->postJson("/api/companies/{$company->id}/people", [
                    'user_id' => $person->id,
                    'company_role' => 'recruiter',
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
});