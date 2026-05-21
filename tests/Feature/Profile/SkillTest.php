<?php

use App\Models\User;
use App\Models\Role;

describe('Skill Endpoints', function () {
    beforeEach(function () {
        Role::create(['name' => 'Admin', 'description' => 'Administrator']);
        Role::create(['name' => 'Recruiter', 'description' => 'Recruiter']);
        Role::create(['name' => 'Candidate', 'description' => 'Candidate']);
    });

    describe('List Skills', function () {
        it('retrieves all skills for authenticated user', function () {
            $role = Role::where('name', 'Candidate')->first();
            $user = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
                'email_verified_at' => now(),
            ]);

            $user->skills()->createMany([
                ['name' => 'PHP', 'proficiency_level' => 'expert'],
                ['name' => 'Laravel', 'proficiency_level' => 'expert'],
                ['name' => 'JavaScript', 'proficiency_level' => 'advanced'],
            ]);

            $response = $this->actingAs($user)
                ->getJson('/api/profile/skills');

            $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'skills' => [
                            '*' => [
                                'id',
                                'name',
                                'proficiency_level',
                            ],
                        ],
                    ],
                ])
                ->assertJson([
                    'success' => true,
                    'message' => 'Skills retrieved successfully',
                ])
                ->assertJsonCount(3, 'data.skills');
        });

        it('returns empty array when user has no skills', function () {
            $role = Role::where('name', 'Candidate')->first();
            $user = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
            ]);

            $response = $this->actingAs($user)
                ->getJson('/api/profile/skills');

            $response->assertStatus(200)
                ->assertJson([
                    'data' => ['skills' => []],
                ]);
        });

        it('fails without authentication', function () {
            $response = $this->getJson('/api/profile/skills');

            $response->assertStatus(401);
        });
    });

    describe('Create Skill', function () {
        it('creates a new skill successfully', function () {
            $role = Role::where('name', 'Candidate')->first();
            $user = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
                'email_verified_at' => now(),
            ]);

            $response = $this->actingAs($user)
                ->postJson('/api/profile/skills', [
                    'name' => 'PHP',
                    'proficiency_level' => 'expert',
                ]);

            $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'skill' => [
                            'id',
                            'name',
                            'proficiency_level',
                        ],
                    ],
                ])
                ->assertJson([
                    'success' => true,
                    'message' => 'Skill created successfully',
                    'data' => [
                        'skill' => [
                            'name' => 'PHP',
                            'proficiency_level' => 'expert',
                        ],
                    ],
                ]);

            $this->assertDatabaseHas('skills', [
                'user_id' => $user->id,
                'name' => 'PHP',
                'proficiency_level' => 'expert',
            ]);
        });

        it('creates multiple skills for same user', function () {
            $role = Role::where('name', 'Candidate')->first();
            $user = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
            ]);

            $this->actingAs($user)
                ->postJson('/api/profile/skills', ['name' => 'PHP', 'proficiency_level' => 'expert'])
                ->assertStatus(201);

            $this->actingAs($user)
                ->postJson('/api/profile/skills', ['name' => 'Laravel', 'proficiency_level' => 'advanced'])
                ->assertStatus(201);

            $this->assertDatabaseHas('skills', [
                'user_id' => $user->id,
                'name' => 'PHP',
            ]);

            $this->assertDatabaseHas('skills', [
                'user_id' => $user->id,
                'name' => 'Laravel',
            ]);
        });

        it('fails without authentication', function () {
            $response = $this->postJson('/api/profile/skills', [
                'name' => 'PHP',
                'proficiency_level' => 'expert',
            ]);

            $response->assertStatus(401);
        });

        it('fails with missing required fields', function () {
            $role = Role::where('name', 'Candidate')->first();
            $user = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
            ]);

            $response = $this->actingAs($user)
                ->postJson('/api/profile/skills', []);

            $response->assertStatus(422);
        });

        it('fails with invalid proficiency level', function () {
            $role = Role::where('name', 'Candidate')->first();
            $user = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
            ]);

            $response = $this->actingAs($user)
                ->postJson('/api/profile/skills', [
                    'name' => 'PHP',
                    'proficiency_level' => 'invalid_level',
                ]);

            $response->assertStatus(422);
        });
    });

    describe('Get Single Skill', function () {
        it('retrieves a specific skill', function () {
            $role = Role::where('name', 'Candidate')->first();
            $user = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
            ]);

            $skill = $user->skills()->create([
                'name' => 'PHP',
                'proficiency_level' => 'expert',
            ]);

            $response = $this->actingAs($user)
                ->getJson("/api/profile/skills/{$skill->id}");

            $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'skill' => [
                            'id',
                            'name',
                            'proficiency_level',
                        ],
                    ],
                ])
                ->assertJson([
                    'data' => [
                        'skill' => [
                            'id' => $skill->id,
                            'name' => 'PHP',
                            'proficiency_level' => 'expert',
                        ],
                    ],
                ]);
        });

        it('fails when accessing another user\'s skill', function () {
            $role = Role::where('name', 'Candidate')->first();
            $user1 = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
            ]);

            $user2 = User::create([
                'name' => 'Jane Doe',
                'email' => 'jane@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
            ]);

            $skill = $user1->skills()->create([
                'name' => 'PHP',
                'proficiency_level' => 'expert',
            ]);

            $response = $this->actingAs($user2)
                ->getJson("/api/profile/skills/{$skill->id}");

            $response->assertStatus(403)
                ->assertJson(['message' => 'You do not have permission to view this skill']);
        });

        it('fails without authentication', function () {
            $role = Role::where('name', 'Candidate')->first();
            $user = User::create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
            ]);
            $skill = $user->skills()->create(['name' => 'PHP', 'proficiency_level' => 'expert']);

            $response = $this->getJson("/api/profile/skills/{$skill->id}");

            $response->assertStatus(401);
        });
    });

    describe('Update Skill', function () {
        it('updates a skill successfully', function () {
            $role = Role::where('name', 'Candidate')->first();
            $user = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
            ]);

            $skill = $user->skills()->create([
                'name' => 'PHP',
                'proficiency_level' => 'intermediate',
            ]);

            $response = $this->actingAs($user)
                ->patchJson("/api/profile/skills/{$skill->id}", [
                    'name' => 'PHP 8',
                    'proficiency_level' => 'expert',
                ]);

            $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Skill updated successfully',
                    'data' => [
                        'skill' => [
                            'name' => 'PHP 8',
                            'proficiency_level' => 'expert',
                        ],
                    ],
                ]);

            $this->assertDatabaseHas('skills', [
                'id' => $skill->id,
                'name' => 'PHP 8',
                'proficiency_level' => 'expert',
            ]);
        });

        it('updates only provided fields', function () {
            $role = Role::where('name', 'Candidate')->first();
            $user = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
            ]);

            $skill = $user->skills()->create([
                'name' => 'PHP',
                'proficiency_level' => 'intermediate',
            ]);

            $response = $this->actingAs($user)
                ->patchJson("/api/profile/skills/{$skill->id}", [
                    'proficiency_level' => 'expert',
                ]);

            $response->assertStatus(200);

            $this->assertDatabaseHas('skills', [
                'id' => $skill->id,
                'name' => 'PHP',
                'proficiency_level' => 'expert',
            ]);
        });

        it('fails when updating another user\'s skill', function () {
            $role = Role::where('name', 'Candidate')->first();
            $user1 = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
            ]);

            $user2 = User::create([
                'name' => 'Jane Doe',
                'email' => 'jane@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
            ]);

            $skill = $user1->skills()->create([
                'name' => 'PHP',
                'proficiency_level' => 'expert',
            ]);

            $response = $this->actingAs($user2)
                ->patchJson("/api/profile/skills/{$skill->id}", [
                    'proficiency_level' => 'beginner',
                ]);

            $response->assertStatus(403)
                ->assertJson(['message' => 'You do not have permission to update this skill']);
        });

        it('fails without authentication', function () {
            $role = Role::where('name', 'Candidate')->first();
            $user = User::create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
            ]);
            $skill = $user->skills()->create(['name' => 'PHP', 'proficiency_level' => 'expert']);

            $response = $this->patchJson("/api/profile/skills/{$skill->id}", [
                'proficiency_level' => 'expert',
            ]);

            $response->assertStatus(401);
        });
    });

    describe('Delete Skill', function () {
        it('deletes a skill successfully', function () {
            $role = Role::where('name', 'Candidate')->first();
            $user = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
            ]);

            $skill = $user->skills()->create([
                'name' => 'PHP',
                'proficiency_level' => 'expert',
            ]);

            $response = $this->actingAs($user)
                ->deleteJson("/api/profile/skills/{$skill->id}");

            $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Skill deleted successfully',
                ]);

            $this->assertDatabaseMissing('skills', [
                'id' => $skill->id,
            ]);
        });

        it('fails when deleting another user\'s skill', function () {
            $role = Role::where('name', 'Candidate')->first();
            $user1 = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
            ]);

            $user2 = User::create([
                'name' => 'Jane Doe',
                'email' => 'jane@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
            ]);

            $skill = $user1->skills()->create([
                'name' => 'PHP',
                'proficiency_level' => 'expert',
            ]);

            $response = $this->actingAs($user2)
                ->deleteJson("/api/profile/skills/{$skill->id}");

            $response->assertStatus(403);

            $this->assertDatabaseHas('skills', [
                'id' => $skill->id,
            ]);
        });

        it('fails without authentication', function () {
            $role = Role::where('name', 'Candidate')->first();
            $user = User::create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
            ]);
            $skill = $user->skills()->create(['name' => 'PHP', 'proficiency_level' => 'expert']);

            $response = $this->deleteJson("/api/profile/skills/{$skill->id}");

            $response->assertStatus(401);
        });
    });
});
