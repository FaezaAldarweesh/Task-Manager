<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
//use Illuminate\Support\Facades\Cache;
use App\Services\ApiResponseService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserService
{
    /**
     * Retrieve all users with pagination.
     * 
     * @throws \Exception
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function listAllUsers()
    {
        try {
            $users = User::with(['roles.permissions','department'])->get();
            return $users;

        } catch (\Exception $e) {
            Log::error('Failed to retrieve users: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }

    /**
     * Create a new user with the provided data.
     * 
     * @param array $data
     * @throws \Exception
     * @return User|\Illuminate\Database\Eloquent\Model
     */
    public function createUser(array $data, array $roleIds)
    {
        try {
            $user = User::create($data);
            $user->assignRoles($roleIds);
            $user->load(['roles.permissions','department']);

            return $user;
        } catch (\Exception $e) {
            Log::error('User creation failed: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }

    /**
     * Retrieve a single user.
     * 
     * @param string $id
     * @throws \Exception
     * @return User
     */
    public function showUser(string $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->load('roles.permissions');

            return $user;
        } catch (ModelNotFoundException $e) {
            Log::error('User not found: ' . $e->getMessage());
            throw new \Exception('User not found.');
        } catch (\Exception $e) {
            Log::error('Failed to retrieve user: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }

    /**
     * Update an existing user with the provided data.
     * 
     * Filter out null values from the roles array then keep non-null values
     * @param string $id
     * @param array $data
     * @param array $roleIds
     * @throws \Exception
     * @return User|User[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function updateUser(string $id, array $data, array $roleIds = [])
    {
        try {
            $user = User::findOrFail($id);
            $user->update($data);

            $validRoleIds = array_filter($roleIds, function ($roleId) {
                return !is_null($roleId);
            });

            if (!empty($validRoleIds)) {
                $user->roles()->sync($validRoleIds);
            }
            $user->load(['roles.permissions','department']);

            return $user;
        } catch (ModelNotFoundException $e) {
            Log::error('User not found: ' . $e->getMessage());
            throw new \Exception('User not found.');
        } catch (\Exception $e) {
            Log::error('Failed to update user: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }

    /**
     * Delete a user.
     * 
     * @param string $id
     * @throws \Exception
     * @return bool
     */
    public function deleteUser(string $id)
    {
        try {
            $user = User::with('roles')->findOrFail($id);
            if ($user->roles->pluck('name')->toArray() == ('CEO ' || 'Leader Manager')) {
                throw new \Exception('لا يمكنك إجراء حذف على حساب الأدمن');
            }

            return $user->forceDelete();
        } catch (ModelNotFoundException $e) {
            Log::error('User not found: ' . $e->getMessage());
            throw new \Exception('User not found.');
        }catch (\Exception $e) { Log::error($e->getMessage());
            return ApiResponseService::error(null, $e->getMessage(), 404);
        }
    }

}
