<?php

namespace Database\Seeders;

use App\Models\Comment;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Comment::create([
            'commentable_type' => 'App\Models\Task',
            'commentable_id' => 1,
            'comment' => 'comment1',
            'user_id' => 1,
        ]);

        Comment::create([
            'commentable_type' => 'App\Models\Task',
            'commentable_id' => 2,
            'comment' => 'comment2',
            'user_id' => 2,
        ]);

        Comment::create([
            'commentable_type' => 'App\Models\Task',
            'commentable_id' => 3,
            'comment' => 'comment2',
            'user_id' => 3,
        ]);

        Comment::create([
            'commentable_type' => 'App\Models\Task',
            'commentable_id' => 4,
            'comment' => 'comment2',
            'user_id' => 4,
        ]);
    }
}
