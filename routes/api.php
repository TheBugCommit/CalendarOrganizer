<?php

use App\Http\Controllers\API\EventsControllerAPI;
use App\Http\Controllers\API\AuthControllerAPI;
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

Route::get('getToken', [AuthControllerAPI::class, 'getToken']);
Route::middleware(['auth:api'])->group(function(){
    Route::get('export_events',  [EventsControllerAPI::class, 'export']);
    Route::post('logout', [AuthControllerAPI::class, 'logout']);
});
