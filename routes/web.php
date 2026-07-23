<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;

/*
|--------------------------------------------------------------------------
| ZONA 1: GUEST (Belum Login)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
    Route::get('/signup', [RegisterController::class, 'create'])->name('register');
    Route::post('/signup', [RegisterController::class, 'store'])->name('register.store');
});

/*
|--------------------------------------------------------------------------
| ZONA 2: OPERASIONAL KASIR (Admin & Karyawan)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Autentikasi & Dashboard
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Keranjang & Transaksi (Menggunakan ProductController untuk load UI kasir)
    Route::get('/keranjang', [ProductController::class, 'index'])->name('keranjang.index');

    // Proses Checkout AJAX
    Route::post('/checkout', [OrderController::class, 'checkout'])->name('checkout.process');
});

/*
|--------------------------------------------------------------------------
| ZONA 3: MANAJEMEN ENTERPRISE (KHUSUS ADMIN)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->group(function () {

    // Manajemen Inventaris & Gudang
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::get('/add', [InventoryController::class, 'create'])->name('create');
        Route::post('/', [InventoryController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [InventoryController::class, 'edit'])->name('edit');
        Route::put('/{id}', [InventoryController::class, 'update'])->name('update');
        Route::delete('/{id}', [InventoryController::class, 'destroy'])->name('destroy');
        Route::get('/export', [InventoryController::class, 'export'])->name('export');
        Route::post('/{id}/tambah-stok', [InventoryController::class, 'tambahStok'])->name('tambah-stok');
    });

    // Manajemen Katalog Produk & Resep (BOM)
    Route::prefix('products')->name('products.')->group(function () {
        Route::post('/', [ProductController::class, 'store'])->name('store');
        Route::put('/{id}', [ProductController::class, 'update'])->name('update');
        Route::delete('/{id}', [ProductController::class, 'destroy'])->name('destroy');
    });

    // Analitik & Pelaporan
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/', [LaporanController::class, 'index'])->name('index');
        Route::get('/export', [LaporanController::class, 'exportCSV'])->name('export');
    });
});
