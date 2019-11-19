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

Route::group(['prefix' => 'api'], function() {
    // all routes that don't need to go to react-router
});

Route::get('{reactRoute}', function () {
    return view('web'); // your start view
})->where('reactRoute', '^((?!api).)*$');