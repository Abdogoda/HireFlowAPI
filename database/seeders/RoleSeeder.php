<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeders.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Admin',
                'description' => 'Administrator with full system access',
            ],
            [
                'name' => 'Recruiter',
                'description' => 'Recruiter who can post jobs and manage candidates',
            ],
            [
                'name' => 'Candidate',
                'description' => 'Job candidate who can apply for positions',
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role['name']],
                ['description' => $role['description']]
            );
        }
    }
}
