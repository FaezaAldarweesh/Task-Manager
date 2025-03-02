<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Services\UserService;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Services\ApiResponseService;
use Illuminate\Support\Facades\Auth;
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
            return ApiResponseService::error(null, $e->getMessage(), 500);
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
            $user = $this->userService->deleteUser($id);
            if ($user instanceof \Illuminate\Http\JsonResponse) {
                return $user;
            }
            return ApiResponseService::success(null, 'User deleted successfully', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponseService::error(null, 'User not found.', 404);
        } catch (\Exception $e) {
            return ApiResponseService::error(null, 'An error occurred on the server.', 500);
        }
    }

    //===========================================================================================================================
    /**
     * method to show user alraedy exist
     * @param  $user_id
     * @return /Illuminate\Http\JsonResponse
     */
    public function view_info()
    {
        $user = User::find(Auth::id());
        return ApiResponseService::success(new UserResource($user), 'User deleted successfully', 200);
    }
    //===========================================================================================================================
    /**
     * method to update user alraedy exist
     * @param  UpdateUserRequest $request
     * @return /Illuminate\Http\JsonResponse
     */
    public function update_info(UpdateUserRequest $request)
    {
        $data = $request->validated();
        $user = User::find(Auth::id());

        $user->name = $data['name'] ?? $user->name;
        $user->phone = $data['phone'] ?? $user->phone;
        $user->location = $data['location'] ?? $user->location;  

        $user->save(); 

       return ApiResponseService::success(new UserResource($user), "تمت عملية التعديل على المعلومات بنجاح", 200);
    }
    //===========================================================================================================================

}
