<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\DepartmentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Auth Routes
Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('logout', 'logout')->middleware('auth:api');
    Route::post('refresh', 'refresh')->middleware('auth:api');
});

Route::middleware(['throttle:60,1', 'security'])->group(function () {

    // Permission Routes
    Route::group(['middleware' => ['permission:permission']], function () {
        Route::apiResource('permissions', PermissionController::class);
    });


    // Role Routes
    Route::group(['middleware' => ['permission:role']], function () {
        Route::apiResource('roles', RoleController::class);
        Route::post('roles/{roleId}/permissions', [RoleController::class, 'assignPermissions']);
    });


    // User Routes
    Route::group(['middleware' => ['permission:user']], function () {
        Route::apiResource('users', UserController::class);
    });

    // Task Routes
    // 1- CRUD
    Route::get('tasks', [TaskController::class, 'index'])->middleware('permission:list_task');
    Route::post('tasks', [TaskController::class, 'store'])->middleware('permission:add_task');
    Route::get('tasks/{id}', [TaskController::class, 'show'])->middleware('permission:view_task');
    Route::put('tasks/{id}', [TaskController::class, 'update'])->middleware('permission:update_task');
    Route::delete('tasks/{id}', [TaskController::class, 'destroy'])->middleware('permission:soft_delete_task');

    // 2-  Soft-Delete
    Route::get('/tasks/deleted', [TaskController::class, 'listDeletedTasks'])->middleware('permission:list_delete_task');
    Route::post('/tasks/{id}/restore', [TaskController::class, 'restoreTask'])->middleware('permission:restor_delete_task');
    Route::delete('/tasks/{id}/force-delete', [TaskController::class, 'forceDeleteTask'])->middleware('permission:force_delete_task');

    // 3- Other Operations
    Route::post('/tasks/{id}/assign', [TaskController::class, 'assignTask'])->middleware('permission:assign_task');
    Route::put('/tasks/{id}/reassign', [TaskController::class, 'reassignTask'])->middleware('permission:assign_task');
    Route::put('/tasks/{id}/status', [TaskController::class, 'updateTaskStatus'])->middleware('permission:update_status');

    // 4- comment
    Route::get( 'comments/{id}', [TaskController::class, 'allComment'])->middleware('permission:comment');
    Route::post('comments/{id}', [TaskController::class, 'addComment'])->middleware('permission:comment');
    Route::put('comments/{CommentId}', [TaskController::class, 'updateComment'])->middleware('permission:comment');
    Route::delete('comments/{CommentId}', [TaskController::class, 'destroyComment'])->middleware('permission:comment');

    Route::delete('attachment/{attachmentid}', [TaskController::class, 'destroyattachment'])->middleware('permission:attachment');

    // 5- log
    Route::get('logs/{taskId}', [TaskController::class, 'logs']);

    // Department Routes
    Route::get('departments', [DepartmentController::class, 'index'])->middleware('permission:department');
    Route::post('departments', [DepartmentController::class, 'store'])->middleware('permission:department');
    Route::put('departments/{id}', [DepartmentController::class, 'update'])->middleware('permission:department');
    Route::delete('departments/{id}', [DepartmentController::class, 'destroy'])->middleware('permission:department');

});

// Report Route
Route::get('/reports/daily-tasks', [ReportController::class, 'generateDailyTaskReport'])->middleware('permission:full_access');
