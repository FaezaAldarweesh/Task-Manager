<?php

namespace App\Services;

use App\Models\Permission;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PermissionService
{
    /**
     * Retrieve all permissions with pagination.
     * 
     * @throws \Exception
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function listAllPermissions()
    {
        try {
            return Permission::paginate(5);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve permissions: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }

    /**
     * Create a new permission with the provided data.
     * 
     * @param array $data
     * @throws \Exception
     * @return Permission|\Illuminate\Database\Eloquent\Model
     */
    public function createPermission(array $data)
    {
        try {
            return Permission::create($data);
        } catch (\Exception $e) {
            Log::error('Permission creation failed: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }

    /**
     * Retrieve a single permission.
     * 
     * @param string $id
     * @throws \Exception
     * @return Permission
     */
    public function showPermission(string $id)
    {
        try {
            return Permission::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            Log::error('Permission not found: ' . $e->getMessage());
            throw new \Exception('Permission not found.');
        } catch (\Exception $e) {
            Log::error('Failed to retrieve permission: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }

    /**
     * Update an existing permission with the provided data.
     * 
     * @param string $id
     * @param array $data
     * @throws \Exception
     * @return Permission
     */
    public function updatePermission(string $id, array $data)
    {
        try {
            $permission = Permission::findOrFail($id);
            $permission->update(array_filter($data));

            return $permission;
        } catch (ModelNotFoundException $e) {
            Log::error('Permission not found: ' . $e->getMessage());
            throw new \Exception('Permission not found.');
        } catch (\Exception $e) {
            Log::error('Failed to update permission: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }

    /**
     * Delete a permission.
     * 
     * @param string $id
     * @throws \Exception
     * @return bool
     */
    public function deletePermission(string $id)
    {
        try {
            $permission = Permission::findOrFail($id);
            return $permission->delete();
        } catch (ModelNotFoundException $e) {
            Log::error('Permission not found: ' . $e->getMessage());
            throw new \Exception('Permission not found.');
        } catch (\Exception $e) {
            Log::error('Failed to delete permission: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }

    /**
     * Show soft deleted permissions.
     * 
     * @throws \Exception
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function listAllDeletedPermissions()
    {
        try {
            return Permission::onlyTrashed()->paginate(5);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve deleted permissions: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }

    /**
     * Delete a permission (force delete).
     * 
     * @param string $id
     * @throws \Exception
     * @return bool|mixed|null
     */
    public function forceDeletePermission(string $id)
    {
        try {
            $permission = Permission::onlyTrashed()->findOrFail($id);
            return $permission->forceDelete();
        } catch (ModelNotFoundException $e) {
            Log::error('Permission not found: ' . $e->getMessage());
            throw new \Exception('Permission not found.');
        } catch (\Exception $e) {
            Log::error('Error force deleting the permission ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }

    /**
     * Restore soft deleted permissions.
     * 
     * @param string $id
     * @throws \Exception
     * @return bool|mixed
     */
    public function restorePermission(string $id)
    {
        try {
            $permission = Permission::onlyTrashed()->findOrFail($id);
            return $permission->restore();
        } catch (ModelNotFoundException $e) {
            Log::error('Permission not found: ' . $e->getMessage());
            throw new \Exception('Permission not found.');
        } catch (\Exception $e) {
            Log::error('Error in restore the permission ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }
}
