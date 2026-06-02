<?php

use App\Enums\Vacancy\EmploymentType;
use App\Enums\Vacancy\ExperienceLevel;
use App\Enums\Vacancy\VacancyStatus;
use App\Enums\Vacancy\WorkMode;
use App\Enums\Company\CompanyRoles;

beforeEach(function () {
    $this->createDefaultRoles();
});

describe('Create Vacancy', function () {
    it('allows a company owner to create a vacancy', function () {
        $owner = $this->createUser(['email' => 'owner@example.com']);

        $company = $owner->ownedCompanies()->create([
            'slug' => 'owner-company',
            'name' => 'Owner Company',
        ]);

        $response = $this->actingAs($owner)
            ->postJson('/api/vacancies', [
                'company_id' => $company->id,
                'title' => 'Senior Backend Developer',
                'description' => 'Build and maintain backend services.',
                'requirements' => 'Laravel, PHP 8.2, MySQL',
                'responsibilities' => 'Design APIs, write tests, review code.',
                'employment_type' => EmploymentType::FULL_TIME->value,
                'experience_level' => ExperienceLevel::SENIOR->value,
                'salary_min' => 2500,
                'salary_max' => 4000,
                'location' => 'Cairo, Egypt',
                'work_mode' => WorkMode::HYBRID->value,
                'status' => VacancyStatus::PUBLISHED->value,
                'application_deadline' => now()->addWeeks(3)->toDateString(),
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Vacancy created successfully',
                'data' => [
                    'vacancy' => [
                        'title' => 'Senior Backend Developer',
                        'status' => VacancyStatus::PUBLISHED->value,
                        'employment_type' => EmploymentType::FULL_TIME->value,
                        'work_mode' => WorkMode::HYBRID->value,
                    ],
                ],
            ]);

        $this->assertDatabaseHas('vacancies', [
            'company_id' => $company->id,
            'created_by' => $owner->id,
            'title' => 'Senior Backend Developer',
            'status' => VacancyStatus::PUBLISHED->value,
        ]);
    });

    it('allows a recruiter to create a vacancy for a company', function () {
        $companyOwner = $this->createUser(['email' => 'company-owner@example.com']);
        $company = $companyOwner->ownedCompanies()->create([
            'slug' => 'recruiter-company',
            'name' => 'Recruiter Company',
        ]);

        $recruiter = $this->createUserWithRole('Recruiter', [
            'email' => 'recruiter@example.com',
        ]);

        // Ensure recruiter is a member of the company so they can create vacancies for it
        $company->memberships()->create([
            'member_id' => $recruiter->id,
            'company_role' => CompanyRoles::RECRUITER->value,
            'position' => 'Recruiter',
            'is_current_position' => true,
        ]);

        $response = $this->actingAs($recruiter)
            ->postJson('/api/vacancies', [
                'company_id' => $company->id,
                'title' => 'Frontend Developer',
                'description' => 'Build the user interface.',
                'requirements' => 'React, TypeScript',
                'responsibilities' => 'Create UI components and maintain design systems.',
                'employment_type' => EmploymentType::CONTRACT->value,
                'experience_level' => ExperienceLevel::MID->value,
                'salary_min' => 1800,
                'salary_max' => 2600,
                'location' => 'Remote',
                'work_mode' => WorkMode::REMOTE->value,
                'application_deadline' => now()->addWeeks(2)->toDateString(),
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Vacancy created successfully',
                'data' => [
                    'vacancy' => [
                        'title' => 'Frontend Developer',
                        'status' => VacancyStatus::DRAFT->value,
                        'employment_type' => EmploymentType::CONTRACT->value,
                        'work_mode' => WorkMode::REMOTE->value,
                    ],
                ],
            ]);

        $this->assertDatabaseHas('vacancies', [
            'company_id' => $company->id,
            'created_by' => $recruiter->id,
            'title' => 'Frontend Developer',
            'status' => VacancyStatus::DRAFT->value,
        ]);
    });

    it('forbids a candidate from creating a vacancy for another company', function () {
        $companyOwner = $this->createUser(['email' => 'owner@example.com']);
        $company = $companyOwner->ownedCompanies()->create([
            'slug' => 'candidate-company',
            'name' => 'Candidate Company',
        ]);

        $candidate = $this->createUserWithRole('Candidate', [
            'email' => 'candidate@example.com',
        ]);

        $response = $this->actingAs($candidate)
            ->postJson('/api/vacancies', [
                'company_id' => $company->id,
                'title' => 'Product Manager',
                'description' => 'Own product decisions.',
                'requirements' => 'Roadmaps, analytics',
                'responsibilities' => 'Define scope and priorities.',
                'employment_type' => EmploymentType::FULL_TIME->value,
                'experience_level' => ExperienceLevel::LEAD->value,
                'location' => 'Cairo, Egypt',
                'work_mode' => WorkMode::ONSITE->value,
                'application_deadline' => now()->addWeeks(4)->toDateString(),
            ]);

        $response->assertStatus(403);
    });
});