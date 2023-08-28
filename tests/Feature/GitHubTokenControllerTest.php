<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;

class GitHubTokenControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testEncryptToken()
    {
        $user = factory(User::class)->create([
            'github_token' => Crypt::encrypt('your-github-token'), // Encrypt the token here
        ]);
    
        $token = 'your-github-token'; // Replace with the token you want to test
    
        $response = $this->actingAs($user)
            ->postJson('/save-token', ['token' => $token]);
    
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Token saved successfully']);
    
        // Ensure the user's github_token attribute is encrypted correctly
    }

    public function testEncryptTokenWithMissingToken()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->postJson('/save-token');

        $response->assertStatus(400);
        $response->assertJson(['error' => 'Token is required']);
    }

    public function testDecryptToken()
    {
        $user = factory(User::class)->create([
            'github_token' => Crypt::encrypt('your-github-token'), // Encrypt a token here
        ]);

        $response = $this->actingAs($user)
            ->get('/decrypted-token');

        $response->assertStatus(200);
        $response->assertJson(['token' => 'your-github-token']);
    }

    public function testDecryptTokenWithNoToken()
    {
        $user = factory(User::class)->create([
            'github_token' => null, // Set github_token to null to simulate no token available
        ]);

        $response = $this->actingAs($user)
            ->get('/decrypted-token');

        $response->assertStatus(404);
        $response->assertJson(['message' => 'No decrypted token available']);
    }
}