<?php

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
        $this->createDefaultRoles();    
        Storage::fake('public');
    });

describe('Update Profile', function () {
        it('updates user profile with valid data', function () {
            $user = $this->createUser();

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
            $user = $this->createUser([
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
