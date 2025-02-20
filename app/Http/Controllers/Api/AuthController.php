<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Support\Facades\Auth;
use App\Services\ApiResponseService;
use App\Services\AuthService;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    /**
     * AuthService instance.
     * @var AuthService
     */
    protected $authService;

    /**
     * Inject AuthService dependency into the controller.
     * @param AuthService $movieService
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Summary of login
     * @param \App\Http\Requests\Auth\LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        $response = $this->authService->login($credentials);

        if ($response['status'] === 'error') {
            $this->error($response['message'], $response['status'], $response['code']);
        }

        return $this->success([
            'user' => $response['user'],
            'authorisation' => [
                'token' => $response['token'],
                'type' => 'bearer',
            ]
        ], 'Login Successful', $response['code']);
    }

    /**
     * Summary of register
     * @param \App\Http\Requests\Auth\RegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    // public function register(RegisterRequest $request)
    // {
    //     $data = $request->validated();
    //     $response = $this->authService->register($data);
    //     return $this->success([
    //         'user' => $response['user'],
    //         'authorisation' => [
    //             'token' => $response['token'],
    //             'type' => 'bearer',
    //         ]
    //     ], 'User created successfully', $response['code']);
    // }

    /**
     * Log out the current user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $response = $this->authService->logout();

        return $this->success(null, $response['message'], $response['code']);
    }

    /**
     * Refresh the user's authentication token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->success([
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ],
        ], 'Token refreshed successfully', 200);
    }
}
