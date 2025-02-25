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
            'name' => 'CEO',
            'description' => 'Administrator with full access'
        ]);
        $adminRole->assignPermissions([1, 2, 3, 4, 5, 6, 7, 8, 9]);

        $leaderRole = Role::create([
            'name' => 'Leader Manager',
            'description' => 'Leader Manager with full access'
        ]);
        $leaderRole->assignPermissions([1, 2, 3, 4, 5, 6, 7, 8, 9]);

        $developerRole = Role::create([
            'name' => 'Supervisor',
            'description' => 'Developer with limited access'
        ]);
        $developerRole->assignPermissions([4, 7, 8, 9]);

        $developerRole = Role::create([
            'name' => 'empolyee',
            'description' => 'Developer with limited access'
        ]);
        $developerRole->assignPermissions([7, 8, 9]);
    }
}
