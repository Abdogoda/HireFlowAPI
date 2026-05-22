<?php

use App\Enums\Authorization\CompanyRoles;
use App\Policies\CompanyPolicy;
use App\Notifications\CompanyMemberAddedNotification;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
        $this->createDefaultRoles();
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
