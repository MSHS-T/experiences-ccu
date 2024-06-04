<?php

use App\Http\Controllers\BookSlotController;
use App\Http\Controllers\CancelBookingController;
use App\Http\Controllers\ConfirmBookingController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\ManipulationController;
use App\Http\Controllers\ManipulationSlotsController;
use App\Http\Controllers\MyBookingsController;
use App\Livewire\Login;
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

Route::get('/connexion', Login::class)->name('subject_login');
Route::get('/deconnexion', LogoutController::class)->name('subject_logout');

Route::get('/mes-inscriptions', MyBookingsController::class)->name('my_bookings');
Route::get('/confirmer/{booking}', ConfirmBookingController::class)->name('confirm_booking');
Route::get('/annuler/{booking}', CancelBookingController::class)->name('cancel_booking');

Route::view('/contact', 'contact')->name('contact');
Route::post('/contact', ContactController::class)->name('contact_send');
Route::view('/mentions-legales', 'legal')->name('legal');
Route::view('/politique-cookies', 'cookies')->name('cookies');
