<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * Attempt to log in a user with the given credentials.
     *
     * @param array $credentials The user's login credentials.
     * @return array An array containing the login response.
     */
    public function login(array $credentials)
    {
        if (!$token = Auth::attempt($credentials)) {
            return [
                'status' => 'error',
                'message' => 'Unauthorized',
                'code' => 401
            ];
        }
        return [
            'status' => 'success',
            'user' => Auth::user(),
            'token' => $token,
            'code' => 200
        ];
    }

    /**
     * Register a new user with the given data.
     *
     * @param array $data The user's registration data.
     * @return array An array containing the registration response.
     */
    // public function register(array $data)
    // {
    //     $user = User::create([
    //         'name' => $data['name'],
    //         'email' => $data['email'],
    //         'password' => $data['password'],
    //     ]);

    //     $user->assignRole('user');

    //     $token = Auth::login($user);

    //     return [
    //         'status' => 'success',
    //         'message' => 'User created successfully',
    //         'user' => $user,
    //         'token' => $token,
    //         'code' => 201,
    //     ];
    // }

    /**
     * Logout the current user.
     *
     * @return array An array containing the logout response.
     */
    public function logout()
    {
        Auth::logout();

        return [
            'status' => 'success',
            'message' => 'Logged out successfully',
            'code' => 200,
        ];
    }
}
