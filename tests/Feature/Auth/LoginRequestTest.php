<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginRequestTest extends TestCase
{
    /**
     * Test that login fails when the provided email does not exist.
     *
     * This test sends a login request with an email that is not registered in the database.
     * It asserts that a 401 status is returned along with an error message.
     *     
     * @return void
     */
    public function test_for_fails_when_email_does_not_exist()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'testtest',
        ]);

        $response->assertStatus(401)->assertJson([
            'status' => 'error',
            'message' => 'A server error has occurred',
            'data' => ['The provided email does not exist.'],
        ]);
    }

    /**
     * Test that login fails when an incorrect password is provided.
     *
     * This test sends a login request with a valid email but an incorrect password.
     * It asserts that a 401 status is returned along with an error message.
     *
     * @return void
     */
    public function test_for_fails_when_password_is_incorrect()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'admin@gmail.com',
            'password' => 'testtest',
        ]);

        $response->assertStatus(401)->assertJson([
            'status' => 'error',
            'message' => 'A server error has occurred',
            'data' => ['The provided password is incorrect.'],
        ]);
    }

    /**
     * Test that login fails when the provided email is not valid.
     *
     * This test sends a login request with an improperly formatted email address.
     * It asserts that a 403 status is returned along with an error message.
     *
     * @return void
     */
    public function test_for_fails_when_email_is_not_valid()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'admin.com',
            'password' => '12345678',
        ]);

        $response->assertStatus(403)->assertJson([
            'status' => 'error',
            'message' => 'A server error has occurred',
            'data' => ["The email address must be a valid email address."],
        ]);
    }

    /**
     * Test that login fails when the password is shorter than the minimum required characters.
     *
     * This test sends a login request with a password that has less than the minimum required length.
     * It asserts that a 403 status is returned with an error message.
     *
     * @return void
     */
    public function test_for_fails_when_password_is_less_than_minimum_required_characters()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'admin@gmail.com',
            'password' => '1',
        ]);

        $response->assertStatus(403)->assertJson([
            'status' => 'error',
            'message' => 'A server error has occurred',
            'data' =>  ["The password must be at least 8 characters."]
        ]);
    }

    /**
     * Test that login fails when the password exceeds the maximum allowed characters.
     *
     * This test sends a login request with a password that exceeds the maximum allowed length.
     * It asserts that a 403 status is returned with an error message.
     *
     * @return void
     */
    public function test_for_fails_when_password_is_greater_than_maximum_characters()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'admin@gmail.com',
            'password' => '1111111111111111111111111111111111',
        ]);

        $response->assertStatus(403)->assertJson([
            'status' => 'error',
            'message' => 'A server error has occurred',
            'data' => ["The password may not be greater than 30 characters."]
        ]);
    }

    /**
     * Test that login fails when the password is not provided.
     *
     * This test sends a login request without the password field.
     * It asserts that a 403 status is returned along with an error message.
     *
     * @return void
     */
    public function test_for_fails_when_password_not_provided()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'admin@gmail.com',
        ]);

        $response->assertStatus(403)->assertJson([
            'status' => 'error',
            'message' => 'A server error has occurred',
            'data' => ["The password field is required."]
        ]);
    }

    /**
     * Test that login fails when the email is not provided.
     *
     * This test sends a login request without the email field.
     * It asserts that a 403 status is returned along with an error message.
     *      
     * @return void
     */
    public function test_for_fails_when_email_not_provided()
    {
        $response = $this->postJson('/api/login', [
            'password' => '12345678',
        ]);

        $response->assertStatus(403)->assertJson([
            'status' => 'error',
            'message' => 'A server error has occurred',
            'data' => ["The email address field is required."]
        ]);
    }

    /**
     * Test that login fails when neither email nor password is provided.
     *
     * This test sends a login request without both the email and password fields.
     * It asserts that a 403 status is returned along with error messages. 
     *
     * @return void
     */
    public function test_for_fails_when_email_and_password_are_not_provided()
    {
        $response = $this->postJson('/api/login');

        $response->assertStatus(403)->assertJson([
            'status' => 'error',
            'message' => 'A server error has occurred',
            'data' =>  [
                "The email address field is required.",
                "The password field is required."
            ]
        ]);
    }
}
