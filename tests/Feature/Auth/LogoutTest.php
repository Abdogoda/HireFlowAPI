<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

describe('Logout Endpoint', function () {
    beforeEach(function () {
        Role::create(['name' => 'Admin', 'description' => 'Administrator']);
        Role::create(['name' => 'Recruiter', 'description' => 'Recruiter']);
        Role::create(['name' => 'Candidate', 'description' => 'Candidate']);
    });

    it('logs out authenticated user', function () {
        $candidateRole = Role::where('name', 'Candidate')->first();
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $candidateRole->id,
            'email_verified_at' => now(),
        ]);

        $token = $user->createToken('api-token')->plainTextToken;
        $initialTokenCount = $user->tokens()->count();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
                'timestamp',
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Logout successful',
            ]);

        // Verify token is deleted from database
        $user->refresh();
        $finalTokenCount = $user->tokens()->count();
        expect($finalTokenCount)->toBe(0);
        expect($initialTokenCount)->toBeGreaterThan($finalTokenCount);
    });

    it('fails when not authenticated', function () {
        $response = $this->postJson('/api/auth/logout');
        $response->assertStatus(401);
    });
});
