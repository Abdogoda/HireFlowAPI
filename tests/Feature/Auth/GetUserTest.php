<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

describe('Get User Endpoint', function () {
    beforeEach(function () {
        Role::create(['name' => 'Admin', 'description' => 'Administrator']);
        Role::create(['name' => 'Recruiter', 'description' => 'Recruiter']);
        Role::create(['name' => 'Candidate', 'description' => 'Candidate']);
    });

    it('returns current authenticated user', function () {
        $candidateRole = Role::where('name', 'Candidate')->first();
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $candidateRole->id,
            'email_verified_at' => now(),
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/user');

        $response->assertStatus(200)
            ->assertJsonStructure(['id', 'name', 'email', 'role'])
            ->assertJson(['name' => 'John Doe', 'email' => 'john@example.com']);
    });

    it('fails when not authenticated', function () {
        $response = $this->getJson('/api/user');
        $response->assertStatus(401);
    });

    it('password is hidden in API responses', function () {
        $candidateRole = Role::where('name', 'Candidate')->first();
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $candidateRole->id,
            'email_verified_at' => now(),
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/user');

        $response->assertStatus(200);
        expect(array_key_exists('password', $response->json()))->toBeFalse();
    });
});
