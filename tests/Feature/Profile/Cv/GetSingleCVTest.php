<?php

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
        $this->createDefaultRoles();
        Storage::fake('public');
    });

describe('Get Single CV', function () {
        it('retrieves a specific CV', function () {
            $user = $this->createUser();

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
            $user1 = $this->createUser(['email' => 'user1@example.com']);
            $user2 = $this->createUser(['email' => 'user2@example.com']);

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
