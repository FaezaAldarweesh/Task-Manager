<?php

namespace App\Services;

use App\Models\Department;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DepartmentService
{
    /**
     * Retrieve all Departments with pagination.
     * 
     * @throws \Exception
     */
    public function listAllDepartments()
    {
        try {
            $departments = Department::all();
            $departments->load('users');

            return $departments;
        } catch (\Exception $e) {
            Log::error('Failed to retrieve departments: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }

    /**
     * Create a new department with the provided data.
     * 
     * @param array $data
     * @throws \Exception
     * @return Department|\Illuminate\Database\Eloquent\Model
     */
    public function createDepartment(array $data)
    {
        try {
            $Department = Department::create($data);
            $Department->load('users');

            return $Department;
        } catch (\Exception $e) {
            Log::error('Department creation failed: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }


    /**
     * Update an existing Department with the provided data.
     * 
     * Filter valid permission IDs then sync permissions only if non-empty array
     * @param string $id
     * @param array $data
     * @throws \Exception
     * @return Department
     */
    public function updateDepartment(string $id, array $data)
    {
        try {
            $Department = Department::findOrFail($id);
            $Department->update(array_filter($data));

            $Department->load('users');

            return $Department;
        } catch (ModelNotFoundException $e) {
            Log::error('Department not found: ' . $e->getMessage());
            throw new \Exception('Department not found.');
        } catch (\Exception $e) {
            Log::error('Failed to update Department: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }

    /**
     * Delete a Department (soft delete).
     * 
     * @param string $id
     * @throws \Exception
     * @return bool
     */
    public function deleteDepartment(string $id)
    {
        try {
            $Department = Department::findOrFail($id);
            return $Department->forceDelete();
        } catch (ModelNotFoundException $e) {
            Log::error('Department not found: ' . $e->getMessage());
            throw new \Exception('Department not found.');
        } catch (\Exception $e) {
            Log::error('Failed to delete Department: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }

}
