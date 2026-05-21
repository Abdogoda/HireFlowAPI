<?php

describe('Email Verification Endpoint', function () {
    beforeEach(function () {
        $this->createDefaultRoles();
    });

    it('verifies email with valid link', function () {
        $user = $this->createUserWithRole('Candidate', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'email_verified_at' => null,
        ]);

        $verificationUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->getJson($verificationUrl);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
                'timestamp',
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Email verified successfully',
            ]);

        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
    });

    it('fails with invalid hash', function () {
        $user = $this->createUserWithRole('Candidate', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'email_verified_at' => null,
        ]);

        $verificationUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => 'invalid-hash']
        );

        $response = $this->getJson($verificationUrl);
        $response->assertStatus(403);
    });

    it('already verified email cannot be verified again', function () {
        $user = $this->createUserWithRole('Candidate', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $verificationUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->getJson($verificationUrl);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
                'timestamp',
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Email already verified',
            ]);
    });

    it('unverified user cannot login', function () {
        $this->createUserWithRole('Candidate', [
            'name' => 'Unverified User',
            'email' => 'unverified@example.com',
            'email_verified_at' => null,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'unverified@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(403)
            ->assertJson(['message' => 'Please verify your email before logging in']);
    });

    it('cannot resend verification if already verified', function () {
        $user = $this->createUserWithRole('Candidate', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/auth/resend-verification-email');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Email already verified']);
    });
});
