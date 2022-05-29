<?php

use App\Http\Controllers\EmailVerificationNotificationController;
use App\Http\Controllers\EmailVerificationPromptController;
use App\Http\Controllers\VerifyEmailController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\CalendarVerify;
use App\Http\Middleware\CategoryVerify;
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

Route::middleware(['guest'])->group(function () {
    Route::name('auth.')->group(function () {
        Route::get('/login', [AuthController::class, 'index'])->name('login');
        Route::get('/signup', [AuthController::class, 'signup'])->name('signup');

        Route::post('/register', [AuthController::class, 'register'])->name('register');
        Route::post('/authenticate', [AuthController::class, 'authenticate'])->name('authenticate');
    });
});

Route::middleware(['auth'])->group(function () {
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::name('verification.')->group(function () {
        Route::get('verify-email', [EmailVerificationPromptController::class, '__invoke'])->name('notice');

        Route::middleware(['throttle:6,1'])->group(function () {
            Route::get('verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])->middleware(['signed'])->name('verify');
            Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])->name('send');
        });
    });


    Route::middleware('verified')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/jasperreport', function(){
            return view('jasperreport');
        })->name('jasperreport.index');

        Route::get('/export_events', function(){
            return view('export_events');
        })->name('export.events');

        Route::name('user.')->group(function () {
            Route::get('/me', [UserController::class, 'me'])->name('me');
            Route::get('/all_users', [UserController::class, 'getAllUsers'])->name('all');
            Route::get('/become_calendar_helper/{token}', [UserController::class, 'becomeHelper'])->name('become.helper');

            Route::name('category.')->group(function () {
                Route::get('/categories', [CategoryController::class, 'index'])->name('index');
                Route::get('/user_categories', [CategoryController::class, 'getCategories'])->name('all');
                Route::post('/category_store', [CategoryController::class, 'store'])->name('store');
                Route::middleware(CategoryVerify::class)->group(function () {
                    Route::patch('/category_update', [CategoryController::class, 'update'])->name('update');
                    Route::delete('/category_delete', [CategoryController::class, 'destroy'])->name('delete');
                });
            });
        });

        Route::name('calendar.')->group(function () {
            Route::get('/calendars', [CalendarController::class, 'index'])->name('all');

            Route::post('/calendar_store', [CalendarController::class, 'store'])->name('store');

            Route::middleware(CalendarVerify::class)->group(function () {
                Route::get('/calendar_get', [CalendarController::class, 'getCalendar'])->name('get');
                Route::get('/calendar_edit/{id}', [CalendarController::class, 'edit'])->name('edit');
                Route::get('/calendar_events', [CalendarController::class, 'getCalendarEvents'])->name('events');
                Route::post('/calendar_event_store', [EventController::class, 'store'])->name('event.store');
            });

            Route::middleware(OwnerCalendarVerify::class)->group(function () {
                Route::name('google.')->group(function() {
                    Route::get('/google_login', [GoogleController::class, 'getAuthUrl'])->name('login');
                    Route::get('/post_login', [GoogleController::class, 'postLogin'])->name('postLogin');

                    Route::name('calendar.')->group(function () {
                        Route::get('/publish_calendar', [GoogleController::class, 'publishGoogleCalendar'])->name('publish');
                        Route::get('/update_calendar', [GoogleController::class, 'updateGoogleCalendar'])->name('update');
                        Route::get('/delete_calendar', [GoogleController::class, 'destroyGoogleCalendar'])->name('delete');
                        Route::get('/calendar_colors', [GoogleController::class, 'getGoogleCalendarColors'])->name('colors');
                    });

                    Route::name('event.')->group(function () {
                        Route::get('/publish_event', [GoogleController::class, 'publishGoogleCalendarEvent'])->name('publish');
                        Route::get('/delete_event_google', [GoogleController::class, 'destroyGoogleCalendarEvent'])->name('destroy');
                        Route::get('/update_event_google', [GoogleController::class, 'updateGoogleCalendarEvent'])->name('update');
                    });
                });

                Route::get('/calendar_publish/{id}', [CalendarController::class, 'publishCalendar'])->name('publish');
                Route::post('/upload_targets', [CalendarController::class, 'uploadTargets'])->name('targets.upload');
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
});
