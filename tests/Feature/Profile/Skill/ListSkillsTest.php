<?php

beforeEach(function () {
        $this->createDefaultRoles();
    });

describe('List Skills', function () {
        it('retrieves all skills for authenticated user', function () {
            $user = $this->createUser();

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
            $user = $this->createUser();
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
