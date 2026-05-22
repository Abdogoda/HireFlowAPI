<?php

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
        $this->createDefaultRoles();    
        Storage::fake('public');
    });

describe('Get Profile', function () {
        it('retrieves authenticated user profile', function () {
            $user = $this->createUser([
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
                            'name' => $user->name,
                            'email' => $user->email,
                            'bio' => $user->bio,
                            'phone_number' => $user->phone_number,
                        ],
                    ],
                ]);
        });

        it('fails without authentication', function () {
            $response = $this->getJson('/api/profile');

            $response->assertStatus(401);
        });
    });
