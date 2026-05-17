<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::prefix('product-movement')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', function () {
        return Inertia::render('product-movement/Index');
    })->name('product-movement.index');

    Route::get('/{id}', function ($id) {
        return Inertia::render('product-movement/Show', ['id' => $id]);
    })->name('product-movement.show');
});