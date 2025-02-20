<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\RoleService;
use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use App\Services\ApiResponseService;
use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RoleController extends Controller
{
    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $roles = $this->roleService->listAllRoles();
            return ApiResponseService::success(RoleResource::collection($roles), 'Roles retrieved successfully', 200);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request)
    {
        $validated = $request->validated();

        try {
            $newRole = $this->roleService->createRole($validated, $validated['permissions']);
            return ApiResponseService::success(new RoleResource($newRole), 'Role created successfully', 201);
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
            $role = $this->roleService->showRole($id);
            return ApiResponseService::success(new RoleResource($role), 'Role retrieved successfully', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponseService::error(null, 'Role not found.', 404);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, string $id)
    {
        $validated = $request->validated();

        try {
            $updatedRole = $this->roleService->updateRole($id, $validated, $validated['permissions'] ?? []);
            return ApiResponseService::success(new RoleResource($updatedRole), 'Role updated successfully', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponseService::error(null, 'Role not found.', 404);
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
            $this->roleService->deleteRole($id);
            return ApiResponseService::success(null, 'Role deleted successfully', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponseService::error(null, 'Role not found.', 404);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }

    /**
     * Display a list of soft deleted roles.
     */
    public function listDeletedRoles()
    {
        try {
            $deletedRoles = $this->roleService->listAllDeletedRoles();
            return ApiResponseService::success(RoleResource::collection($deletedRoles), 'Deleted roles retrieved successfully', 200);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }

    /**
     * Force delete the specified soft deleted role.
     */
    public function forceDeleteRole(string $id)
    {
        try {
            $this->roleService->forceDeleteRole($id);
            return ApiResponseService::success(null, 'Role permanently deleted successfully', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponseService::error(null, 'Role not found.', 404);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }

    /**
     * Restore a soft deleted role.
     */
    public function restoreRole(string $id)
    {
        try {
            $this->roleService->restoreRole($id);
            return ApiResponseService::success(null, 'Role restored successfully', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponseService::error(null, 'Role not found.', 404);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }

    /**
     * Assign permissions to a role.
     * 
     * @param string $roleId
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignPermissions(string $roleId, Request $request)
    {
        $validated = $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        try {
            $updatedRole = $this->roleService->assignPermissionsToRole($roleId, $validated['permissions']);
            return ApiResponseService::success(new RoleResource($updatedRole), 'Permissions assigned successfully', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponseService::error(null, 'Role not found.', 404);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }
}
