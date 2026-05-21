<?php

namespace Tests;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Hash;

abstract class TestCase extends BaseTestCase
{
    /**
     * Create default roles (Admin, Recruiter, Candidate)
     */
    protected function createDefaultRoles(): array
    {
        return [
            'admin' => Role::create(['name' => 'Admin', 'description' => 'Administrator']),
            'recruiter' => Role::create(['name' => 'Recruiter', 'description' => 'Recruiter']),
            'candidate' => Role::create(['name' => 'Candidate', 'description' => 'Candidate']),
        ];
    }

    /**
     * Get a role by name
     */
    protected function getRole(string $name): Role
    {
        return Role::where('name', $name)->firstOrCreate(
            ['name' => $name],
            ['description' => $name]
        );
    }

    /**
     * Create a user with default data
     */
    protected function createUser(array $attributes = []): User
    {
        $defaults = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $this->getRole('Candidate')->id,
            'email_verified_at' => now(),
        ];

        return User::create(array_merge($defaults, $attributes));
    }

    /**
     * Create and authenticate a user
     */
    protected function authenticateUser(array $attributes = []): User
    {
        $user = $this->createUser($attributes);
        $this->actingAs($user);
        return $user;
    }

    /**
     * Create a user with a specific role
     */
    protected function createUserWithRole(string $roleName, array $attributes = []): User
    {
        $role = $this->getRole($roleName);
        return $this->createUser(array_merge($attributes, ['role_id' => $role->id]));
    }

    /**
     * Create and authenticate a user with a specific role
     */
    protected function authenticateUserWithRole(string $roleName, array $attributes = []): User
    {
        $user = $this->createUserWithRole($roleName, $attributes);
        $this->actingAs($user);
        return $user;
    }
}