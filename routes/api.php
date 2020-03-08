<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::group(['prefix' => 'auth', 'namespace' => 'Api'], function () {
    Route::post('login', 'AuthController@login')->name('api.auth.login');
    Route::post('register', 'AuthController@register')->name('api.auth.register');
    Route::middleware('jwt.auth')->post('logout', 'AuthController@logout')->name('api.auth.logout');;
    Route::middleware('jwt.auth')->get('me', 'AuthController@me')->name('api.auth.me');
    Route::middleware('jwt.refresh')->post('refresh', 'AuthController@refresh')->name('api.auth.refresh');
});

Route::group(['prefix' => 'v1', 'middleware' => 'jwt.auth', 'namespace' => 'Api'], function () {
    Route::apiResource('users', 'UserController');
    Route::get('users/{user}/detail', 'UserController@detail')->name('users.detail');
});

/**
 * protected $middlewareAliases = [
    'jwt.auth' => Authenticate::class,
    'jwt.check' => Check::class,
    'jwt.refresh' => RefreshToken::class, // response a token at the header
    'jwt.renew' => AuthenticateAndRenew::class,
];
 */
