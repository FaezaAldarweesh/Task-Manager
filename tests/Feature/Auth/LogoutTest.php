<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\WithFaker;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LogoutTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:refresh');
        Artisan::call('db:seed');
    }
    /**
     * Test that an authenticated user can successfully log out.
     *
     * This test sends a logout request with a valid token.
     * It uses the JWTAuth facade to generate a token from an existing user.
     * It asserts that a 200 status is returned and confirming the logout.
     *
     * @return void
     */
    public function test_for_allows_authenticated_user_to_logout_successfully()
    {
        $user = User::first();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/logout');

        $response->assertStatus(200)->assertJson([
            "status" => "success",
            "message" => "Logged out successfully",
            "data" => null
        ]);
    }

    /**
     * Test that an unauthenticated user cannot log out.
     *
     * This test sends a logout request without a valid token.
     * It asserts that a 401 status is returned with an error message.
     *
     * @return void
     */

    // public function test_for_denies_logout_for_unauthenticated_user()
    // {
    //     $response = $this->postJson('/api/logout');

    //     $response->assertStatus(401)
    //         ->assertJson(['message' => 'Unauthenticated.']);
    // }
}
