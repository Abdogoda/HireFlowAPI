<?php

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
        $this->createDefaultRoles();    
        Storage::fake('public');
    });

describe('Delete Profile Picture', function () {
        it('deletes profile picture successfully', function () {
            $user = $this->createUser();

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
            $user = $this->createUser();

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
