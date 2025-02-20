<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\TaskService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Services\ApiResponseService;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TaskController extends Controller
{
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * Display a listing of tasks with optional filters.
     */
    public function index(Request $request)
    {
        try {
            $filters = [
                'type'        => $request->query('type'),
                'status'      => $request->query('status'),
                'assigned_to' => $request->query('assigned_to'),
                'due_date'    => $request->query('due_date'),
                'priority'    => $request->query('priority'),
                'depends_on'  => $request->query('depends_on'),
            ];

            $tasks = $this->taskService->listAllTasks($filters);
            return ApiResponseService::success(TaskResource::collection($tasks), 'Tasks retrieved successfully', 200);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }

    /**
     * Store a newly created task in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        $validated = $request->validated();
        try {
            $newTask = $this->taskService->createTask($validated);
            return ApiResponseService::success(new TaskResource($newTask), 'Task created successfully', 201);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }

    /**
     * Display the specified task.
     */
    public function show(string $id)
    {
        try {
            $task = $this->taskService->showTask($id);
            return ApiResponseService::success(new TaskResource($task), 'Task retrieved successfully', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponseService::error(null, 'Task not found.', 404);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }

    /**
     * Update the specified task in storage.
     */
    public function update(UpdateTaskRequest $request, string $id)
    {
        $validated = $request->validated();
        try {
            $updatedTask = $this->taskService->updateTask($id, $validated);
            return ApiResponseService::success(new TaskResource($updatedTask), 'Task updated successfully', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponseService::error(null, 'User not found.', 404);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }

    /**
     * Remove the specified task from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->taskService->deleteTask($id);
            return ApiResponseService::success(null, 'Task deleted successfully', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponseService::error(null, 'Task not found.', 404);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }

    /**
     * Display a listing of soft deleted tasks.
     */
    public function listDeletedTasks()
    {
        try {
            $tasks = $this->taskService->listAllDeletedTasks();
            return ApiResponseService::success(TaskResource::collection($tasks), 'Deleted tasks retrieved successfully.', 200);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }

    /**
     * Permanently delete a soft deleted task.
     */
    public function forceDeleteTask($id)
    {
        try {
            $this->taskService->forceDeleteTask($id);
            return ApiResponseService::success(null, 'Task permanently deleted.', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponseService::error(null, 'Task not found.', 404);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }

    /**
     * Restore a soft deleted task.
     */
    public function restoreTask($id)
    {
        try {
            $this->taskService->restoreTask($id);
            return ApiResponseService::success(null, 'Task restored successfully.', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponseService::error(null, 'Task not found.', 404);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }

    /**
     * Add a comment to a task.
     */
    public function addComment(Request $request, string $taskId)
    {
        $validatedData = $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        try {
            $newComment = $this->taskService->addCommentToTask($taskId, $validatedData['comment']);
            return ApiResponseService::success($newComment, 'Comment added successfully.', 201);
        } catch (ModelNotFoundException $e) {
            return ApiResponseService::error(null, 'Task not found.', 404);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }

    /**
     * Add an attachment to a task.
     */
    public function addAttachment(Request $request, string $taskId)
    {
        $validatedData = $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
        ]);

        try {
            $attachment = $this->taskService->addAttachment($taskId, $request->file('file'));
            return ApiResponseService::success($attachment, 'Attachment added successfully.', 201);
        } catch (ModelNotFoundException $e) {
            return ApiResponseService::error(null, 'Task not found.', 404);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }

    /**
     * Assign a task to a user.
     */
    public function assignTask(Request $request, string $id)
    {
        $validated = $request->validate([
            'assigned_to' => 'required|integer|exists:users,id',
        ]);

        try {
            $task = $this->taskService->assignTask($id, $validated['assigned_to']);
            return ApiResponseService::success(new TaskResource($task), 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponseService::error(null, 'Task not found.', 404);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }

    /**
     * Re-Assign a task to a user.
     */
    public function reassignTask(Request $request, string $id)
    {
        $validated = $request->validate([
            'assigned_to' => 'required|integer|exists:users,id',
        ]);

        try {
            $task = $this->taskService->reassignTask($id, $validated);
            return ApiResponseService::success(new TaskResource($task), 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponseService::error(null, 'Task not found.', 404);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }

    /**
     * Update Task Status
     */
    public function updateTaskStatus(Request $request, string $id)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:open,in_progress,completed,blocked',
        ]);

        try {
            $task = $this->taskService->updateTaskStatus($id, $validated);
            return ApiResponseService::success($task, 'Status updated successfully.', 201);
        } catch (ModelNotFoundException $e) {
            return ApiResponseService::error(null, 'Task not found.', 404);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }

    /**
     * List blocked and late tasks.
     */
    public function blockedAndLateTasks(Request $request)
    {
        try {
            $tasks = $this->taskService->listBlockedAndLateTasks();

            return ApiResponseService::success(TaskResource::collection($tasks), 'Blocked and late tasks retrieved successfully.', 200);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }
}
