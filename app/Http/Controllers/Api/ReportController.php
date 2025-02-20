<?php

namespace App\Http\Controllers\Api;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\DailyTaskReportMail;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\ApiResponseService;
use Illuminate\Support\Facades\Mail;
use App\Jobs\GenerateDailyTaskReport;
use App\Jobs\GenerateTaskStatusReport;

class ReportController extends Controller
{
    /**
     * Dispatch the job to generate and send the daily task report email.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateDailyTaskReport()
    {
        Log::info('GenerateDailyTaskReport dispach from controller started.');
        try {

            GenerateDailyTaskReport::dispatch();
            Log::info('GenerateDailyTaskReport job dispatched via API.');

            return ApiResponseService::success(null, 'Daily task report email generation has been dispatched successfully', 200);
        } catch (\Exception $e) {
            Log::error('Error dispatching GenerateDailyTaskReport job: ' . $e->getMessage());
            return ApiResponseService::error(null, 'Failed to dispatch the job: ', 500);
        }
    }

}
