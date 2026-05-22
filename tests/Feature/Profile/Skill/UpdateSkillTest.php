<?php

beforeEach(function () {
        $this->createDefaultRoles();
    });

describe('Update Skill', function () {
        it('updates a skill successfully', function () {
            $user = $this->createUser();
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
            $user = $this->createUser();
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
            $user1 = $this->createUser(['email' => 'user1@example.com']);
            $user2 = $this->createUser(['email' => 'user2@example.com']);

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
            $user = $this->createUser();

            $skill = $user->skills()->create(['name' => 'PHP']);

            $response = $this->patchJson("/api/profile/skills/{$skill->id}", [
                'proficiency_level' => 'expert',
            ]);

            $response->assertStatus(401);
        });
    });
