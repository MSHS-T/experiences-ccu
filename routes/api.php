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

Route::group(['middleware' => 'apiheader'], function($router){
    Route::group(['prefix' => 'auth'], function ($router) {
        Route::post('login', 'AuthController@login')->name('login');
        Route::post('logout', 'AuthController@logout');
        Route::post('refresh', 'AuthController@refresh');
        Route::post('me', 'AuthController@me');
    });
    Route::apiResource('user', 'API\UserController');
    Route::apiResource('equipment', 'API\EquipmentController');
    Route::apiResource('manipulation', 'API\ManipulationController');
    Route::apiResource('plateau', 'API\PlateauController');
});
