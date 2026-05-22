<?php

beforeEach(function () {
        $this->createDefaultRoles();
    });

describe('Create Skill', function () {
        it('creates a new skill successfully', function () {
            $user = $this->createUser();

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
            $user = $this->createUser();
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
            $user = $this->createUser();
            $response = $this->actingAs($user)
                ->postJson('/api/profile/skills', []);

            $response->assertStatus(422);
        });

        it('fails with invalid proficiency level', function () {
            $user = $this->createUser();
            $response = $this->actingAs($user)
                ->postJson('/api/profile/skills', [
                    'name' => 'PHP',
                    'proficiency_level' => 'invalid_level',
                ]);

            $response->assertStatus(422);
        });
    });
