<?php

beforeEach(function () {
        $this->createDefaultRoles();
    });

describe('Create Project', function () {
        it('creates a new project successfully', function () {
            $user = $this->createUser();

            $response = $this->actingAs($user)
                ->postJson('/api/profile/projects', [
                    'title' => 'E-Commerce Platform',
                    'description' => 'A full-featured e-commerce platform',
                    'url' => 'https://example.com',
                    'technologies' => 'Laravel, React, PostgreSQL',
                    'start_date' => '2023-01-01',
                    'end_date' => '2023-06-30',
                ]);

            $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'project' => [
                            'id',
                            'title',
                            'description',
                            'url',
                            'technologies',
                            'start_date',
                            'end_date',
                        ],
                    ],
                ])
                ->assertJson([
                    'success' => true,
                    'message' => 'Project created successfully',
                    'data' => [
                        'project' => [
                            'title' => 'E-Commerce Platform',
                        ],
                    ],
                ]);

            $this->assertDatabaseHas('projects', [
                'user_id' => $user->id,
                'title' => 'E-Commerce Platform',
            ]);
        });

        it('creates project with minimal fields', function () {
            $user = $this->createUser();
            $response = $this->actingAs($user)
                ->postJson('/api/profile/projects', [
                    'title' => 'Simple Project',
                    'description' => 'A simple project',
                    'start_date' => '2023-01-01',
                ]);

            $response->assertStatus(201)
                ->assertJson([
                    'data' => [
                        'project' => [
                            'title' => 'Simple Project',
                        ],
                    ],
                ]);
        });

        it('creates multiple projects for same user', function () {
            $user = $this->createUser();
            $this->actingAs($user)
                ->postJson('/api/profile/projects', [
                    'title' => 'Project 1',
                    'description' => 'First project',
                    'start_date' => '2023-01-01',
                ])
                ->assertStatus(201);

            $this->actingAs($user)
                ->postJson('/api/profile/projects', [
                    'title' => 'Project 2',
                    'description' => 'Second project',
                    'start_date' => '2023-07-01',
                ])
                ->assertStatus(201);

            $this->assertDatabaseCount('projects', 2);
        });

        it('fails without authentication', function () {
            $response = $this->postJson('/api/profile/projects', [
                'title' => 'Test Project',
                'start_date' => '2023-01-01',
            ]);

            $response->assertStatus(401);
        });

        it('fails with missing title', function () {
            $user = $this->createUser();
            $response = $this->actingAs($user)
                ->postJson('/api/profile/projects', [
                    'start_date' => '2023-01-01',
                ]);

            $response->assertStatus(422);
        });

        it('fails with missing start_date', function () {
            $user = $this->createUser();
            $response = $this->actingAs($user)
                ->postJson('/api/profile/projects', [
                    'title' => 'Test Project',
                    'description' => 'Test description',
                ]);

            $response->assertStatus(422);
        });

        it('fails with invalid URL', function () {
            $user = $this->createUser();
            $response = $this->actingAs($user)
                ->postJson('/api/profile/projects', [
                    'title' => 'Test Project',
                    'start_date' => '2023-01-01',
                    'url' => 'not-a-valid-url',
                ]);

            $response->assertStatus(422);
        });
    });
