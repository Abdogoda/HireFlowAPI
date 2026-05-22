<?php

beforeEach(function () {
        $this->createDefaultRoles();
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
