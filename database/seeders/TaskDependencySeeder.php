<?php

namespace Database\Seeders;

use App\Models\TaskDependency;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TaskDependencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TaskDependency::create([
            'task_id' => 2,
            'depends_on' => 1,
        ]);

        TaskDependency::create([
            'task_id' => 3,
            'depends_on' => 1,
        ]);

        TaskDependency::create([
            'task_id' => 4,
            'depends_on' => 1,
        ]);

        TaskDependency::create([
            'task_id' => 3,
            'depends_on' => 2,
        ]);

        TaskDependency::create([
            'task_id' => 4,
            'depends_on' => 3,
        ]);
    }
}
