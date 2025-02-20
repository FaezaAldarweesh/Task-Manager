<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TaskStatusUpdate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TaskStatusUpdateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TaskStatusUpdate::create([
            'task_id' => 1,
            'user_id' => 1,
            'previous_status' => 'open',
            'new_status' => 'in_Progress',
        ]);

        TaskStatusUpdate::create([
            'task_id' => 2,
            'user_id' => 2,
            'previous_status' => 'open',
            'new_status' => 'in_Progress',
        ]);
    }
}
