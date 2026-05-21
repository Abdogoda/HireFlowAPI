<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

describe('Register Endpoint', function () {
    beforeEach(function () {
        $this->createDefaultRoles();
    });

    it('registers a new user with valid data', function () {
        $candidateRole = $this->getRole('Candidate');

        $response = $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role_id' => $candidateRole->id,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => ['id', 'name', 'email', 'role'],
                ],
                'timestamp',
            ])
            ->assertJson([
                'success' => true,
                'message' => 'User registered successfully. Please verify your email.',
                'data' => [
                    'user' => ['name' => 'John Doe', 'email' => 'john@example.com'],
                ],
            ]);

        $this->assertDatabaseHas('users', ['name' => 'John Doe', 'email' => 'john@example.com']);
    });

    it('fails with invalid email', function () {
        $candidateRole = $this->getRole('Candidate');

        $response = $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role_id' => $candidateRole->id,
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors('email');
    });

    it('fails when email already exists', function () {
        $this->createUserWithRole('Candidate', [
            'name' => 'Existing User',
            'email' => 'existing@example.com',
        ]);

        $candidateRole = $this->getRole('Candidate');

        $response = $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role_id' => $candidateRole->id,
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors('email');
    });

    it('fails when passwords do not match', function () {
        $candidateRole = $this->getRole('Candidate');

        $response = $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different',
            'role_id' => $candidateRole->id,
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors('password');
    });

    it('fails when password is too short', function () {
        $candidateRole = $this->getRole('Candidate');

        $response = $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
            'role_id' => $candidateRole->id,
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors('password');
    });

    it('fails with invalid role', function () {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role_id' => 999,
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors('role_id');
    });

    it('password is hashed in database', function () {
        $candidateRole = $this->getRole('Candidate');

        $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role_id' => $candidateRole->id,
        ]);

        $user = User::where('email', 'john@example.com')->first();
        expect(Hash::check('password123', $user->password))->toBeTrue();
        expect($user->password)->not->toBe('password123');
    });
});
