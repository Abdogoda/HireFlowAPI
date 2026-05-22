<?php

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
        $this->createDefaultRoles();    
        Storage::fake('public');
    });

describe('Upload Avatar', function () {
        it('uploads avatar successfully', function () {
            $user = $this->createUser();

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
            $user = $this->createUser();

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
