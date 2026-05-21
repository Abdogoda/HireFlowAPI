<?php

describe('Login Endpoint', function () {
    beforeEach(function () {
        $this->createDefaultRoles();
    });

    it('logs in a user with verified email', function () {
        $user = $this->createUserWithRole('Candidate', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => ['id', 'name', 'email', 'role'],
                    'token',
                ],
                'timestamp',
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => [
                        'name' => 'John Doe',
                        'email' => 'john@example.com',
                    ],
                ],
            ]);

        $this->assertNotEmpty($response->json('data.token'));
    });

    it('fails with unverified email', function () {
        $this->createUserWithRole('Candidate', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'email_verified_at' => null,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(403)
            ->assertJson(['message' => 'Please verify your email before logging in']);
    });

    it('fails with invalid credentials', function () {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(401)->assertJson(['message' => 'Invalid credentials']);
    });

    it('fails with wrong password', function () {
        $this->createUserWithRole('Candidate', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'john@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)->assertJson(['message' => 'Invalid credentials']);
    });
});