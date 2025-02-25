<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\TaskService;
use App\Services\AttachmentService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Services\ApiResponseService;
use App\Http\Resources\CommentResource;
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
                'status'      => $request->query('status'),
                'assigned_to' => $request->query('assigned_to'),
                'due_date'    => $request->query('due_date'),
                'priority'    => $request->query('priority'),
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
        }  catch (\Exception $e) {
            Log::error('Task store failed: ' . $e->getMessage());
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
            return ApiResponseService::success(new TaskResource($task), 'Status updated successfully.', 201);
        } catch (ModelNotFoundException $e) {
            return ApiResponseService::error(null, 'Task not found.', 404);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }


    /**
     * all comments on the task.
     */
    public function allComment(string $taskId)
    {
        try {
            $allComments = $this->taskService->allCommentToTask($taskId);
            return ApiResponseService::success(CommentResource::collection($allComments), 'Comments retrieved successfully.', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponseService::error(null, 'Comments not found.', 404);
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
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
        ]);

        try {
            $newComment = $this->taskService->addCommentToTask($taskId, $validatedData['comment'], $validatedData['file']);
            return ApiResponseService::success(new CommentResource($newComment), 'Comment added successfully.', 201);
        } catch (ModelNotFoundException $e) {
            return ApiResponseService::error(null, 'Comment not found.', 404);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }

    /**
     * update a comment to a task.
     */
    public function updateComment(Request $request,string $CommentId)
    {
        $validatedData = $request->validate([
            'comment' => 'nullable|string|max:1000',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
        ]);

        try {
            $updateComment = $this->taskService->updateCommentToTask( $CommentId, $validatedData['comment'],$validatedData['file']);
            return ApiResponseService::success(new CommentResource($updateComment), 'Comment update successfully.', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponseService::error(null, 'Comment not found.', 404);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }

    /**
     * delete a comment to a task.
     */
    public function destroyComment(string $CommentId)
    {
        try {
            $destroyComment = $this->taskService->destroyCommentToTask($CommentId);
            return ApiResponseService::success(null, 'Comment destroy successfully.', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponseService::error(null, 'Comment not found.', 404);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }


    /**
     * delete a file.
     */
    public function destroyattachment(string $attachmentid)
    {
        try {
            $attachmentService = new AttachmentService();
            $destroyattachment = $attachmentService->deleteAttachment($attachmentid);
            return ApiResponseService::success(null, 'attachment destroy successfully.', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponseService::error(null, 'attachment not found.', 404);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }
}
