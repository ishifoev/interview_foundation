<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GrahamCampbell\GitHub\Facades\GitHub; 
use Illuminate\Support\Facades\Crypt;

class GithubRepoController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $user = auth()->user();
        $githubToken = Crypt::decrypt($user->github_token);

        try {
            // Authenticate using the GitHub token
            Github::authenticate($githubToken, '', 'access_token_header');

                // Fetch starred repositories using the GitHub facade
                $starredRepos = Github::me()->starring()->all();

                return response()->json($starredRepos);
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                // Handle bad token or unauthorized request
                return response()->json(['error' => 'Bad token or unauthorized request'], 401);
            } catch (\GuzzleHttp\Exception\ServerException $e) {
                // Handle 500 internal server error
                return response()->json(['error' => 'Failed to fetch data from the server'], 500);
            } catch (\Exception $e) {
                // Handle other exceptions
                return response()->json(['error' => 'An error occurred'], 500);
            }
        }
}
