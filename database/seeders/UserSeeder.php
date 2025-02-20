<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => '12345678',
        ]);
        $admin->assignRoles([1]);

        $leader = User::create([
            'name' => 'Team Leader',
            'email' => 'leader@gmail.com',
            'password' => '12345678',
        ]);
        $leader->assignRoles([2]);

        $developer1 = User::create([
            'name' => 'Developer',
            'email' => 'developer1@gmail.com',
            'password' => '12345678',
        ]);
        $developer1->assignRoles([3]);

        $developer2 = User::create([
            'name' => 'Developer',
            'email' => 'developer2@gmail.com',
            'password' => '12345678',
        ]);
        $developer2->assignRoles([3]);
    }
}
