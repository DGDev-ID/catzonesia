<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::prefix('cashier')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', function () {
        return Inertia::render('cashier/Index');
    })->name('cashier.index');
});