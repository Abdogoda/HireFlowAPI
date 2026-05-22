<?php

beforeEach(function () {
        $this->createDefaultRoles();
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
