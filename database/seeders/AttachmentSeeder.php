<?php

namespace Database\Seeders;

use App\Models\Attachment;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AttachmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Attachment::create([
            'name' => 'attachment1',
            'type' => 'docx',
            'path' => 'public/attachments/attachment1.docx',
            'mime_type' => 'application/pdf',
            'attachable_type' => 'App\Models\Task',
            'attachable_id' => 1,
            'user_id' => 1,
        ]);

        Attachment::create([
            'name' => 'attachment2',
            'type' => 'pdf',
            'path' => 'public/attachments/attachment2.pdf',
            'mime_type' => 'image/png',
            'attachable_type' => 'App\Models\Task',
            'attachable_id' => 2,
            'user_id' => 2,
        ]);
    }
}
