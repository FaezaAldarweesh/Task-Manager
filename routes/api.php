<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\ErrorLogController;
use App\Http\Controllers\Api\PermissionController;

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
    Route::group(['middleware' => ['permission:full_access']], function () {
        Route::apiResource('permissions', PermissionController::class);
        Route::get('/permissions/deleted', [PermissionController::class, 'listDeletedPermissions']);
        Route::post('/permissions/{id}/restore', [PermissionController::class, 'restorePermission']);
        Route::delete('/permissions/{id}/force-delete', [PermissionController::class, 'forceDeletePermission']);
    });


    // Role Routes
    Route::group(['middleware' => ['permission:full_access']], function () {
        Route::apiResource('roles', RoleController::class);
        Route::get('/roles/deleted', [RoleController::class, 'listDeletedRoles']);
        Route::post('/roles/{id}/restore', [RoleController::class, 'restoreRole']);
        Route::delete('/roles/{id}/force-delete', [RoleController::class, 'forceDeleteRole']);
        Route::post('roles/{roleId}/permissions', [RoleController::class, 'assignPermissions']);
    });


    // User Routes
    Route::group(['middleware' => ['permission:full_access']], function () {
        Route::apiResource('users', UserController::class);
        Route::get('/users/deleted', [UserController::class, 'listDeletedUsers']);
        Route::post('/users/{id}/restore', [UserController::class, 'restoreUser']);
        Route::delete('/users/{id}/force-delete', [UserController::class, 'forceDeleteUser']);
    });

    // Task Routes
    // 1- CRUD
    Route::get('tasks', [TaskController::class, 'index'])->middleware('auth:api');
    Route::post('tasks', [TaskController::class, 'store'])->middleware('permission:task');
    Route::get('tasks/{id}', [TaskController::class, 'show'])->middleware('auth:api');
    Route::put('tasks/{id}', [TaskController::class, 'update'])->middleware('permission:task');
    Route::delete('tasks/{id}', [TaskController::class, 'destroy'])->middleware('permission:task');

    // 2-  Soft-Delete
    Route::get('/tasks/deleted', [TaskController::class, 'listDeletedTasks'])->middleware('permission:task');
    Route::post('/tasks/{id}/restore', [TaskController::class, 'restoreTask'])->middleware('permission:task');
    Route::delete('/tasks/{id}/force-delete', [TaskController::class, 'forceDeleteTask'])->middleware('permission:task');

    // 3- Other Operations
    Route::post('/tasks/{id}/assign', [TaskController::class, 'assignTask'])->middleware('permission:status');
    Route::put('/tasks/{id}/reassign', [TaskController::class, 'reassignTask'])->middleware('permission:status');
    Route::put('/tasks/{id}/status', [TaskController::class, 'updateTaskStatus'])->middleware('permission:full_access');
    Route::get('/tasks/blocked-late', [TaskController::class, 'blockedAndLateTasks'])->middleware('permission:task');
    Route::post('/tasks/{id}/comment', [TaskController::class, 'addComment'])->middleware('permission:comment');
    Route::post('/tasks/{id}/attachment', [TaskController::class, 'addAttachment'])->middleware('permission:attachment');
});

// Report Route
Route::get('/reports/daily-tasks', [ReportController::class, 'generateDailyTaskReport'])->middleware('permission:full_access');
