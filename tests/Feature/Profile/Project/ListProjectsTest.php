<?php

beforeEach(function () {
        $this->createDefaultRoles();
    });

describe('List Projects', function () {
        it('retrieves all projects for authenticated user', function () {
            $user = $this->createUser();

            $user->projects()->createMany([
                [
                    'title' => 'E-Commerce Platform',
                    'description' => 'A full-featured e-commerce platform',
                    'url' => 'https://example.com',
                    'technologies' => ['Laravel', 'React', 'PostgreSQL'],
                    'start_date' => '2023-01-01',
                    'end_date' => '2023-06-30',
                ],
                [
                    'title' => 'CMS Application',
                    'description' => 'Content management system',
                    'url' => 'https://cms.example.com',
                    'technologies' => ['Laravel', 'Vue.js'],
                    'start_date' => '2023-07-01',
                ],
            ]);

            $response = $this->actingAs($user)
                ->getJson('/api/profile/projects');

            $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'projects' => [
                            '*' => [
                                'id',
                                'title',
                                'description',
                                'url',
                                'technologies',
                                'start_date',
                                'end_date',
                            ],
                        ],
                    ],
                ])
                ->assertJson([
                    'success' => true,
                    'message' => 'Projects retrieved successfully',
                ])
                ->assertJsonCount(2, 'data.projects');
        });

        it('returns empty array when user has no projects', function () {
            $user = $this->createUser();
            $response = $this->actingAs($user)
                ->getJson('/api/profile/projects');

            $response->assertStatus(200)
                ->assertJson([
                    'data' => ['projects' => []],
                ]);
        });

        it('fails without authentication', function () {
            $response = $this->getJson('/api/profile/projects');

            $response->assertStatus(401);
        });
    });
