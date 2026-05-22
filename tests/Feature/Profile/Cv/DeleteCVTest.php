<?php

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
        $this->createDefaultRoles();
        Storage::fake('public');
    });

describe('Delete CV', function () {
        it('deletes a CV successfully', function () {
            $user = $this->createUser();

            $file = UploadedFile::fake()->create('resume.pdf', 100, 'application/pdf');

            $cv = $this->actingAs($user)
                ->postJson('/api/profile/cvs', ['file' => $file])
                ->assertStatus(201)
                ->json('data.cv');

            $response = $this->actingAs($user)
                ->deleteJson("/api/profile/cvs/{$cv['id']}");

            $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'CV deleted successfully',
                ]);

            $this->assertDatabaseMissing('resumes', [
                'id' => $cv['id'],
            ]);
        });

        it('fails when deleting another user\'s CV', function () {
            $user1 = $this->createUser(['email' => 'user1@gmail.com']);
            $user2 = $this->createUser(['email' => 'user2@gmail.com']);

            $file = UploadedFile::fake()->create('resume.pdf', 100, 'application/pdf');

            $cv = $this->actingAs($user1)
                ->postJson('/api/profile/cvs', ['file' => $file])
                ->assertStatus(201)
                ->json('data.cv');

            $response = $this->actingAs($user2)
                ->deleteJson("/api/profile/cvs/{$cv['id']}");

            $response->assertStatus(403);

            $this->assertDatabaseHas('resumes', [
                'id' => $cv['id'],
            ]);
        });
    });
