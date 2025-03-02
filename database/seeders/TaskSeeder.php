<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Task::create([
            'title' => 'task one',
            'description' => 'description task one',
            'status' => 'open',
            'priority' => 'high',
            'due_date' => '2024-10-15',
            'assigned_to' => 1,
            'created_by' => 3,
        ]);

        Task::create([
            'title' => 'task two',
            'description' => 'description task two',
            'status' => 'open',
            'priority' => 'low',
            'due_date' => '2024-10-14',
            'assigned_to' => 1,
            'created_by' => 3,
        ]);

        Task::create([
            'title' => 'task three',
            'description' => 'description task three',
            'status' => 'in_progress',
            'priority' => 'high',
            'due_date' => '2024-10-13',
            'assigned_to' => 1,
            'created_by' => 3,
        ]);

        Task::create([
            'title' => 'task four',
            'description' => 'description task four',
            'status' => 'in_progress',
            'priority' => 'medium',
            'due_date' => '2024-10-12',
            'assigned_to' => 1,
            'created_by' => 3,
        ]);

        Task::create([
            'title' => 'task five',
            'description' => 'description task five',
            'status' => 'completed',
            'priority' => 'medium',
            'due_date' => '2024-10-11',
            'assigned_to' => 1,
            'created_by' => 3,
        ]);

        Task::create([
            'title' => 'task six',
            'description' => 'description task six',
            'status' => 'completed',
            'priority' => 'high',
            'due_date' => '2024-10-10',
            'assigned_to' => 1,
            'created_by' => 3,
        ]);




        Task::create([
            'title' => 'task seven',
            'description' => 'description task seven',
            'status' => 'open',
            'priority' => 'high',
            'due_date' => '2024-10-9',
            'assigned_to' => null,
            'created_by' => 1,
        ]);

        Task::create([
            'title' => 'task eight',
            'description' => 'description task eight',
            'status' => 'open',
            'priority' => 'low',
            'due_date' => '2024-10-8',
            'assigned_to' => null,
            'created_by' => 1,
        ]);

        Task::create([
            'title' => 'task nine',
            'description' => 'description task nine',
            'status' => 'in_progress',
            'priority' => 'medium',
            'due_date' => '2024-10-7',
            'assigned_to' => 3,
            'created_by' => 1,
        ]);

        Task::create([
            'title' => 'task ten',
            'description' => 'description task ten',
            'status' => 'in_progress',
            'priority' => 'low',
            'due_date' => '2024-10-6',
            'assigned_to' => 3,
            'created_by' => 1,
        ]);

        Task::create([
            'title' => 'task eleven',
            'description' => 'description task eleven',
            'status' => 'completed',
            'priority' => 'medium',
            'due_date' => '2024-10-5',
            'assigned_to' => 3,
            'created_by' => 1,
        ]);

        Task::create([
            'title' => 'task twelve',
            'description' => 'description task twelve',
            'status' => 'completed',
            'priority' => 'low',
            'due_date' => '2024-10-4',
            'assigned_to' => 3,
            'created_by' => 1,
        ]);
    }
}
