<?php

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

Route::group(['middleware' => 'apiheader'], function ($router) {
    Route::group(['prefix' => 'auth'], function ($router) {
        Route::post('login', 'AuthController@login')->name('login');
        Route::post('logout', 'AuthController@logout');
        Route::post('refresh', 'AuthController@refresh');
        Route::post('me', 'AuthController@me');
        Route::post('forgotpassword', 'AuthController@sendPasswordResetEmail');
        Route::post('resetpassword', 'AuthController@doReset');
    });
    Route::apiResource('user', 'API\UserController');
    Route::apiResource('equipment', 'API\EquipmentController');
    Route::get('manipulation/all', 'API\ManipulationController@all');
    Route::apiResource('manipulation', 'API\ManipulationController');
    Route::apiResource('plateau', 'API\PlateauController');

    Route::prefix('slot')->group(function () {
        Route::get('{manipulation}', 'API\SlotController@index');
        Route::post('{manipulation}/generate', 'API\SlotController@generate');
        Route::post('{manipulation}', 'API\SlotController@store');
        Route::delete('{slot}', 'API\SlotController@destroy');
        Route::get('{manipulation}/call_sheet', 'API\ExportController@callSheet');
    });

    Route::put('booking', 'API\BookingController@update');

    Route::get('bookinghistory', 'API\BookingHistoryController@index');
    Route::put('bookinghistory', 'API\BookingHistoryController@update');

    Route::get('settings', 'API\SettingController@index');
    Route::post('settings', 'API\SettingController@store');

    Route::prefix('profile')->group(function () {
        Route::get('/', 'API\ProfileController@index');
        Route::put('/', 'API\ProfileController@update');
    });
});
