<?php

beforeEach(function () {
        $this->createDefaultRoles();
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
