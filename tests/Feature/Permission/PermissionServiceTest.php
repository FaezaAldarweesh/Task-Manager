<?php

namespace Tests\Feature\Permission;

use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Foundation\Testing\WithFaker;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PermissionServiceTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    public function test_for_list_all_permissions_if_authenticated_the_user_is_admin()
    {
        $user = User::where('email', 'admin@gmail.com')->first();

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/permissions');

        $response->assertStatus(200);
    }

    // public function test_for_create_permission_by_admin()
    // {
    //     $user = User::where('email', 'admin@gmail.com')->first();
    //     $token = JWTAuth::fromUser($user);

    //     $data = [
    //         'name' => 'New Permission',
    //         'description' => 'Description of the new permission',
    //     ];

    //     $response = $this->withHeader('Authorization', 'Bearer ' . $token)
    //         ->postJson('/api/permissions', $data);

    //     $response->assertStatus(201);
    // }
}
