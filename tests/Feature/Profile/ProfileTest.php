<?php

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

describe('Profile Endpoints', function () {
    beforeEach(function () {
        Storage::fake('public');
        Role::create(['name' => 'Admin', 'description' => 'Administrator']);
        Role::create(['name' => 'Recruiter', 'description' => 'Recruiter']);
        Role::create(['name' => 'Candidate', 'description' => 'Candidate']);
    });

    describe('Get Profile', function () {
        it('retrieves authenticated user profile', function () {
            $role = Role::where('name', 'Candidate')->first();
            $user = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
                'email_verified_at' => now(),
                'bio' => 'Test bio',
                'phone_number' => '1234567890',
                'address' => '123 Main St',
                'city' => 'Springfield',
                'state' => 'IL',
                'country' => 'USA',
            ]);

            $response = $this->actingAs($user)
                ->getJson('/api/profile');

            $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'profile' => [
                            'id',
                            'name',
                            'email',
                            'bio',
                            'phone_number',
                            'address',
                            'city',
                            'state',
                            'country',
                            'role',
                            'skills',
                            'experiences',
                            'projects',
                            'resumes',
                            'social_profiles',
                        ],
                    ],
                ])
                ->assertJson([
                    'success' => true,
                    'message' => 'Profile retrieved successfully',
                    'data' => [
                        'profile' => [
                            'name' => 'John Doe',
                            'email' => 'john@example.com',
                            'bio' => 'Test bio',
                            'phone_number' => '1234567890',
                        ],
                    ],
                ]);
        });

        it('fails without authentication', function () {
            $response = $this->getJson('/api/profile');

            $response->assertStatus(401);
        });
    });

    describe('Update Profile', function () {
        it('updates user profile with valid data', function () {
            $role = Role::where('name', 'Candidate')->first();
            $user = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
                'email_verified_at' => now(),
            ]);

            $response = $this->actingAs($user)
                ->patchJson('/api/profile', [
                    'name' => 'Jane Doe',
                    'bio' => 'Updated bio',
                    'phone_number' => '9876543210',
                    'address' => '456 Oak Ave',
                    'city' => 'Shelbyville',
                    'state' => 'IL',
                    'country' => 'USA',
                ]);

            $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'profile' => [
                            'id',
                            'name',
                            'bio',
                            'phone_number',
                            'address',
                            'city',
                            'state',
                            'country',
                        ],
                    ],
                ])
                ->assertJson([
                    'success' => true,
                    'message' => 'Profile updated successfully',
                    'data' => [
                        'profile' => [
                            'name' => 'Jane Doe',
                            'bio' => 'Updated bio',
                            'phone_number' => '9876543210',
                            'address' => '456 Oak Ave',
                            'city' => 'Shelbyville',
                        ],
                    ],
                ]);

            $this->assertDatabaseHas('users', [
                'id' => $user->id,
                'name' => 'Jane Doe',
                'bio' => 'Updated bio',
            ]);
        });

        it('updates only provided fields', function () {
            $role = Role::where('name', 'Candidate')->first();
            $user = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
                'bio' => 'Original bio',
            ]);

            $response = $this->actingAs($user)
                ->patchJson('/api/profile', [
                    'name' => 'John Smith',
                ]);

            $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'profile' => [
                            'name' => 'John Smith',
                        ],
                    ],
                ]);

            $this->assertDatabaseHas('users', [
                'id' => $user->id,
                'name' => 'John Smith',
                'bio' => 'Original bio',
            ]);
        });

        it('fails without authentication', function () {
            $response = $this->patchJson('/api/profile', [
                'name' => 'Jane Doe',
            ]);

            $response->assertStatus(401);
        });
    });

    describe('Upload Avatar', function () {
        it('uploads avatar successfully', function () {
            $role = Role::where('name', 'Candidate')->first();
            $user = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
                'email_verified_at' => now(),
            ]);

            $file = UploadedFile::fake()->image('avatar.jpg', 100, 100);

            $response = $this->actingAs($user)
                ->postJson('/api/profile/avatar', [
                    'avatar' => $file,
                ]);

            $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => ['url'],
                ])
                ->assertJson([
                    'success' => true,
                    'message' => 'Avatar uploaded successfully',
                ]);

            $this->assertNotEmpty($response->json('data.url'));
        });

        it('deletes old avatar when uploading new one', function () {
            $role = Role::where('name', 'Candidate')->first();
            $user = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
                'email_verified_at' => now(),
            ]);

            $oldFile = UploadedFile::fake()->image('old_avatar.jpg');
            $oldPath = $oldFile->store('avatars', 'public');
            $user->update(['avatar' => $oldPath]);

            $newFile = UploadedFile::fake()->image('new_avatar.jpg');

            $response = $this->actingAs($user)
                ->postJson('/api/profile/avatar', [
                    'avatar' => $newFile,
                ]);

            $response->assertStatus(201);
        });

        it('fails without authentication', function () {
            $file = UploadedFile::fake()->image('avatar.jpg');

            $response = $this->postJson('/api/profile/avatar', [
                'avatar' => $file,
            ]);

            $response->assertStatus(401);
        });

        it('fails without avatar file', function () {
            $role = Role::where('name', 'Candidate')->first();
            $user = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
            ]);

            $response = $this->actingAs($user)
                ->postJson('/api/profile/avatar', []);

            $response->assertStatus(422);
        });
    });

    describe('Delete Avatar', function () {
        it('deletes avatar successfully', function () {
            $role = Role::where('name', 'Candidate')->first();
            $user = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
                'email_verified_at' => now(),
            ]);

            $file = UploadedFile::fake()->image('avatar.jpg');
            $path = $file->store('avatars', 'public');
            $user->update(['avatar' => $path]);

            $response = $this->actingAs($user)
                ->deleteJson('/api/profile/avatar');

            $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Avatar deleted successfully',
                ]);

            $this->assertDatabaseHas('users', [
                'id' => $user->id,
                'avatar' => null,
            ]);
        });

        it('handles deletion when no avatar exists', function () {
            $role = Role::where('name', 'Candidate')->first();
            $user = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
            ]);

            $response = $this->actingAs($user)
                ->deleteJson('/api/profile/avatar');

            $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Avatar deleted successfully',
                ]);
        });

        it('fails without authentication', function () {
            $response = $this->deleteJson('/api/profile/avatar');

            $response->assertStatus(401);
        });
    });

    describe('Upload Profile Picture', function () {
        it('uploads profile picture successfully', function () {
            $role = Role::where('name', 'Candidate')->first();
            $user = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
                'email_verified_at' => now(),
            ]);

            $file = UploadedFile::fake()->image('profile.jpg', 500, 500);

            $response = $this->actingAs($user)
                ->postJson('/api/profile/picture', [
                    'picture' => $file,
                    'type' => 'profile',
                ]);

            $response->assertStatus(201)
                ->assertJson([
                    'success' => true,
                    'message' => 'Picture uploaded successfully',
                ]);

            $this->assertNotEmpty($response->json('data.url'));
        });

        it('uploads thumbnail successfully', function () {
            $role = Role::where('name', 'Candidate')->first();
            $user = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
            ]);

            $file = UploadedFile::fake()->image('thumbnail.jpg');

            $response = $this->actingAs($user)
                ->postJson('/api/profile/picture', [
                    'picture' => $file,
                    'type' => 'thumbnail',
                ]);

            $response->assertStatus(201)
                ->assertJson([
                    'success' => true,
                    'message' => 'Picture uploaded successfully',
                ]);
        });

        it('requires type parameter', function () {
            $role = Role::where('name', 'Candidate')->first();
            $user = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
            ]);

            $file = UploadedFile::fake()->image('picture.jpg');

            $response = $this->actingAs($user)
                ->postJson('/api/profile/picture', [
                    'picture' => $file,
                ]);

            $response->assertStatus(422);
        });
    });

    describe('Delete Profile Picture', function () {
        it('deletes profile picture successfully', function () {
            $role = Role::where('name', 'Candidate')->first();
            $user = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
            ]);

            $file = UploadedFile::fake()->image('profile.jpg');
            $path = $file->store('profile-pictures', 'public');
            $user->update(['profile_image' => $path]);

            $response = $this->actingAs($user)
                ->deleteJson('/api/profile/picture', [
                    'type' => 'profile',
                ]);

            $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Picture deleted successfully',
                ]);

            $this->assertDatabaseHas('users', [
                'id' => $user->id,
                'profile_image' => null,
            ]);
        });

        it('deletes thumbnail successfully', function () {
            $role = Role::where('name', 'Candidate')->first();
            $user = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
            ]);

            $file = UploadedFile::fake()->image('thumbnail.jpg');
            $path = $file->store('thumbnails', 'public');
            $user->update(['thumbnail' => $path]);

            $response = $this->actingAs($user)
                ->deleteJson('/api/profile/picture', [
                    'type' => 'thumbnail',
                ]);

            $response->assertStatus(200);

            $this->assertDatabaseHas('users', [
                'id' => $user->id,
                'thumbnail' => null,
            ]);
        });
    });
});
