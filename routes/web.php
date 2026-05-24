<?php

use App\Http\Controllers\CashierController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Master\CategoryController;
use App\Http\Controllers\Master\PackageController;
use App\Http\Controllers\Master\ProductController;
use App\Http\Controllers\Master\UnitController;
use App\Http\Controllers\ProductMovementController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserManagement\UserController;
use App\Http\Controllers\UserManagement\RoleController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('home');

Route::get('dashboard', DashboardController::class)->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::prefix('master')->name('master.')->group(function () {
        Route::resource('category', CategoryController::class)->except(['show']);
        Route::resource('unit', UnitController::class)->except(['show']);
        Route::resource('product', ProductController::class)->except(['show']);
        Route::resource('package', PackageController::class)->except(['show']);
    });

    Route::resource('product-movement', ProductMovementController::class)->except(['create', 'edit']);
    Route::post('product-movement/{product}/adjustment', [ProductMovementController::class, 'adjustment'])->name('product-movement.adjustment');

    Route::get('transaction/export', [TransactionController::class, 'export'])->name('transaction.export');
    Route::resource('transaction', TransactionController::class)->except(['edit']);
    Route::post('transaction/{id}/success', [TransactionController::class, 'markAsSuccess'])->name('transaction.markAsSuccess');
    Route::post('transaction/{id}/failed', [TransactionController::class, 'markAsFailed'])->name('transaction.markAsFailed');

    Route::resource('cashier', CashierController::class)->only(['index']);
    Route::get('cashier/transaction/{transaction}', [CashierController::class, 'showTransaction'])->name('cashier.transaction.show');
    Route::post('cashier/checkout', [CashierController::class, 'checkout'])->name('cashier.checkout');
    Route::post('cashier/{id}/success', [CashierController::class, 'markAsSuccess'])->name('cashier.markAsSuccess');
    Route::post('cashier/{id}/failed', [CashierController::class, 'markAsFailed'])->name('cashier.markAsFailed');

    Route::prefix('user-management')->name('user-management.')->group(function () {
        Route::resource('user', UserController::class)->except(['show']);
        Route::resource('role', RoleController::class)->except(['show']);
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
