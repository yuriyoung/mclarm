<?php

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

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// SocialAccount Auth
Route::group(['namespace' => 'Auth'], function () {
    Route::get('auth/login', 'LoginController@login')->name('auth.login');
    Route::get('auth/logout', 'LoginController@logout')->name('auth.logout');
    Route::get('oauth/{provider}', 'OAuthController@oauthRedirect')->name('oauth.redirect');
    Route::get('oauth/{provider}/callback', 'OAuthController@oauthCallback')->name('oauth.callback');
});
