<?php


namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use Mockery;
use GrahamCampbell\GitHub\Facades\GitHub;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;

class GitHubRepoControllerTest extends TestCase
{
    use RefreshDatabase; // Use RefreshDatabase trait for database testing

    public function testFetchStarredRepos()
    {
        // Mock the GitHub facade
        $mock = Mockery::mock('alias:'.GitHub::class);
        $mock->shouldReceive('authenticate')->once(); // Mock authentication

        $mockStarredRepos = [
            ['name' => 'Repo 1'],
            ['name' => 'Repo 2'],
        ];
        $mock->shouldReceive('me->starring->all')->andReturn($mockStarredRepos);

        // Create a test user with a GitHub token
        $user = factory(User::class)->create([
            'github_token' => Crypt::encrypt('your-github-token'),
        ]);

        // Acting as the authenticated user
        $response = $this->actingAs($user)
            ->get('/starred-repos');

        $response->assertStatus(200);
       // $response->assertJsonCount(2, 'data');
        $response->assertJson($mockStarredRepos); // Assert response matches mocked data
    }

    public function testFetchStarredReposWithInvalidToken()
    {
        // Mock the GitHub facade for unauthorized request
        $mock = Mockery::mock('alias:'.GitHub::class);
        $mock->shouldReceive('authenticate')->once();
        $mock->shouldReceive('me->starring->all')
            ->andThrow(new \GuzzleHttp\Exception\ClientException('', Mockery::mock(\GuzzleHttp\Psr7\Request::class)));

        // Create a test user with an invalid GitHub token
        $user = factory(User::class)->create([
            'github_token' => Crypt::encrypt('invalid-token'),
        ]);

        // Acting as the authenticated user
        $response = $this->actingAs($user)
            ->get('/starred-repos');

        $response->assertStatus(401);
    }

    public function testFetchStarredReposServerError()
    {
        // Mock the GitHub facade for server error
        $mock = Mockery::mock('alias:'.GitHub::class);
        $mock->shouldReceive('authenticate')->once();
        $mock->shouldReceive('me->starring->all')
            ->andThrow(new \GuzzleHttp\Exception\ServerException('', Mockery::mock(\GuzzleHttp\Psr7\Request::class)));

        // Create a test user with a valid GitHub token
        $user = factory(User::class)->create([
            'github_token' => Crypt::encrypt('your-github-token'),
        ]);

        // Acting as the authenticated user
        $response = $this->actingAs($user)
            ->get('/starred-repos');

        $response->assertStatus(500);
    }

    public function testFetchStarredReposOtherException()
    {
        // Mock the GitHub facade for other exceptions
        $mock = Mockery::mock('alias:'.GitHub::class);
        $mock->shouldReceive('authenticate')->once();
        $mock->shouldReceive('me->starring->all')
            ->andThrow(new \Exception());

        // Create a test user with a valid GitHub token
        $user = factory(User::class)->create([
            'github_token' => Crypt::encrypt('your-github-token'),
        ]);

        // Acting as the authenticated user
        $response = $this->actingAs($user)
            ->get('/starred-repos');

        $response->assertStatus(500);
    }

    protected function tearDown(): void
    {
        Mockery::close(); // Close mockery after each test
        parent::tearDown();
    }
}