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
        ]);
        $admin->assignRoles([1]);


        //-------------------------------------------------

        $leader = User::create([
            'name' => 'Leader Manager',
            'email' => 'leader@gmail.com',
            'department_id' => null,
            'password' => '12345678',
        ]);
        $leader->assignRoles([2]);


        //-------------------------------------------------


        $supervisorIt = User::create([
            'name' => 'Supervisor',
            'email' => 'supervisorIt@gmail.com',
            'department_id' => 1,
            'password' => '12345678',
        ]);
        $supervisorIt->assignRoles([3]);

        $supervisorFin = User::create([
            'name' => 'Supervisor',
            'email' => 'supervisorFin@gmail.com',
            'department_id' => 2,
            'password' => '12345678',
        ]);
        $supervisorFin->assignRoles([3]);


        //-------------------------------------------------

        $empolyee1 = User::create([
            'name' => 'empolyee1',
            'email' => 'empolyee1@gmail.com',
            'department_id' => 1,
            'password' => '12345678',
        ]);
        $empolyee1->assignRoles([3]);

        $empolyee2 = User::create([
            'name' => 'empolyee2',
            'email' => 'empolyee2@gmail.com',
            'department_id' => 1,
            'password' => '12345678',
        ]);
        $empolyee2->assignRoles([3]);

        
        $empolyee3 = User::create([
            'name' => 'empolyee3',
            'email' => 'empolyee3@gmail.com',
            'department_id' => 2,
            'password' => '12345678',
        ]);
        $empolyee3->assignRoles([3]);

        $empolyee4 = User::create([
            'name' => 'empolyee4',
            'email' => 'empolyee4@gmail.com',
            'department_id' => 2,
            'password' => '12345678',
        ]);
        $empolyee4->assignRoles([3]);
    }
}
