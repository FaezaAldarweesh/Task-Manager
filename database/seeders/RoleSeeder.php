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
        $adminRole->assignPermissions([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17]);

        $leaderRole = Role::create([
            'name' => 'Leader Manager',
            'description' => 'Leader Manager with full access'
        ]);
        $leaderRole->assignPermissions([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17]);

        $developerRole = Role::create([
            'name' => 'Supervisor',
            'description' => 'Developer with limited access'
        ]);
        $developerRole->assignPermissions([5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17]);

        $developerRole = Role::create([
            'name' => 'empolyee',
            'description' => 'Developer with limited access'
        ]);
        $developerRole->assignPermissions([6, 7, 13, 14, 16]);
    }
}
