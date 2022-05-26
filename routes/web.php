<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CalendarController;
//use App\Http\Controllers\CalendarHelpersController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\MailerController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\CalendarVerify;
use App\Http\Middleware\OwnerCalendarVerify;
use App\Http\Middleware\EventVerify;
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

    Route::name('user.')->group(function () {
        Route::get('/me', [UserController::class, 'me'])->name('me');
        Route::get('/all_users', [UserController::class, 'getAllUsers'])->name('all');
        Route::get('/categories', [UserController::class, 'getCategories'])->name('categories');
        Route::get('/become_calendar_helper/{token}', [UserController::class, 'becomeHelper'])->name('become.helper');
    });

    Route::name('email.')->group(function () {
        Route::post("/send_email", [MailerController::class, "send"])->name("send");
    });

    Route::name('calendar.')->group(function(){
        Route::get('/calendars', [CalendarController::class,'index'])->name('all');

        Route::post('/calendar_store', [CalendarController::class, 'store'])->name('store');

        Route::middleware(CalendarVerify::class)->group(function(){
            Route::get('/calendar_get', [CalendarController::class, 'getCalendar'])->name('get');
            Route::get('/calendar_edit/{id}', [CalendarController::class, 'edit'])->name('edit');
            Route::get('/calendar_events', [CalendarController::class, 'getCalendarEvents'])->name('events');
            Route::post('/calendar_event_store', [EventController::class, 'store'])->name('event.store');
        });

        Route::middleware(OwnerCalendarVerify::class)->group(function (){

            Route::post('/upload_targets/{id}', [CalendarController::class, 'uploadTargets'])->name('targets.upload');
            Route::patch('/calendar_update/{id}', [CalendarController::class, 'update'])->name('update');
            Route::delete('/calendar_destroy/{id}', [CalendarController::class, 'destory'])->name('destroy');

            Route::name('helpers.')->group(function () {
                Route::get('/editHelpers/{id}', [CalendarController::class, 'editHelpers'])->name('index');
                Route::get('/getHelpers', [CalendarController::class, 'getHelpers'])->name('get');
                Route::post('/addHelpers', [CalendarController::class, 'addHelpers'])->name('add');
                Route::delete('/removeHelpers', [CalendarController::class, 'removeHelper'])->name('remove');
            });
        });

        Route::middleware(EventVerify::class)->group(function () {
            Route::patch('/calendar_event_update', [EventController::class, 'update'])->name('event.update');
            Route::delete('/calendar_event_destroy', [EventController::class, 'destroy'])->name('event.destroy');
        });
    });
});
