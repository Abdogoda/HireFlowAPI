<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

describe('Reset Password Endpoint', function () {
    beforeEach(function () {
        Role::create(['name' => 'Admin', 'description' => 'Administrator']);
        Role::create(['name' => 'Recruiter', 'description' => 'Recruiter']);
        Role::create(['name' => 'Candidate', 'description' => 'Candidate']);
    });

    it('resets password with valid token', function () {
        $candidateRole = Role::where('name', 'Candidate')->first();
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('oldpassword'),
            'role_id' => $candidateRole->id,
            'email_verified_at' => now(),
        ]);

        $token = \Illuminate\Support\Facades\Password::createToken($user);

        $response = $this->postJson('/api/auth/reset-password', [
            'token' => $token,
            'email' => 'john@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
                'timestamp',
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Password reset successfully',
            ]);

        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    });

    it('fails with invalid token', function () {
        $candidateRole = Role::where('name', 'Candidate')->first();
        User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $candidateRole->id,
            'email_verified_at' => now(),
        ]);

        $response = $this->postJson('/api/auth/reset-password', [
            'token' => 'invalid-token',
            'email' => 'john@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(400);
    });

    it('fails when passwords do not match', function () {
        $candidateRole = Role::where('name', 'Candidate')->first();
        User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $candidateRole->id,
            'email_verified_at' => now(),
        ]);

        $token = \Illuminate\Support\Facades\Password::createToken(User::first());

        $response = $this->postJson('/api/auth/reset-password', [
            'token' => $token,
            'email' => 'john@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'different',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors('password');
    });
});
