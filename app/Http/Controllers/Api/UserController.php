<?php

namespace App\Http\Controllers\Api;

use App\Services\UserService;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Services\ApiResponseService;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $users = $this->userService->listAllUsers();
            return ApiResponseService::success(UserResource::collection($users), 'Users retrieved successfully', 200);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        try {
            $newUser = $this->userService->createUser($validated, $validated['roles']);
            return ApiResponseService::success(new UserResource($newUser), 'User created successfully', 201);
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
            $user = $this->userService->showUser($id);
            return ApiResponseService::success(new UserResource($user), 'User retrieved successfully', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponseService::error(null, 'User not found.', 404);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, string $id)
    {
        $validated = $request->validated();

        try {
            $updatedUser = $this->userService->updateUser($id, $validated, $validated['roles'] ?? []);
            return ApiResponseService::success(new UserResource($updatedUser), 'User updated successfully', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponseService::error(null, 'User not found.', 404);
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
            $this->userService->deleteUser($id);
            return ApiResponseService::success(null, 'User deleted successfully', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponseService::error(null, 'User not found.', 404);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }

    /**
     * Display a list of soft deleted users.
     */
    public function listDeletedUsers()
    {
        try {
            $deletedUsers = $this->userService->listAllDeletedUsers();
            return ApiResponseService::success(UserResource::collection($deletedUsers), 'Deleted users retrieved successfully', 200);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }

    /**
     * Force delete the specified soft deleted user.
     */
    public function forceDeleteUser(string $id)
    {
        try {
            $this->userService->forceDeleteUser($id);
            return ApiResponseService::success(null, 'User permanently deleted successfully', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponseService::error(null, 'User not found.', 404);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }

    /**
     * Restore a soft deleted user.
     */
    public function restoreUser(string $id)
    {
        try {
            $this->userService->restoreUser($id);
            return ApiResponseService::success(null, 'User restored successfully', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponseService::error(null, 'User not found.', 404);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }

    /**
     * Assign a role to a user.
     */
    public function assignRole(string $userId, string $roleName)
    {
        try {
            $data = $this->userService->assignRoleToUser($userId, $roleName);

            return ApiResponseService::success($data, 'Role assigned successfully', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponseService::error(null, 'Role not found.', 404);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }

    /**
     * Remove a role from a user.
     */
    public function removeRole(string $userId, string $roleName)
    {
        try {
            $this->userService->removeRoleFromUser($userId, $roleName);
            return ApiResponseService::success(null, 'Role removed successfully', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponseService::error(null, 'Role not found.', 404);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }
}
