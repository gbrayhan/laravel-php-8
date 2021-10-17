<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/login', [AuthController::class, 'loginStatus'])->name('login');
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);
});


Route::group([
    'prefix' => 'person'
], function ($router) {
    Route::post('/', [PersonController::class, 'store']);
    Route::post('/associate-person', [PersonController::class, 'associatePerson']);
    Route::get('/{id}', [PersonController::class, 'showByID']);
    Route::put('/{id}', [PersonController::class, 'update']);
});


Route::group([
    'prefix' => 'account'
], function ($router) {
    Route::post('/', [AccountController::class, 'store']);
    Route::get('/{id}', [AccountController::class, 'showByID']);
});


Route::group([
    'prefix' => 'transaction'
], function ($router) {
    Route::post('/', [TransactionController::class, 'store']);
    Route::get('/own-user', [TransactionController::class, 'showOwnUser']);
    Route::get('/{id}', [TransactionController::class, 'showByID']);
});


