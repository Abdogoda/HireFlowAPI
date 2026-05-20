<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

describe('Email Verification Endpoint', function () {
    beforeEach(function () {
        Role::create(['name' => 'Admin', 'description' => 'Administrator']);
        Role::create(['name' => 'Recruiter', 'description' => 'Recruiter']);
        Role::create(['name' => 'Candidate', 'description' => 'Candidate']);
    });

    it('verifies email with valid link', function () {
        $candidateRole = Role::where('name', 'Candidate')->first();
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $candidateRole->id,
            'email_verified_at' => null,
        ]);

        $verificationUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->getJson($verificationUrl);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Email verified successfully']);

        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
    });

    it('fails with invalid hash', function () {
        $candidateRole = Role::where('name', 'Candidate')->first();
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $candidateRole->id,
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
        $candidateRole = Role::where('name', 'Candidate')->first();
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $candidateRole->id,
            'email_verified_at' => now(),
        ]);

        $verificationUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->getJson($verificationUrl);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Email already verified']);
    });

    it('unverified user cannot login', function () {
        $candidateRole = Role::where('name', 'Candidate')->first();
        User::create([
            'name' => 'Unverified User',
            'email' => 'unverified@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $candidateRole->id,
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
            ->postJson('/api/auth/resend-verification-email');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Email already verified']);
    });
});
