<?php

namespace App\Services;

use GrahamCampbell\GitHub\Facades\GitHub;
use Illuminate\Support\Facades\Crypt;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

class GitHubService
{
    public function encryptToken($token)
    {
        return Crypt::encryptString($token);
    }

    public function decryptToken($encryptedToken)
    {
        return Crypt::decryptString($encryptedToken);
    }

    public function getStarredRepos($encryptedToken)
    {
        try {
            // Decrypt the GitHub token
            $githubToken = $this->decryptToken($encryptedToken);

            // Authenticate using the decrypted GitHub token
            GitHub::authenticate($githubToken, '', 'access_token_header');

            // Fetch starred repositories using the GitHub facade
            $starredRepos = GitHub::me()->starring()->all();

            return $starredRepos;
        } catch (ClientException $e) {
            // Handle bad token or unauthorized request
            return ['error' => 'Bad token or unauthorized request'];
        } catch (ServerException $e) {
            // Handle 500 internal server error
            return ['error' => 'Failed to fetch data from the server'];
        } catch (\Exception $e) {
            // Handle other exceptions
            return ['error' => 'An error occurred'];
        }
    }
}