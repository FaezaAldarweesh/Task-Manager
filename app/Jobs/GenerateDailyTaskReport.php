<?php

namespace App\Jobs;

use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use App\Mail\DailyTaskReportMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class GenerateDailyTaskReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * Fetch tasks with status updates in the last 24 hours
     * Send an email to each user with their tasks
     * 
     * @return void
     */
    public function handle()
    {
        try {
            Log::info('GenerateDailyTaskReport job started.');

            $tasks = Task::whereHas('statusUpdates', function ($query) {
                $query->where('created_at', '>=', now()->subDay());
            })->with(['statusUpdates', 'assignedTo'])->get();

            // Group tasks by assigned user
            $userTasks = $tasks->groupBy('assigned_to');

            foreach ($userTasks as $userId => $tasks) {
                $user = User::find($userId);
                if ($user) {
                    Mail::to($user->email)->send(new DailyTaskReportMail($user, $tasks));
                }
            }

            Log::info('GenerateDailyTaskReport job completed.');
        } catch (\Exception $e) {
            Log::error('Error in GenerateDailyTaskReport job: ' . $e->getMessage());
        }
    }
}
