<?php

namespace App\Http\Controllers\Api;

use App\Models\Department;
use Illuminate\Http\Request;
use App\Services\DepartmentService;
use App\Http\Controllers\Controller;
use App\Services\ApiResponseService;
use App\Http\Resources\DepartmentResource;
use App\Http\Requests\Department\StoreDepartmentRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\Department\UpdateDepartmentRequest;


class DepartmentController extends Controller
{
    protected $DepartmentService;

    public function __construct(DepartmentService $DepartmentService)
    {
        $this->DepartmentService = $DepartmentService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $Departments = $this->DepartmentService->listAllDepartments();
            return ApiResponseService::success(DepartmentResource::collection($Departments), 'Departments retrieved successfully', 200);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDepartmentRequest $request)
    {
        $validated = $request->validated();

        try {
            $newDepartment = $this->DepartmentService->createDepartment($validated);
            return ApiResponseService::success(new DepartmentResource($newDepartment), 'Department created successfully', 201);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDepartmentRequest $request, string $id)
    {
        $validated = $request->validated();

        try {
            $updatedDepartment = $this->DepartmentService->updateDepartment($id, $validated);
            return ApiResponseService::success(new DepartmentResource($updatedDepartment), 'Department updated successfully', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponseService::error(null, 'Department not found.', 404);
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
            $this->DepartmentService->deleteDepartment($id);
            return ApiResponseService::success(null, 'Department deleted successfully', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponseService::error(null, 'Department not found.', 404);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }

}
