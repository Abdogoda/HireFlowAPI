<?php

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
        $this->createDefaultRoles();
        Storage::fake('public');
    });

describe('List CVs', function () {
        it('retrieves all CVs for authenticated user', function () {
            $user = $this->createUser();

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
            $user = $this->createUser();

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
