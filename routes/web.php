<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::middleware(['auth'])->group(function () {
    Route::post('/save-token', 'GithubEncryptController');
    Route::get('/decrypted-token', 'GithubDecryptController');
    Route::get('/starred-repos', 'GithubRepoController');
    //Route::post('/save-token', 'UserController');
});
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
