<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ThreadController;
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

// Auth routes
Route::group(['prefix' => 'auth'], function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    
    // Protected routes
    Route::group(['middleware' => 'auth:api'], function() {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me', [AuthController::class, 'me']);
    });
});

// Thread routes
Route::group(['middleware' => 'auth:api'], function() {
    Route::get("threads", [ThreadController::class, 'list']);
    Route::get("threads/{id}", [ThreadController::class, 'get']);
    Route::post("threads", [ThreadController::class, 'create']);
    Route::put("threads/{id}", [ThreadController::class, 'update']);
    Route::delete("threads/{id}", [ThreadController::class, 'delete']);
});

// Post routes
Route::group(['middleware' => 'auth:api'], function() {
    // API endpoints for posts will be added here
});