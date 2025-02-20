<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('task_status_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('previous_status', ['open', 'in_Progress', 'completed', 'blocked'])->nullable();
            $table->enum('new_status', ['open', 'in_Progress', 'completed', 'blocked']);
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['task_id', 'user_id']);
            $table->index(['previous_status']);
            $table->index(['new_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_status_updates');
    }
};
