<?php

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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});
Route::prefix('user')->group(function () {
    Route::post('register', 'App\Http\Controllers\Api\UserController@register');
});

Route::prefix('quotation')->group(function () {
    Route::post('upload', 'App\Http\Controllers\Api\ExchangeRateController@upload');
});

Route::prefix('transaction')->group(function () {
    Route::post('refill', 'App\Http\Controllers\Api\TransactionController@refill');
    Route::post('transfer', 'App\Http\Controllers\Api\TransactionController@transfer');
});
