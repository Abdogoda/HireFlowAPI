<?php

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
        $this->createDefaultRoles();
        Storage::fake('public');
    });

describe('Upload CV', function () {
        it('uploads a CV successfully', function () {
            $user = $this->createUser();

            $file = UploadedFile::fake()->create('resume.pdf', 100, 'application/pdf');

            $response = $this->actingAs($user)
                ->postJson('/api/profile/cvs', [
                    'file' => $file,
                    'title' => 'My Professional Resume',
                ]);

            $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'cv' => [
                            'id',
                            'title',
                            'file_path',
                            'file_url',
                        ],
                    ],
                ])
                ->assertJson([
                    'success' => true,
                    'message' => 'CV uploaded successfully',
                    'data' => [
                        'cv' => [
                            'title' => 'My Professional Resume',
                        ],
                    ],
                ]);
        });

        it('uploads CV with default title', function () {
            $user = $this->createUser();

            $file = UploadedFile::fake()->create('my-cv.pdf', 100, 'application/pdf');

            $response = $this->actingAs($user)
                ->postJson('/api/profile/cvs', [
                    'file' => $file,
                ]);

            $response->assertStatus(201)
                ->assertJson([
                    'data' => [
                        'cv' => [
                            'title' => 'my-cv.pdf',
                        ],
                    ],
                ]);
        });

        it('uploads multiple CVs for same user', function () {
            $user = $this->createUser();

            $file1 = UploadedFile::fake()->create('resume1.pdf', 100, 'application/pdf');
            $file2 = UploadedFile::fake()->create('resume2.pdf', 100, 'application/pdf');

            $this->actingAs($user)
                ->postJson('/api/profile/cvs', ['file' => $file1])
                ->assertStatus(201);

            $this->actingAs($user)
                ->postJson('/api/profile/cvs', ['file' => $file2])
                ->assertStatus(201);

            $this->assertDatabaseCount('resumes', 2);
        });

        it('fails without authentication', function () {
            $file = UploadedFile::fake()->create('resume.pdf', 100, 'application/pdf');

            $response = $this->postJson('/api/profile/cvs', [
                'file' => $file,
            ]);

            $response->assertStatus(401);
        });

        it('fails with missing file', function () {
            $user = $this->createUser();

            $response = $this->actingAs($user)
                ->postJson('/api/profile/cvs', [
                    'title' => 'Resume',
                ]);

            $response->assertStatus(422);
        });

        it('fails with invalid file type', function () {
            $role = Role::where('name', 'Candidate')->first();
            $user = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
            ]);

            $file = UploadedFile::fake()->create('document.txt', 100, 'text/plain');

            $response = $this->actingAs($user)
                ->postJson('/api/profile/cvs', [
                    'file' => $file,
                ]);

            $response->assertStatus(422);
        });
    });
