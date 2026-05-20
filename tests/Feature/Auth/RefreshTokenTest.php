<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

describe('Refresh Token Endpoint', function () {
    beforeEach(function () {
        Role::create(['name' => 'Admin', 'description' => 'Administrator']);
        Role::create(['name' => 'Recruiter', 'description' => 'Recruiter']);
        Role::create(['name' => 'Candidate', 'description' => 'Candidate']);
    });

    it('refreshes user token', function () {
        $candidateRole = Role::where('name', 'Candidate')->first();
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $candidateRole->id,
            'email_verified_at' => now(),
        ]);

        $oldToken = $user->createToken('api-token')->plainTextToken;
        $initialTokenCount = $user->tokens()->count();

        $response = $this->withHeader('Authorization', "Bearer $oldToken")
            ->postJson('/api/auth/refresh-token');

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'token'])
            ->assertJson(['message' => 'Token refreshed successfully']);

        $newToken = $response->json('token');
        $this->assertNotEmpty($newToken);
        $this->assertNotSame($oldToken, $newToken);

        // Verify token count is still 1 (old token deleted, new token created)
        $user->refresh();
        $finalTokenCount = $user->tokens()->count();
        expect($finalTokenCount)->toBe(1);

        // New token should work
        $this->withHeader('Authorization', "Bearer $newToken")
            ->getJson('/api/user')
            ->assertStatus(200);
    });

    it('fails when not authenticated', function () {
        $response = $this->postJson('/api/auth/refresh-token');
        $response->assertStatus(401);
    });

    it('user can have multiple valid tokens', function () {
        $candidateRole = Role::where('name', 'Candidate')->first();
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $candidateRole->id,
            'email_verified_at' => now(),
        ]);

        $token1 = $user->createToken('device1')->plainTextToken;
        $token2 = $user->createToken('device2')->plainTextToken;

        $response1 = $this->withHeader('Authorization', "Bearer $token1")->getJson('/api/user');
        $response2 = $this->withHeader('Authorization', "Bearer $token2")->getJson('/api/user');

        $response1->assertStatus(200);
        $response2->assertStatus(200);
    });

    it('refreshing token only revokes current token', function () {
        $candidateRole = Role::where('name', 'Candidate')->first();
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $candidateRole->id,
            'email_verified_at' => now(),
        ]);

        $token1 = $user->createToken('device1')->plainTextToken;
        $token2 = $user->createToken('device2')->plainTextToken;
        $initialTokenCount = $user->tokens()->count();
        expect($initialTokenCount)->toBe(2);

        // Refresh token1
        $response = $this->withHeader('Authorization', "Bearer $token1")
            ->postJson('/api/auth/refresh-token')
            ->assertStatus(200);

        // After refresh: token1 deleted, new token created, token2 still exists = 2 tokens
        $user->refresh();
        $afterRefreshTokenCount = $user->tokens()->count();
        expect($afterRefreshTokenCount)->toBe(2);

        // Token2 should still work
        $this->withHeader('Authorization', "Bearer $token2")
            ->getJson('/api/user')
            ->assertStatus(200);

        $newToken = $response->json('token');
        // New token should work
        $this->withHeader('Authorization', "Bearer $newToken")
            ->getJson('/api/user')
            ->assertStatus(200);
    });
});
