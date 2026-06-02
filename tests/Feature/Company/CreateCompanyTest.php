<?php

use App\Notifications\CompanyMemberAddedNotification;
use Illuminate\Support\Facades\Notification;

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
