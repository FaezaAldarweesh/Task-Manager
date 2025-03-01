<?php

namespace App\Services;

use App\Models\Attachment;
use App\Models\Comment;
use Carbon\Carbon;
use App\Models\Task;
use Illuminate\Support\Facades\Log;
//use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TaskService
{
    /**
     * Retrieve all tasks with pagination and optional filters using model scopes.
     * 
     * @param array $filters
     * @throws \Exception
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function listAllTasks(array $filters = [])
    {
        try {
            $query = Task::query();

            return $query->status($filters['status'] ?? null)
                ->assignedTo($filters['assigned_to'] ?? null)
                ->dueDate($filters['due_date'] ?? null)
                ->priority($filters['priority'] ?? null)
                ->get();

        } catch (\Exception $e) {
            Log::error('Failed to retrieve tasks: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }

    /**
     * Create a new task with the provided data.
     *
     * Log the initial status with auth user_id, i dont add previous status when creating a new task
     * Sync dependencies if provided
     * Invalidate the cache for tasks
     * 
     * @param array $data
     * @throws \Exception
     * @return Task|\Illuminate\Database\Eloquent\Model
     */
    public function createTask(array $data, $file)
    {
        try {
            $task = Task::create($data);
            
            $task->statusUpdates()->create([
                'assigned_to' => $task->assigned_to, 
                'created_by' => $task->created_by,
                'previous_status' => null,
                'new_status' => $data['status'],
                'user_id' => auth()->id(),
            ]);

            $attachmentService = new AttachmentService();
            $attachment = $attachmentService->storeAttachment($file , Task::class , $task->id);

            $task->attachments()->save($attachment);

            return $task->load('attachments');
        } catch (\Exception $e) {
            Log::error('Task creation failed: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }

    /**
     * Retrieve a single task.
     * 
     * @param string $id
     * @throws \Exception
     * @return Task
     */
    public function showTask(string $id)
    {
        try {
            $task = Task::findOrFail($id);
            $task->load('comments', 'attachments', 'statusUpdates');

            return $task;
        } catch (ModelNotFoundException $e) {
            Log::error('Task not found: ' . $e->getMessage());
            throw new \Exception('Task not found.');
        } catch (\Exception $e) {
            Log::error('Failed to retrieve task: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }

    /**
     * Update an existing task with the provided data.
     * 
     * Firstly i get the old status, then update the task
     * Check if the status is changing, then log the changes
     * Handle dependent tasks based on the new status
     * Sync dependencies if provided
     * 
     * @param string $id
     * @param array $data
     * @throws \Exception
     * @return Task
     */
    public function updateTask(string $id, array $data, $file)
    {
        try {
            $task = Task::findOrFail($id);
            $oldStatus = $task->status;

            $task->update(array_filter($data));

            if (isset($data['status']) && $data['status'] !== $oldStatus) {
                $task->statusUpdates()->create([
                    'assigned_to' => $task->assigned_to, 
                    'created_by' => $task->created_by,
                    'previous_status' => $oldStatus,
                    'new_status' => $data['status'],
                    'user_id' => auth()->id(),
                ]);

            $attachmentService = new AttachmentService();
            $attachment = $attachmentService->storeAttachment($file , Task::class , $task->id);

            $task->attachments()->save($attachment);

            $this->changeTaskStatus($data, $task);
            }

            return $task->load('attachments');
        } catch (ModelNotFoundException $e) {
            Log::error('Task not found: ' . $e->getMessage());
            throw new \Exception('Task not found.');
        } catch (\Exception $e) {
            Log::error('Failed to update task: ' . $e->getMessage());
            throw new \Exception('An error occurred while updating the task.');
        }
    }

    /**
     * Delete a task.
     * 
     * @param string $id
     * @throws \Exception
     * @return bool
     */
    public function deleteTask(string $id)
    {
        try {
            $task = Task::findOrFail($id);
            $task->comments()->delete();
            $task->attachments()->delete();
            return $task->delete();
        } catch (ModelNotFoundException $e) {
            Log::error('Task not found: ' . $e->getMessage());
            throw new \Exception('Task not found.');
        } catch (\Exception $e) {
            Log::error('Failed to delete task: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }

    /**
     * Show soft deleted tasks.
     * 
     * @throws \Exception
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function listAllDeletedTasks()
    {
        try {
            return Task::onlyTrashed()->paginate(5);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve deleted tasks: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }

    /**
     * Force delete a task (permanently delete).
     * 
     * @param string $id
     * @throws \Exception
     * @return bool|null
     */
    public function forceDeleteTask(string $id)
    {
        try {
            $task = Task::onlyTrashed()->findOrFail($id);
            $task->comments()->forceDelete();
            $task->attachments()->forceDelete();
            return $task->forceDelete();
        } catch (ModelNotFoundException $e) {
            Log::error('Task not found: ' . $e->getMessage());
            throw new \Exception('Task not found.');
        } catch (\Exception $e) {
            Log::error('Failed to force delete task: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }

    /**
     * Restore a soft deleted task.
     * 
     * @param string $id
     * @throws \Exception
     * @return bool|null
     */
    public function restoreTask(string $id)
    {
        try {
            $task = Task::onlyTrashed()->findOrFail($id);
            $task->comments()->restore();
            $task->attachments()->restore();
            return $task->restore();
        } catch (ModelNotFoundException $e) {
            Log::error('Task not found: ' . $e->getMessage());
            throw new \Exception('Task not found.');
        } catch (\Exception $e) {
            Log::error('Failed to restore task: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }

    /**
     * Assign a task to a user.
     * 
     * @param string $id
     * @param string $userId
     * @throws \Exception
     * @return Task|Task[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function assignTask(string $id, string $userId)
    {
        try {
            $task = Task::findOrFail($id);
            $task->update(['assigned_to' => $userId]);

            $task->statusUpdates()->create([
                'assigned_to' => $userId, 
                'created_by' => $task->created_by,
                'previous_status' => $task->previous_status,
                'new_status' => $task->status,
                'user_id' => auth()->id(),
            ]);

            return $task;
        } catch (ModelNotFoundException $e) {
            Log::error('Task not found: ' . $e->getMessage());
            throw new \Exception('Task not found.');
        } catch (\Exception $e) {
            Log::error('Failed to assign task: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }

    /**
     * Re-Assign Task
     * 
     * @param string $id
     * @param array $data
     * @throws \Exception
     * @return Task|Task[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function reassignTask(string $id, array $data)
    {
        try {
            $task = Task::findOrFail($id);
            $task->assigned_to = $data['assigned_to'];
            $task->save();

            $task->statusUpdates()->create([
                'assigned_to' => $data['assigned_to'], 
                'created_by' => $task->created_by,
                'previous_status' => $task->previous_status,
                'new_status' => $task->status,
                'user_id' => auth()->id(),
            ]);

            return $task;
        } catch (ModelNotFoundException $e) {
            Log::error('Task not found: ' . $e->getMessage());
            throw new \Exception('Task not found.');
        } catch (\Exception $e) {
            Log::error('Failed to reassign task: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }

    /**
     * Change the status of a task, log changes, and handle dependent tasks accordingly.
     * 
     * @param array $data
     * @param \App\Models\Task $task
     * @return void
     */
    public function changeTaskStatus(array $data, Task $task)
    {
        $newStatus = $data['status'];
        $oldStatus = $task->status;

        $task->status = $newStatus;
        $task->save();

        if ($oldStatus !== $newStatus) {
            $task->statusUpdates()->create([
                'previous_status' => $oldStatus,
                'new_status' => $newStatus,
                'user_id' => auth()->id(),
            ]);
        }
    }

    /**
     * Update Task Status.
     * 
     * Get old status, them log changes
     * Check if the due date if it is late handle the status as block or follow other senarios
     * 
     * @param string $id
     * @param array $data
     * @throws \Exception
     * @return Task|Task[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function updateTaskStatus(string $id, array $data)
    {
        try {
            $task = Task::findOrFail($id);
            $oldStatus = $task->status;

            $task->status = $data['status'];
            $task->save();

            $task->statusUpdates()->create([
                'assigned_to' => $task->assigned_to, 
                'created_by' => $task->created_by,
                'previous_status' => $oldStatus,
                'new_status' => $data['status'],
                'user_id' => auth()->id(),
            ]);

            return $task;
        } catch (ModelNotFoundException $e) {
            Log::error('Task not found: ' . $e->getMessage());
            throw new \Exception('Task not found.');
        } catch (\Exception $e) {
            Log::error('Failed to update task status: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }

    /**
     * Retrieve all comments.
     * 
     * @throws \Exception
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function allCommentToTask($taskId)
    {
        try {
            $comments = Comment::where('commentable_id','=',$taskId)->get();
            return $comments->load(['user','attachments']);

        } catch (\Exception $e) {
            Log::error('Failed to retrieve tasks: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }

    /**
     * Add a comment to a task.
     * handle file upload and storage by AttachmentService, then associate the attachment with the comment
     * 
     * 
     * @param string $taskId
     * @param string $commentNEW
     * @param mixed $file
     * @throws \Exception
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function addCommentToTask(string $taskId, string $commentNEW, $file)
    {
        try {
            $task = Task::findOrFail($taskId);

            $comment1 = $task->comments()->create([
                'comment' => $commentNEW,
                'user_id' => auth()->id(),
            ]);
            
            $attachmentService = new AttachmentService();
            $attachment = $attachmentService->storeAttachment($file , Comment::class , $comment1->id);

            $comment1->attachments()->save($attachment);

            return $comment1->load(['user', 'attachments']);
        } catch (ModelNotFoundException $e) {
            Log::error('Task not found: ' . $e->getMessage());
            throw new \Exception('Task not found.');
        } catch (\Exception $e) {
            Log::error('Failed to add comment: ' . $e->getMessage());
            throw new \Exception('An error occurred while adding the comment.');
        }
    }


    /**
     * update a comment to a task.
     * 
     * @param string $CommentId
     * @param string $commentNEW
     * @throws \Exception
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function updateCommentToTask(string $CommentId, string $commentNEW, $file)
    {
        try {    
            $comment = Comment::where('id', $CommentId)->firstOrFail();
            $comment->update([
                'comment' => $commentNEW,
            ]);
            
            $attachmentService = new AttachmentService();
            $attachment = $attachmentService->storeAttachment($file , Comment::class , $comment->id);

            $comment->attachments()->save($attachment);

            return $comment->load(['user', 'attachments']);
        } catch (ModelNotFoundException $e) {
            Log::error('Task not found: ' . $e->getMessage());
            throw new \Exception('Task not found.');
        } catch (\Exception $e) {
            Log::error('Failed to add comment: ' . $e->getMessage());
            throw new \Exception('An error occurred while adding the comment.');
        }
    }

    public function destroyCommentToTask(string $CommentId)
    {
        try {
            $Comment = Comment::findOrFail($CommentId);
            $Comment->attachments()->forceDelete();
            $Comment->forceDelete();
            return true;
        } catch (ModelNotFoundException $e) {
            Log::error(message: 'Comment not found: ' . $e->getMessage());
            throw new \Exception('Comment not found.');
        } catch (\Exception $e) {
            Log::error('Failed to force delete Comment: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }

    
    /**
     * return all the updates on a specific task.
     * 
     * 
     * @param string $id
     * @throws \Exception
     * @return Task
     */
    public function logs(string $id)
    {
        try {
            $task = Task::findOrFail($id);
            $task->load('statusUpdates');

            return $task;
        } catch (ModelNotFoundException $e) {
            Log::error('Task not found: ' . $e->getMessage());
            throw new \Exception('Task not found.');
        } catch (\Exception $e) {
            Log::error('Failed to retrieve task: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }
}
