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
            'title' => 'Fix bug in authentication',
            'description' => 'Resolve the login bug for the application.',
            'type' => 'bug',
            'status' => 'open',
            'priority' => 'high',
            'due_date' => '2024-10-15',
            'assigned_to' => 3,
            'created_by' => 1,
        ]);

        Task::create([
            'title' => 'Add new feature to dashboard',
            'description' => 'Implement analytics on the dashboard for user tracking.',
            'type' => 'feature',
            'status' => 'in_Progress',
            'priority' => 'medium',
            'due_date' => '2024-11-01',
            'assigned_to' => 4,
            'created_by' => 2,
        ]);

        Task::create([
            'title' => 'Improve performance of the API',
            'description' => 'Optimize the database queries to improve the response time.',
            'type' => 'improvement',
            'status' => 'completed',
            'priority' => 'low',
            'due_date' => '2024-09-30',
            'assigned_to' => 3,
            'created_by' => 2,
        ]);

        Task::create([
            'title' => 'Resolve issue with blocked tasks',
            'description' => 'Investigate and fix the issue causing tasks to be blocked incorrectly.',
            'type' => 'bug',
            'status' => 'blocked',
            'priority' => 'high',
            'due_date' => '2020-10-20',
            'assigned_to' => null,
            'created_by' => 2,
        ]);
    }
}
