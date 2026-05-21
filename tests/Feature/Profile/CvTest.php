<?php

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

describe('CV/Resume Endpoints', function () {
    beforeEach(function () {
        Role::create(['name' => 'Admin', 'description' => 'Administrator']);
        Role::create(['name' => 'Recruiter', 'description' => 'Recruiter']);
        Role::create(['name' => 'Candidate', 'description' => 'Candidate']);
        Storage::fake('public');
    });

    describe('List CVs', function () {
        it('retrieves all CVs for authenticated user', function () {
            $role = Role::where('name', 'Candidate')->first();
            $user = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
                'email_verified_at' => now(),
            ]);

            $response = $this->actingAs($user)
                ->getJson('/api/profile/cvs');

            $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'cvs' => [
                            '*' => [
                                'id',
                                'title',
                                'file_path',
                                'file_url',
                            ],
                        ],
                    ],
                ])
                ->assertJson([
                    'success' => true,
                    'message' => 'CVs retrieved successfully',
                ]);
        });

        it('returns empty array when user has no CVs', function () {
            $role = Role::where('name', 'Candidate')->first();
            $user = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
            ]);

            $response = $this->actingAs($user)
                ->getJson('/api/profile/cvs');

            $response->assertStatus(200)
                ->assertJson([
                    'data' => ['cvs' => []],
                ]);
        });

        it('fails without authentication', function () {
            $response = $this->getJson('/api/profile/cvs');

            $response->assertStatus(401);
        });
    });

    describe('Upload CV', function () {
        it('uploads a CV successfully', function () {
            $role = Role::where('name', 'Candidate')->first();
            $user = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
                'email_verified_at' => now(),
            ]);

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
            $role = Role::where('name', 'Candidate')->first();
            $user = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
            ]);

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
            $role = Role::where('name', 'Candidate')->first();
            $user = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
            ]);

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
            $role = Role::where('name', 'Candidate')->first();
            $user = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
            ]);

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

    describe('Get Single CV', function () {
        it('retrieves a specific CV', function () {
            $role = Role::where('name', 'Candidate')->first();
            $user = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
            ]);

            $file = UploadedFile::fake()->create('resume.pdf', 100, 'application/pdf');

            $cv = $this->actingAs($user)
                ->postJson('/api/profile/cvs', ['file' => $file])
                ->assertStatus(201)
                ->json('data.cv');

            $response = $this->actingAs($user)
                ->getJson("/api/profile/cvs/{$cv['id']}");

            $response->assertStatus(200)
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
                    'data' => [
                        'cv' => [
                            'id' => $cv['id'],
                        ],
                    ],
                ]);
        });

        it('fails when accessing another user\'s CV', function () {
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

            $file = UploadedFile::fake()->create('resume.pdf', 100, 'application/pdf');

            $cv = $this->actingAs($user1)
                ->postJson('/api/profile/cvs', ['file' => $file])
                ->assertStatus(201)
                ->json('data.cv');

            $response = $this->actingAs($user2)
                ->getJson("/api/profile/cvs/{$cv['id']}");

            $response->assertStatus(403)
                ->assertJson(['message' => 'You do not have permission to view this CV']);
        });
    });

    describe('Delete CV', function () {
        it('deletes a CV successfully', function () {
            $role = Role::where('name', 'Candidate')->first();
            $user = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
            ]);

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
});
