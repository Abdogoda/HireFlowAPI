<?php

describe('Logout Endpoint', function () {
    beforeEach(function () {
        $this->createDefaultRoles();
    });

    it('logs out authenticated user', function () {
        $user = $this->createUserWithRole('Candidate', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
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
