<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
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
            $users = Cache::remember('users', 3600, function () {
                return User::with('roles.permissions')->paginate(5);
            });

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
            $user->load('roles.permissions');

            Cache::forget('users');
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
            $user->update(array_filter($data));

            $validRoleIds = array_filter($roleIds, function ($roleId) {
                return !is_null($roleId);
            });

            if (!empty($validRoleIds)) {
                $user->roles()->sync($validRoleIds);
            }
            $user->load('roles.permissions');

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
            $user = User::findOrFail($id);

            return $user->delete();
        } catch (ModelNotFoundException $e) {
            Log::error('User not found: ' . $e->getMessage());
            throw new \Exception('User not found.');
        } catch (\Exception $e) {
            Log::error('Failed to delete user: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }

    /**
     * Show soft deleted users.
     * 
     * @throws \Exception
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function listAllDeletedUsers()
    {
        try {
            return User::onlyTrashed()->paginate(5);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve deleted users: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }

    /**
     * Delete a user (force delete).
     * 
     * @param string $id
     * @throws \Exception
     * @return bool|mixed|null
     */
    public function forceDeleteUser(string $id)
    {
        try {
            $user = User::onlyTrashed()->findOrFail($id);
            return $user->forceDelete();
        } catch (ModelNotFoundException $e) {
            Log::error('User not found: ' . $e->getMessage());
            throw new \Exception('User not found.');
        } catch (\Exception $e) {
            Log::error('Error force deleting the user ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }

    /**
     * Restore soft deleted users.
     * 
     * @param string $id
     * @throws \Exception
     * @return bool|mixed
     */
    public function restoreUser(string $id)
    {
        try {
            $user = User::onlyTrashed()->findOrFail($id);
            return $user->restore();
        } catch (ModelNotFoundException $e) {
            Log::error('User not found: ' . $e->getMessage());
            throw new \Exception('User not found.');
        } catch (\Exception $e) {
            Log::error('Error in restore the user ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }

    #TODO use it with role id
    /**
     * Assign a role to a user.
     * 
     * @param string $userId
     * @param string $roleName
     * @throws \Exception
     * @return void
     */
    public function assignRoleToUser(string $userId, string $roleName): array
    {
        try {
            $user = User::select(['id', 'name'])->findOrFail($userId);
            $user->assignRole($roleName);
            $user->load('roles.permissions');

            return $user;
        } catch (ModelNotFoundException $e) {
            Log::error('User not found: ' . $e->getMessage());
            throw new \Exception('User not found.');
        } catch (\Exception $e) {
            Log::error('Failed to assign user: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }

    #TODO use it
    /**
     * Remove a role from a user.
     * 
     * @param string $userId
     * @param string $roleName
     * @throws \Exception
     * @return void
     */
    public function removeRoleFromUser(string $userId, string $roleName)
    {
        try {
            $user = User::findOrFail($userId);
            $user->removeRole($roleName);
        } catch (ModelNotFoundException $e) {
            Log::error('User not found: ' . $e->getMessage());
            throw new \Exception('User not found.');
        } catch (\Exception $e) {
            Log::error('Failed to remove user: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }
}
