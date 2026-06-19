<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LaporanController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
    Route::get('/signup', [RegisterController::class, 'create'])->name('register');
    Route::post('/signup', [RegisterController::class, 'store'])->name('register.store');
});

Route::middleware('auth')->group(function () {

    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('keranjang')->name('keranjang.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::get('/add', [CartController::class, 'create'])->name('create');
        Route::post('/', [CartController::class, 'store'])->name('store');
        Route::delete('/{cart}', [CartController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::get('/add', [InventoryController::class, 'create'])->name('create');
        Route::post('/', [InventoryController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [InventoryController::class, 'edit'])->name('edit');
        Route::put('/{id}', [InventoryController::class, 'update'])->name('update');
        Route::delete('/{id}', [InventoryController::class, 'destroy'])->name('destroy');
        Route::get('/export', [InventoryController::class, 'export'])->name('export');
    });

    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');

});
