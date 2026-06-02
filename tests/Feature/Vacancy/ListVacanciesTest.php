<?php

use App\Enums\Vacancy\EmploymentType;
use App\Enums\Vacancy\ExperienceLevel;
use App\Enums\Vacancy\VacancyStatus;
use App\Enums\Vacancy\WorkMode;

beforeEach(function () {
    $this->createDefaultRoles();
});

describe('Vacancy Listing', function () {
    it('searches and filters vacancies', function () {
        $viewer = $this->createUser(['email' => 'viewer@example.com']);
        $owner = $this->createUser(['email' => 'owner@example.com']);

        $company = $owner->ownedCompanies()->create([
            'slug' => 'search-company',
            'name' => 'Search Company',
        ]);

        $company->vacancies()->create([
            'created_by' => $owner->id,
            'title' => 'Backend Engineer',
            'description' => 'Build APIs and services.',
            'requirements' => 'Laravel',
            'responsibilities' => 'Ship backend features.',
            'employment_type' => EmploymentType::FULL_TIME->value,
            'experience_level' => ExperienceLevel::SENIOR->value,
            'location' => 'Cairo',
            'work_mode' => WorkMode::HYBRID->value,
            'status' => VacancyStatus::PUBLISHED->value,
            'application_deadline' => now()->addWeeks(2)->toDateString(),
            'published_at' => now(),
        ]);

        $company->vacancies()->create([
            'created_by' => $owner->id,
            'title' => 'Frontend Engineer',
            'description' => 'Build interfaces.',
            'requirements' => 'React',
            'responsibilities' => 'Ship UI features.',
            'employment_type' => EmploymentType::CONTRACT->value,
            'experience_level' => ExperienceLevel::MID->value,
            'location' => 'Alexandria',
            'work_mode' => WorkMode::REMOTE->value,
            'status' => VacancyStatus::DRAFT->value,
            'application_deadline' => now()->addWeeks(3)->toDateString(),
        ]);

        $company->vacancies()->create([
            'created_by' => $owner->id,
            'title' => 'Product Manager',
            'description' => 'Lead product discovery.',
            'requirements' => 'Analytics, roadmaps',
            'responsibilities' => 'Own priorities.',
            'employment_type' => EmploymentType::FULL_TIME->value,
            'experience_level' => ExperienceLevel::LEAD->value,
            'location' => 'Cairo',
            'work_mode' => WorkMode::ONSITE->value,
            'status' => VacancyStatus::PUBLISHED->value,
            'application_deadline' => now()->addWeeks(4)->toDateString(),
            'published_at' => now(),
        ]);

        $response = $this->actingAs($viewer)
            ->getJson('/api/vacancies?search=Engineer&status=published');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.vacancies')
            ->assertJson([
                'data' => [
                    'vacancies' => [
                        [
                            'title' => 'Backend Engineer',
                            'status' => VacancyStatus::PUBLISHED->value,
                        ],
                    ],
                ],
            ]);
    });

    it('paginates and sorts vacancies', function () {
        $viewer = $this->createUser(['email' => 'viewer@example.com']);
        $owner = $this->createUser(['email' => 'owner@example.com']);

        $company = $owner->ownedCompanies()->create([
            'slug' => 'sort-company',
            'name' => 'Sort Company',
        ]);

        foreach (['Gamma Role', 'Alpha Role', 'Beta Role'] as $title) {
            $company->vacancies()->create([
                'created_by' => $owner->id,
                'title' => $title,
                'description' => 'Job description for ' . $title,
                'requirements' => 'Requirements',
                'responsibilities' => 'Responsibilities',
                'employment_type' => EmploymentType::FULL_TIME->value,
                'experience_level' => ExperienceLevel::MID->value,
                'location' => 'Cairo',
                'work_mode' => WorkMode::REMOTE->value,
                'status' => VacancyStatus::PUBLISHED->value,
                'application_deadline' => now()->addWeeks(2)->toDateString(),
                'published_at' => now(),
            ]);
        }

        $response = $this->actingAs($viewer)
            ->getJson('/api/vacancies?per_page=2&sort_by=title&sort_direction=asc');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'pagination' => [
                        'current_page' => 1,
                        'total' => 3,
                        'per_page' => 2,
                        'last_page' => 2,
                    ],
                ],
            ])
            ->assertJsonCount(2, 'data.vacancies')
            ->assertJson([
                'data' => [
                    'vacancies' => [
                        [
                            'title' => 'Alpha Role',
                        ],
                        [
                            'title' => 'Beta Role',
                        ],
                    ],
                ],
            ]);
    });
});