<?php

use Illuminate\Http\Request;
use app\Http\Controllers\UserController;

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


Route::prefix('api')->group(function () {
    Route::post('sessions', 'Auth\LoginController@sessionCreate');
    Route::post('users', 'UserController@store');
});

Route::middleware(['apitoken'])->prefix('api')->group(function () {

    Route::delete('sessions', 'Auth\LoginController@sessionDestroy');
    Route::get('users', 'UserController@users');


});
