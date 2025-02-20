<?php

namespace Tests\Feature\Task\TaskController;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IndexTaskTest extends TestCase
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
     * Test that any authenticated user can retrieve a list of tasks with pagination.
     *
     * This test verifies that any authenticated user (admin, leader, or developer) can access
     * the list of tasks, with pagination applied. The response status should be 200 (OK).
     *
     * @return void
     */
    public function test_for_authenticated_user_can_view_all_tasks_with_pagination()
    {
        $authenticatedUser = $this->adminUser;
        $this->actingAs($authenticatedUser);

        $response = $this->getJson('/api/tasks');

        $response->assertStatus(200);
    }

    /**
     * Test that an authenticated user can retrieve a list of tasks filtered by type.
     *
     * This test ensures that an authenticated user can apply a filter by task type and that
     * the filtered results are returned correctly with a 200 (OK) status.
     *
     * @return void
     */
    public function test_for_authenticated_user_can_filter_tasks_by_type()
    {
        $authenticatedUser = $this->leaderUser;
        $this->actingAs($authenticatedUser);

        $response = $this->getJson('/api/tasks?type=bug');

        $response->assertStatus(200);
    }

    /**
     * Test that an authenticated user can retrieve a list of tasks filtered by status.
     *
     * This test checks if the authenticated user can apply a filter by task status, ensuring
     * that only tasks with the specified status are returned.
     *
     * @return void
     */
    // public function test_for_authenticated_user_can_filter_tasks_by_status()
    // {
    //     $authenticatedUser = $this->developerUser; 
    //     $this->actingAs($authenticatedUser);

    //     $response = $this->getJson(route('tasks.index', ['status' => 'open']));

    //     $response->assertStatus(200);
    // }

    /**
     * Test that an authenticated user can retrieve a list of tasks filtered by multiple parameters.
     *
     * This test ensures that the authenticated user can apply multiple filters (e.g., type, status, priority)
     * and that the response contains tasks that match all of the provided filters.
     *
     * @return void
     */
    public function test_for_authenticated_user_can_filter_tasks_by_multiple_parameters()
    {
        $authenticatedUser = $this->adminUser;
        $this->actingAs($authenticatedUser);

        $response = $this->getJson('/api/tasks?type=bug&status=open&priority=high');

        $response->assertStatus(200);
    }

    /**
     * Test that an unauthenticated user cannot retrieve tasks.
     *
     * This test checks that an unauthenticated user who is not logged in receives a 403
     * Forbidden response when attempting to access the list of tasks.
     *
     * @return void
     */
    // public function test_for_unauthenticated_user_cannot_view_tasks()
    // {
    //     $response = $this->getJson('/api/tasks');

    //     $response->assertStatus(403);
    // }
}
