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
        //1
        Permission::create([
            'name' => 'permission',
            'description' => 'All permission permissions'
        ]);

        //2
        Permission::create([
            'name' => 'role',
            'description' => 'All role permissions'
        ]);

        //3
        Permission::create([
            'name' => 'department',
            'description' => 'All department permissions'
        ]);

        //4
        Permission::create([
            'name' => 'user',
            'description' => 'All user permissions'
        ]);

        //5
        Permission::create([
            'name' => 'add_task',
            'description' => 'add task permissions'
        ]);

        //6
        Permission::create([
            'name' => 'list_task',
            'description' => 'list task permissions'
        ]);

        //7
        Permission::create([
            'name' => 'view_task',
            'description' => 'view task permissions'
        ]);

        //8
        Permission::create([
            'name' => 'update_task',
            'description' => 'update task permissions'
        ]);

        //9
        Permission::create([
            'name' => 'soft_delete_task',
            'description' => 'soft delete task permissions'
        ]);

        //10
        Permission::create([
            'name' => 'list_delete_task',
            'description' => 'list delete task permissions'
        ]);

        //11
        Permission::create([
            'name' => 'restor_delete_task',
            'description' => 'restor delete task permissions'
        ]);

        //12
        Permission::create([
            'name' => 'force_delete_task',
            'description' => 'force delete task permissions'
        ]);

        //13
        Permission::create([
            'name' => 'comment',
            'description' => 'Comment operations'
        ]);

        //14
        Permission::create([
            'name' => 'attachment',
            'description' => 'Attachment operations'
        ]);

        //15
        Permission::create([
            'name' => 'assign_task',
            'description' => 'assign task to user'
        ]);

        //16
         Permission::create([
            'name' => 'update_status',
            'description' => 'update task status'
        ]);

        //17
        Permission::create([
            'name' => 'log_task',
            'description' => 'view all the updates on the task'
        ]);
    }
}
