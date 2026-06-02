<?php

use App\Enums\Authorization\CompanyRoles;

beforeEach(function () {
        $this->createDefaultRoles();
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
