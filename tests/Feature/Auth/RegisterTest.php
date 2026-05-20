<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

describe('Register Endpoint', function () {
    beforeEach(function () {
        Role::create(['name' => 'Admin', 'description' => 'Administrator']);
        Role::create(['name' => 'Recruiter', 'description' => 'Recruiter']);
        Role::create(['name' => 'Candidate', 'description' => 'Candidate']);
    });

    it('registers a new user with valid data', function () {
        $candidateRole = Role::where('name', 'Candidate')->first();

        $response = $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role_id' => $candidateRole->id,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['message', 'user' => ['id', 'name', 'email', 'role']])
            ->assertJson([
                'message' => 'User registered successfully. Please verify your email.',
                'user' => ['name' => 'John Doe', 'email' => 'john@example.com'],
            ]);

        $this->assertDatabaseHas('users', ['name' => 'John Doe', 'email' => 'john@example.com']);
    });

    it('fails with invalid email', function () {
        $candidateRole = Role::where('name', 'Candidate')->first();

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
        $candidateRole = Role::where('name', 'Candidate')->first();
        User::create([
            'name' => 'Existing User',
            'email' => 'existing@example.com',
            'password' => Hash::make('password'),
            'role_id' => $candidateRole->id,
        ]);

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
        $candidateRole = Role::where('name', 'Candidate')->first();

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
        $candidateRole = Role::where('name', 'Candidate')->first();

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
        $candidateRole = Role::where('name', 'Candidate')->first();

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
