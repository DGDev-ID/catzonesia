<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::prefix('master')->middleware(['auth', 'verified'])->group(function () {
    // Category Routes
    Route::get('category', function () {
        return Inertia::render('master/category/Index');
    })->name('master.category.index');

    Route::get('category/create', function () {
        return Inertia::render('master/category/Create');
    })->name('master.category.create');

    Route::get('category/{id}/edit', function ($id) {
        return Inertia::render('master/category/Edit', ['id' => $id]);
    })->name('master.category.edit');

    // Unit Routes
    Route::get('unit', function () {
        return Inertia::render('master/unit/Index');
    })->name('master.unit.index');

    Route::get('unit/create', function () {
        return Inertia::render('master/unit/Create');
    })->name('master.unit.create');

    Route::get('unit/{id}/edit', function ($id) {
        return Inertia::render('master/unit/Edit', ['id' => $id]);
    })->name('master.unit.edit');

    // Product Routes
    Route::get('product', function () {
        return Inertia::render('master/product/Index');
    })->name('master.product.index');

    Route::get('product/create', function () {
        return Inertia::render('master/product/Create');
    })->name('master.product.create');

    Route::get('product/{id}/edit', function ($id) {
        return Inertia::render('master/product/Edit', ['id' => $id]);
    })->name('master.product.edit');

    // Package Routes
    Route::get('package', function () {
        return Inertia::render('master/package/Index');
    })->name('master.package.index');

    Route::get('package/create', function () {
        return Inertia::render('master/package/Create');
    })->name('master.package.create');

    Route::get('package/{id}/edit', function ($id) {
        return Inertia::render('master/package/Edit', ['id' => $id]);
    })->name('master.package.edit');
});