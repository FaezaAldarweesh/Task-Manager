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
            'name' => 'CEO',
            'email' => 'admin@gmail.com',
            'department_id' => null,
            'password' => '12345678',
            'phone' => '1234567890',
            'location' => 'location',
        ]);
        $admin->assignRoles([1]);


        //-------------------------------------------------

        $leader = User::create([
            'name' => 'Leader Manager',
            'email' => 'leader@gmail.com',
            'department_id' => null,
            'password' => '12345678',
            'phone' => '1234567890',
            'location' => 'location',
        ]);
        $leader->assignRoles([2]);


        //-------------------------------------------------


        $supervisorIt = User::create([
            'name' => 'Supervisor',
            'email' => 'supervisorIt@gmail.com',
            'department_id' => 1,
            'password' => '12345678',
            'phone' => '1234567890',
            'location' => 'location',

        ]);
        $supervisorIt->assignRoles([3]);

        $supervisorFin = User::create([
            'name' => 'Supervisor',
            'email' => 'supervisorFin@gmail.com',
            'department_id' => 2,
            'password' => '12345678',
            'phone' => '1234567890',
            'location' => 'location',
        ]);
        $supervisorFin->assignRoles([3]);


        //-------------------------------------------------

        $empolyee1 = User::create([
            'name' => 'empolyee1',
            'email' => 'empolyee1@gmail.com',
            'department_id' => 1,
            'password' => '12345678',
            'phone' => '1234567890',
            'location' => 'location',
        ]);
        $empolyee1->assignRoles([4]);

        $empolyee2 = User::create([
            'name' => 'empolyee2',
            'email' => 'empolyee2@gmail.com',
            'department_id' => 1,
            'password' => '12345678',
            'phone' => '1234567890',
            'location' => 'location',
        ]);
        $empolyee2->assignRoles([4]);

        
        $empolyee3 = User::create([
            'name' => 'empolyee3',
            'email' => 'empolyee3@gmail.com',
            'department_id' => 2,
            'password' => '12345678',
            'phone' => '1234567890',
            'location' => 'location',
        ]);
        $empolyee3->assignRoles([4]);

        $empolyee4 = User::create([
            'name' => 'empolyee4',
            'email' => 'empolyee4@gmail.com',
            'department_id' => 2,
            'password' => '12345678',
            'phone' => '1234567890',
            'location' => 'location',
        ]);
        $empolyee4->assignRoles([4]);
    }
}
