<?php


namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanLogin()
    {
        // Create a user
        $user = factory(User::class)->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Send a POST request to the login route
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        // Assert that the user is redirected to the home page
        $response->assertRedirect('/home');

        // Assert that the authenticated user matches the created user
        $this->assertAuthenticatedAs($user);
    }

    public function testUserCannotLoginWithInvalidCredentials()
    {
        // Create a user
        factory(User::class)->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Send a POST request to the login route with invalid credentials
        $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrong_password',
        ]);

        // Assert that the user is not authenticated
        $this->assertGuest();
    }
}