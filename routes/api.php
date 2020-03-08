<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('register', 'AuthController@register');
Route::post('resend-activation', 'AuthController@resendActivation');
Route::post('forgot-password', 'AuthController@requestForgotPassword');
Route::post('activate-account', 'AuthController@activateAccount');
Route::post('reset-password', 'AuthController@resetPassword');

Route::middleware('auth:api')->group(function() {
	Route::get('profile', 'UserController@getProfile');
    Route::patch('profile', 'UserController@updateProfile');
    Route::patch('change-password', 'AuthController@changePassword');
    Route::get('notifications', 'UserController@getNotifications');
	Route::get('users', 'UserController@getUsers');
	Route::get('users/{username}', 'UserController@getSingleUser');
	Route::patch('users/{id}/follow', 'UserController@followUser');
	Route::post('users/{id}/followers', 'UserController@getFollowers');
	Route::post('users/{id}/following', 'UserController@getFollowing');
	Route::get('users/{username}/posts', 'PostController@getUserPosts');
    Route::get('saved-posts', 'PostController@getSavedPosts');
    Route::get('posts/explore', 'PostController@getExplorePosts');
    Route::get('posts', 'PostController@getPosts');
    Route::post('posts', 'PostController@createPost');
    Route::get('posts/{slug}', 'PostController@getSinglePost');
    Route::delete('posts/{id}', 'PostController@deletePost');
    Route::get('stories', 'StoryController@getStories');
    Route::post('stories', 'StoryController@createStory');
    Route::post('stories/{id}/view', 'StoryController@viewStory');
    Route::post('posts/{id}/comments', 'PostController@postComment');
    Route::patch('posts/{id}/likes', 'PostController@likePost');
    Route::patch('posts/{id}/save', 'PostController@savePost');
    Route::get('posts/{id}/likes', 'PostController@getLikes');
    Route::get('suggested-people', 'UserController@getSuggestedPeople');
    Route::get('messages', 'MessageController@getMessages');
    Route::get('messages/{username}', 'MessageController@getMessageList');
    Route::post('messages/{username}', 'MessageController@postMessage');
    Route::patch('messages/{username}/read', 'MessageController@readMessages');
    Route::post('realtime', 'RealtimeController@getRealtime');
});
