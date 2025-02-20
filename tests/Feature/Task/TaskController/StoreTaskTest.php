<?php

namespace Tests\Feature\Task\TaskController;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StoreTaskTest extends TestCase
{
    protected $adminUser;
    protected $leaderUser;
    protected $developerUser;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:refresh');
        Artisan::call('db:seed');

        // Retrieve needed users for these tests
        $this->adminUser = User::where('email', 'admin@gmail.com')->first();
        $this->leaderUser = User::where('email', 'leader@gmail.com')->first();
        $this->developerUser = User::where('email', 'developer1@gmail.com')->first();
    }

    /**
     * Test that a leader user who has task permission can create a task.
     *
     * This test simulates a request where a user with task permissions (leader) 
     * attempts to create a task. The test ensures that the task is created successfully 
     * and that the response status is 201 (Created).
     *
     * @return void
     */
    public function test_for_leader_user_with_task_permission_can_create_task()
    {
        $leader = $this->leaderUser;
        $this->actingAs($leader);

        $data = [
            'title' => 'Test Task',
            'description' => 'Description of the test task',
            'type' => 'bug',
            'status' => 'open',
            'priority' => 'medium',
            'due_date' => '2024-12-01',
            'assigned_to' => null,
            'created_by' => $this->adminUser->id,
        ];

        $response = $this->postJson('/api/tasks', $data);

        $response->assertStatus(201);
    }

    /**
     * Test that an admin user who has task permission can create a task.
     *
     * This test simulates a request where an admin user, who has task creation permissions, 
     * tries to create a task. The test verifies that the task is created successfully 
     * and that the response status is 201 (Created).
     *
     * @return void
     */
    public function test_for_admin_user_with_task_permission_can_create_task()
    {
        $admin = $this->adminUser;
        $this->actingAs($admin);

        $data = [
            'title' => 'Test Task by Admin',
            'description' => 'Description of the task created by admin',
            'type' => 'feature',
            'status' => 'open',
            'priority' => 'high',
            'due_date' => '2024-12-01',
            'assigned_to' => null,
            'created_by' => $this->adminUser->id,
        ];

        $response = $this->postJson('/api/tasks', $data);

        $response->assertStatus(201);
    }

    /**
     * Test that a user without task permission (developer) cannot create a task.
     *
     * This test simulates a request where a user (developer) who does not have task 
     * creation permissions attempts to create a task. The test ensures that the user 
     * receives a 403 Forbidden response, preventing task creation.
     *
     * @return void
     */
    public function test_for_user_without_task_permission_cannot_create_task()
    {
        $developer = $this->developerUser;
        $this->actingAs($developer);

        $data = [
            'title' => 'Test Task by Developer',
            'description' => 'Developer should not be able to create this task',
            'type' => 'bug',
            'status' => 'open',
            'priority' => 'medium',
            'due_date' => '2024-12-01',
            'assigned_to' => null,
            'created_by' => $this->adminUser->id,
        ];

        $response = $this->postJson('/api/tasks', $data);

        $response->assertStatus(403);
    }

    /**
     * Test that an unauthenticated user cannot create a task.
     *
     * This test ensures that an unauthenticated user who is not logged in 
     * attempts to create a task. The test verifies that the response status 
     * is 403 Forbidden, and that task creation is blocked for unauthenticated users.
     *
     * @return void
     */
    public function test_for_unauthenticated_user_cannot_create_task()
    {
        $data = [
            'title' => 'Test Task by Unauthenticated User',
            'description' => 'Unauthenticated user should not be able to create this task',
            'type' => 'bug',
            'status' => 'open',
            'priority' => 'medium',
            'due_date' => '2024-12-01',
            'assigned_to' => null,
            'created_by' => $this->adminUser->id,
        ];

        $response = $this->postJson('/api/tasks', $data);

        $response->assertStatus(403);
    }
}
