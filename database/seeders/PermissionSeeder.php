<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::create([
            'name' => 'full_access',
            'description' => 'Full access for administrator'
        ]);

        Permission::create([
            'name' => 'role',
            'description' => 'All role permissions'
        ]);

        Permission::create([
            'name' => 'permission',
            'description' => 'All permission permissions'
        ]);

        Permission::create([
            'name' => 'task',
            'description' => 'All task permissions'
        ]);

        Permission::create([
            'name' => 'department',
            'description' => 'All department permissions'
        ]);

        Permission::create([
            'name' => 'user',
            'description' => 'All user permissions'
        ]);

        Permission::create([
            'name' => 'status',
            'description' => 'Change task status'
        ]);

        Permission::create([
            'name' => 'comment',
            'description' => 'Comment operations'
        ]);

        Permission::create([
            'name' => 'attachment',
            'description' => 'Attachment operations'
        ]);
    }
}
