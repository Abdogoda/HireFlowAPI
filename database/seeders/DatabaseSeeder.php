<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles first
        $this->call(RoleSeeder::class);

        // Get roles
        $adminRole = Role::where('name', 'Admin')->first();
        $recruiterRole = Role::where('name', 'Recruiter')->first();
        $candidateRole = Role::where('name', 'Candidate')->first();

        // Create test users with different roles
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role_id' => $adminRole?->id,
        ]);

        User::factory()->create([
            'name' => 'Recruiter User',
            'email' => 'recruiter@example.com',
            'role_id' => $recruiterRole?->id,
        ]);

        User::factory()->create([
            'name' => 'Candidate User',
            'email' => 'candidate@example.com',
            'role_id' => $candidateRole?->id,
        ]);
    }
}

