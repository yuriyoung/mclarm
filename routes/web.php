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

// Social Auth
Route::group(['namespace' => 'Auth'], function () {
    Route::get('oauth/{provider}', 'LoginController@oauthRedirect')->name('oauth.redirect');
    Route::get('oauth/{provider}/callback', 'LoginController@oauthCallback')->name('oauth.callback');
});
