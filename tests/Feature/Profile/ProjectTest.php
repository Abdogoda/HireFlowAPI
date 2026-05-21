<?php

describe('Project Endpoints', function () {
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

    describe('Get Single Project', function () {
        it('retrieves a specific project', function () {
            $user = $this->createUser();
            $project = $user->projects()->create([
                'title' => 'Test Project',
                'description' => 'Test description',
                'url' => 'https://test.com',
                'start_date' => '2023-01-01',
            ]);

            $response = $this->actingAs($user)
                ->getJson("/api/profile/projects/{$project->id}");

            $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'project' => [
                            'id',
                            'title',
                            'description',
                        ],
                    ],
                ])
                ->assertJson([
                    'data' => [
                        'project' => [
                            'id' => $project->id,
                            'title' => 'Test Project',
                        ],
                    ],
                ]);
        });

        it('fails when accessing another user\'s project', function () {
            $user1 = $this->createUser(['email' => 'user1@example.com']);
            $user2 = $this->createUser(['email' => 'user2@example.com']);

            $project = $user1->projects()->create([
                'title' => 'Test Project',
                'description' => 'Test description',
                'start_date' => '2023-01-01',
            ]);

            $response = $this->actingAs($user2)
                ->getJson("/api/profile/projects/{$project->id}");

            $response->assertStatus(403)
                ->assertJson(['message' => 'You do not have permission to view this project']);
        });

        it('fails without authentication', function () {
            $user = $this->createUser();
            
            $project = $user->projects()->create([
                'title' => 'Test Project',
                'description' => 'Test description',
                'start_date' => '2023-01-01',
            ]);

            $response = $this->getJson("/api/profile/projects/{$project->id}");

            $response->assertStatus(401);
        });
    });

    describe('Update Project', function () {
        it('updates a project successfully', function () {
            $user = $this->createUser();
            $project = $user->projects()->create([
                'title' => 'Old Title',
                'description' => 'Old description',
                'start_date' => '2023-01-01',
            ]);

            $response = $this->actingAs($user)
                ->patchJson("/api/profile/projects/{$project->id}", [
                    'title' => 'Updated Title',
                    'description' => 'Updated description',
                ]);

            $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Project updated successfully',
                    'data' => [
                        'project' => [
                            'title' => 'Updated Title',
                            'description' => 'Updated description',
                        ],
                    ],
                ]);

            $this->assertDatabaseHas('projects', [
                'id' => $project->id,
                'title' => 'Updated Title',
            ]);
        });

        it('updates only provided fields', function () {
            $user = $this->createUser();
            $project = $user->projects()->create([
                'title' => 'Original Title',
                'description' => 'Original description',
                'url' => 'https://original.com',
                'start_date' => '2023-01-01',
            ]);

            $response = $this->actingAs($user)
                ->patchJson("/api/profile/projects/{$project->id}", [
                    'url' => 'https://updated.com',
                ]);

            $response->assertStatus(200);

            $this->assertDatabaseHas('projects', [
                'id' => $project->id,
                'title' => 'Original Title',
                'url' => 'https://updated.com',
            ]);
        });

        it('fails when updating another user\'s project', function () {
            $user1 = $this->createUser(['email' => 'user1@example.com']);
            $user2 = $this->createUser(['email' => 'user2@example.com']);

            $project = $user1->projects()->create([
                'title' => 'Test Project',
                'description' => 'Test description',
                'start_date' => '2023-01-01',
            ]);

            $response = $this->actingAs($user2)
                ->patchJson("/api/profile/projects/{$project->id}", [
                    'title' => 'Hacked Title',
                ]);

            $response->assertStatus(403)
                ->assertJson(['message' => 'You do not have permission to update this project']);
        });

        it('fails without authentication', function () {
            $user = $this->createUser();
            
            $project = $user->projects()->create([
                'title' => 'Test Project',
                'description' => 'Test description',
                'start_date' => '2023-01-01',
            ]);

            $response = $this->patchJson("/api/profile/projects/{$project->id}", [
                'title' => 'Updated Title',
            ]);

            $response->assertStatus(401);
        });
    });

    describe('Delete Project', function () {
        it('deletes a project successfully', function () {
            $user = $this->createUser();
            $project = $user->projects()->create([
                'title' => 'Test Project',
                'description' => 'Test description',
                'start_date' => '2023-01-01',
            ]);

            $response = $this->actingAs($user)
                ->deleteJson("/api/profile/projects/{$project->id}");

            $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Project deleted successfully',
                ]);

            $this->assertDatabaseMissing('projects', [
                'id' => $project->id,
            ]);
        });

        it('fails when deleting another user\'s project', function () {
            $user1 = $this->createUser(['email' => 'user1@example.com']);
            $user2 = $this->createUser(['email' => 'user2@example.com']);

            $project = $user1->projects()->create([
                'title' => 'Test Project',
                'description' => 'Test description',
                'start_date' => '2023-01-01',
            ]);

            $response = $this->actingAs($user2)
                ->deleteJson("/api/profile/projects/{$project->id}");

            $response->assertStatus(403);

            $this->assertDatabaseHas('projects', [
                'id' => $project->id,
            ]);
        });

        it('fails without authentication', function () {
            $user = $this->createUser();
            
            $project = $user->projects()->create([
                'title' => 'Test Project',
                'description' => 'Test description',
                'start_date' => '2023-01-01',
            ]);

            $response = $this->deleteJson("/api/profile/projects/{$project->id}");

            $response->assertStatus(401);
        });
    });
});