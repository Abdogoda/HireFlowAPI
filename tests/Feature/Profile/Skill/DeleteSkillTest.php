<?php

beforeEach(function () {
        $this->createDefaultRoles();
    });

describe('Delete Skill', function () {
        it('deletes a skill successfully', function () {
            $user = $this->createUser();
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
            $user1 = $this->createUser(['email' => 'user1@example.com']);
            $user2 = $this->createUser(['email' => 'user2@example.com']);

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
            $user = $this->createUser(); 
            
            $skill = $user->skills()->create(['name' => 'PHP', 'proficiency_level' => 'expert']);

            $response = $this->deleteJson("/api/profile/skills/{$skill->id}");

            $response->assertStatus(401);
        });
    });
