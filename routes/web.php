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
| ZONA 2: OPERASIONAL HARIAN (Admin & Karyawan)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Autentikasi & Dashboard
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Keranjang & Transaksi Kasir
    Route::get('/keranjang', [ProductController::class, 'index'])->name('keranjang.index');
    Route::post('/checkout', [OrderController::class, 'checkout'])->name('checkout.process');

    // Gudang Operasional: Karyawan BISA Lihat Stok & Input Barang Masuk (Restock)
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::post('/{id}/tambah-stok', [InventoryController::class, 'tambahStok'])->name('tambah-stok');
    });
});

/*
|--------------------------------------------------------------------------
| ZONA 3: MANAJEMEN ENTERPRISE & MASTER DATA (KHUSUS ADMIN)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->group(function () {

    // Gudang Akses Dewa: Tambah Bahan Baku Baru, Edit, & Hapus (Dilarang untuk Karyawan)
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/add', [InventoryController::class, 'create'])->name('create');
        Route::post('/', [InventoryController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [InventoryController::class, 'edit'])->name('edit');
        Route::put('/{id}', [InventoryController::class, 'update'])->name('update');
        Route::delete('/{id}', [InventoryController::class, 'destroy'])->name('destroy');
        Route::get('/export', [InventoryController::class, 'export'])->name('export');
    });

    // Manajemen Katalog Produk & Resep (BOM)
    Route::prefix('products')->name('products.')->group(function () {
        Route::post('/', [ProductController::class, 'store'])->name('store');
        Route::put('/{id}', [ProductController::class, 'update'])->name('update');
        Route::delete('/{id}', [ProductController::class, 'destroy'])->name('destroy');
    });

    // Analitik & Pelaporan Omzet/Moving Average
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/', [LaporanController::class, 'index'])->name('index');
        Route::get('/export', [LaporanController::class, 'exportCSV'])->name('export');
    });
});
