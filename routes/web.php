<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LaporanController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LoginController::class, 'create'])->name('login');
Route::get('/signup', [RegisterController::class, 'create'])->name('register');

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
});

Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
