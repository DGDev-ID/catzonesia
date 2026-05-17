<?php

use Illuminate\Support\Facades\Route;

// Master Category Routes
Route::prefix('master/category')->group(function () {
    Route::get('/', 'App\Http\Controllers\Master\CategoryController@index');
    Route::post('/', 'App\Http\Controllers\Master\CategoryController@store');
    Route::get('/{id}', 'App\Http\Controllers\Master\CategoryController@show');
    Route::put('/{id}', 'App\Http\Controllers\Master\CategoryController@update');
    Route::delete('/{id}', 'App\Http\Controllers\Master\CategoryController@destroy');
});

// Master Unit Routes
Route::prefix('master/unit')->group(function () {
    Route::get('/', 'App\Http\Controllers\Master\UnitController@index');
    Route::post('/', 'App\Http\Controllers\Master\UnitController@store');
    Route::get('/{id}', 'App\Http\Controllers\Master\UnitController@show');
    Route::put('/{id}', 'App\Http\Controllers\Master\UnitController@update');
    Route::delete('/{id}', 'App\Http\Controllers\Master\UnitController@destroy');
});

// Master Product Routes
Route::prefix('master/product')->group(function () {
    Route::get('/', 'App\Http\Controllers\Master\ProductController@index');
    Route::post('/', 'App\Http\Controllers\Master\ProductController@store');
    Route::get('/{id}', 'App\Http\Controllers\Master\ProductController@show');
    Route::put('/{id}', 'App\Http\Controllers\Master\ProductController@update');
    Route::delete('/{id}', 'App\Http\Controllers\Master\ProductController@destroy');
});

// Master Package Routes
Route::prefix('master/package')->group(function () {
    Route::get('/', 'App\Http\Controllers\Master\PackageController@index');
    Route::post('/', 'App\Http\Controllers\Master\PackageController@store');
    Route::get('/{id}', 'App\Http\Controllers\Master\PackageController@show');
    Route::put('/{id}', 'App\Http\Controllers\Master\PackageController@update');
    Route::delete('/{id}', 'App\Http\Controllers\Master\PackageController@destroy');
});

// Product Movement Routes
Route::prefix('product-movement')->group(function () {
    Route::get('/', 'App\Http\Controllers\ProductMovementController@index');
    Route::post('/', 'App\Http\Controllers\ProductMovementController@store');
    Route::get('/{id}', 'App\Http\Controllers\ProductMovementController@show');
    Route::put('/{id}', 'App\Http\Controllers\ProductMovementController@update');
    Route::delete('/{id}', 'App\Http\Controllers\ProductMovementController@destroy');
    Route::post('/{id}/adjustment', 'App\Http\Controllers\ProductMovementController@adjustment');
});

// Transaction Routes
Route::prefix('transaction')->group(function () {
    Route::get('/', 'App\Http\Controllers\TransactionController@index');
    Route::post('/', 'App\Http\Controllers\TransactionController@store');
    Route::get('/{id}', 'App\Http\Controllers\TransactionController@show');
    Route::put('/{id}', 'App\Http\Controllers\TransactionController@update');
    Route::delete('/{id}', 'App\Http\Controllers\TransactionController@destroy');
    Route::post('/{id}/success', 'App\Http\Controllers\TransactionController@markAsSuccess');
    Route::post('/{id}/failed', 'App\Http\Controllers\TransactionController@markAsFailed');
    Route::get('/export', 'App\Http\Controllers\TransactionController@export');
});

// Cashier Routes
Route::prefix('cashier')->group(function () {
    Route::get('/', 'App\Http\Controllers\CashierController@index');
    Route::post('/checkout', 'App\Http\Controllers\CashierController@checkout');
    Route::post('/{id}/success', 'App\Http\Controllers\CashierController@markAsSuccess');
    Route::post('/{id}/failed', 'App\Http\Controllers\CashierController@markAsFailed');
});