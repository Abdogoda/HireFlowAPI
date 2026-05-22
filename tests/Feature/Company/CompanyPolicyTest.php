<?php

use App\Enums\Authorization\CompanyRoles;
use App\Policies\CompanyPolicy;
use App\Notifications\CompanyMemberAddedNotification;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
        $this->createDefaultRoles();
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
