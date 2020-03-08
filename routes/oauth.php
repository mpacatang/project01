<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Oauth Routes
|--------------------------------------------------------------------------
|
| Here is where you can register Oauth routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "oauth" middleware group.
|
*/

Route::post('oauth/token', 'Auth\CustomAccessTokenController@issueUserToken');