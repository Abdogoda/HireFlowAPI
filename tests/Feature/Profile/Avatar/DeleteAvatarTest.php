<?php

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
        $this->createDefaultRoles();    
        Storage::fake('public');
    });

describe('Delete Avatar', function () {
        it('deletes avatar successfully', function () {
            $user = $this->createUser();

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
            $user = $this->createUser();
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
