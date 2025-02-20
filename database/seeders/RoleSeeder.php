<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::create([
            'name' => 'admin',
            'description' => 'Administrator with full access'
        ]);
        $adminRole->assignPermissions([1, 2, 3, 4, 5]);

        $leaderRole = Role::create([
            'name' => 'leader',
            'description' => 'Team Leader with full task persmissions'
        ]);
        $leaderRole->assignPermissions([2, 3, 4, 5]);

        $developerRole = Role::create([
            'name' => 'developer',
            'description' => 'Developer with limited access'
        ]);
        $developerRole->assignPermissions([3, 4, 5]);
    }
}
