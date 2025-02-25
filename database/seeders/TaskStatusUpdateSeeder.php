<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TaskStatusUpdate;

class TaskStatusUpdateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TaskStatusUpdate::create([
            'assigned_to' => 3, 
            'created_by' => 1,
            'task_id' => 1,
            'user_id' => 5,
            'previous_status' => 'open',
            'new_status' => 'in_Progress',
        ]);

        TaskStatusUpdate::create([
            'assigned_to' => 7, 
            'created_by' => 2,
            'task_id' => 2,
            'user_id' => 2,
            'previous_status' => 'open',
            'new_status' => 'in_Progress',
        ]);
    }
}
