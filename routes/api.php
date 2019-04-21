<?php

use Illuminate\Http\Request;

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

Route::post('/login', 'Auth\LoginController@login');
Route::post('/register', 'Auth\LoginController@register');
Route::get('/me', 'Auth\LoginController@me');
Route::get('/logout', 'Auth\LoginController@logout');

Route::middleware('auth:api')->group(function () {

    Route::get('/user/names', 'UserController@names');

    Route::get('/users', 'UserController@index')->middleware('can:viewAll,App\User');
    Route::put('/user', 'UserController@update')->middleware('can:update,App\User');

    Route::get('/transactions/my', 'TransactionController@index');
    Route::get('/transactions/all', 'TransactionController@all')->middleware('can:viewAll,App\Transaction');
    Route::put('/transaction', 'TransactionController@update')->middleware('can:update,App\Transaction');
    Route::post('/transaction', 'TransactionController@create');
});
