<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\DashboardController;
use App\Http\Middleware\CalendarVerify;
use Illuminate\Support\Facades\Route;

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

Route::middleware(['guest'])->group(function(){
    Route::name('auth.')->group(function () {

        Route::get('/login', [AuthController::class, 'index'])->name('login');
        Route::get('/signin', [AuthController::class, 'signin'])->name('signin');

        Route::post('/register', [AuthController::class, 'register'])->name('register');
        Route::post('/authenticate', [AuthController::class, 'authenticate'])->name('authenticate');
    });
});

Route::middleware(['auth'])->group(function(){
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::name('calendar.')->group(function(){
        Route::get('/calendars', [CalendarController::class,'index'])->name('all');
        Route::post('/calendar_store', [CalendarController::class, 'store'])->name('store');
        Route::get('/calendar_edit/{id}', [CalendarController::class, 'edit'])->name('edit')->middleware(CalendarVerify::class);
    });
});
