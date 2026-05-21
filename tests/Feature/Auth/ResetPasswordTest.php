<?php

use Illuminate\Support\Facades\Hash;

describe('Reset Password Endpoint', function () {
    beforeEach(function () {
        $this->createDefaultRoles();
    });

    it('resets password with valid token', function () {
        $user = $this->createUserWithRole('Candidate', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('oldpassword'),
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
        $this->createUserWithRole('Candidate', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
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
        $user = $this->createUserWithRole('Candidate', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $token = \Illuminate\Support\Facades\Password::createToken($user);

        $response = $this->postJson('/api/auth/reset-password', [
            'token' => $token,
            'email' => 'john@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'different',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors('password');
    });
});
