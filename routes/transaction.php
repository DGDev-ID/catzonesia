<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::prefix('transaction')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', function () {
        return Inertia::render('transaction/Index');
    })->name('transaction.index');

    Route::get('/{id}', function ($id) {
        return Inertia::render('transaction/Show', ['id' => $id]);
    })->name('transaction.show');
});