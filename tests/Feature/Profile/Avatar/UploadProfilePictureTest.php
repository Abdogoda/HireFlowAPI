<?php

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
        $this->createDefaultRoles();    
        Storage::fake('public');
    });

describe('Upload Profile Picture', function () {
        it('uploads profile picture successfully', function () {
            $user = $this->createUser();

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
            $user = $this->createUser();

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
            $user = $this->createUser();

            $file = UploadedFile::fake()->image('picture.jpg');

            $response = $this->actingAs($user)
                ->postJson('/api/profile/picture', [
                    'picture' => $file,
                ]);

            $response->assertStatus(422);
        });
    });
