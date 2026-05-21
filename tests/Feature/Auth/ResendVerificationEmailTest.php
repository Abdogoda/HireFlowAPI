<?php

describe('Resend Verification Email Endpoint', function () {
    beforeEach(function () {
        $this->createDefaultRoles();
    });

    it('resends verification email to unverified user', function () {
        $user = $this->createUserWithRole('Candidate', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'email_verified_at' => null,
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/auth/resend-verification-email');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
                'timestamp',
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Verification email sent',
            ]);
    });

    it('fails when not authenticated', function () {
        $response = $this->postJson('/api/auth/resend-verification-email');
        $response->assertStatus(401);
    });
});
