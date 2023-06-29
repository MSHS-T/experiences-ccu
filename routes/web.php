<?php

use App\Http\Controllers\BookSlotController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ManipulationController;
use App\Http\Controllers\ManipulationSlotsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', HomeController::class)->name('home');
// Route::get('/manipulations', ManipulationController::class)->name('manipulations');
Route::get('/manipulations/{manipulation}/creneaux', ManipulationSlotsController::class)->name('manipulation_slots');
Route::post('/book/{slot}', BookSlotController::class)->name('book_slot');

Route::view('/mentions-legales', 'legal')->name('legal');
Route::view('/politique-cookies', 'cookies')->name('cookies');
