<?php

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

Route::prefix('reports')->group(function () {
    Route::get('transaction', 'App\Http\Controllers\Reports\TransactionController@index');
    Route::post('transaction/getData', 'App\Http\Controllers\Reports\TransactionController@getData');
    Route::post('transaction/exportcsv', 'App\Http\Controllers\Reports\TransactionController@exportCsv');
});

