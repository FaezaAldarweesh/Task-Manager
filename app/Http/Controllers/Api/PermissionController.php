<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ApiResponseService;
use App\Http\Requests\Permission\StorePermissionRequest;
use App\Http\Requests\Permission\UpdatePermissionRequest;
use App\Http\Resources\PermissionResource;
use App\Services\PermissionService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PermissionController extends Controller
{
    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $permissions = $this->permissionService->listAllPermissions();
            return ApiResponseService::success(PermissionResource::collection($permissions), 'Permissions retrieved successfully', 200);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePermissionRequest $request)
    {
        $validated = $request->validated();

        try {
            $newPermission = $this->permissionService->createPermission($validated);
            return ApiResponseService::success(new PermissionResource($newPermission), 'Permission created successfully', 201);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $permission = $this->permissionService->showPermission($id);
            return ApiResponseService::success(new PermissionResource($permission), 'Permission retrieved successfully', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponseService::error(null, 'Permission not found.', 404);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePermissionRequest $request, string $id)
    {
        $validated = $request->validated();

        try {
            $updatedPermission = $this->permissionService->updatePermission($id, $validated);
            return ApiResponseService::success(new PermissionResource($updatedPermission), 'Permission updated successfully', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponseService::error(null, 'Permission not found.', 404);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }

    /**
     * Remove the specified resource from storage (soft-delete).
     */
    public function destroy(string $id)
    {
        try {
            $this->permissionService->deletePermission($id);
            return ApiResponseService::success(null, 'Permission deleted successfully', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponseService::error(null, 'Permission not found.', 404);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }

    /**
     * Display a list of soft deleted permissions.
     */
    public function listDeletedPermissions()
    {
        try {
            $deletedPermissions = $this->permissionService->listAllDeletedPermissions();
            return ApiResponseService::success(PermissionResource::collection($deletedPermissions), 'Deleted permissions retrieved successfully', 200);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }

    /**
     * Force delete the specified soft deleted permission.
     */
    public function forceDeletePermission(string $id)
    {
        try {
            $this->permissionService->forceDeletePermission($id);
            return ApiResponseService::success(null, 'Permission permanently deleted successfully', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponseService::error(null, 'Permission not found.', 404);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }

    /**
     * Restore a soft deleted permission.
     */
    public function restorePermission(string $id)
    {
        try {
            $this->permissionService->restorePermission($id);
            return ApiResponseService::success(null, 'Permission restored successfully', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponseService::error(null, 'Permission not found.', 404);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }
}
