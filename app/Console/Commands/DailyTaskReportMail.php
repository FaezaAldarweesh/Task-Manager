<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Jobs\GenerateDailyTaskReport;

class DailyTaskReportMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:send-daily-tasks-email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch the job to send daily tasks email to all users daily';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info('Starting scheduled command to dispatch GenerateDailyTaskReport job.');

        GenerateDailyTaskReport::dispatch();
        $this->info('Scheduled job to send daily tasks email dispatched.');

        Log::info('GenerateDailyTaskReport job successfully dispatched.');
    }
}
