<?php

beforeEach(function () {
        $this->createDefaultRoles();
    });

describe('Get Single Skill', function () {
        it('retrieves a specific skill', function () {
            $user = $this->createUser();
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
            $user1 = $this->createUser(['email' => 'user1@example.com']);
            $user2 = $this->createUser(['email' => 'user2@example.com']);

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
            $user = $this->createUser();

            $skill = $user->skills()->create(['name' => 'PHP', 'proficiency_level' => 'expert']);

            $response = $this->getJson("/api/profile/skills/{$skill->id}");

            $response->assertStatus(401);
        });
    });
