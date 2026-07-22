<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\IngredientController;

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
});

Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');

// Jalur untuk memproses checkout dari tombol Bayar Sekarang
Route::post('/checkout', [OrderController::class, 'checkout'])->name('checkout');

// Jalur untuk menyimpan data produk baru dari form modal
Route::post('/products', [OrderController::class, 'storeProduct'])->name('products.store');

// Ganti rute get /keranjang lama lo dengan yang mengarah ke controller ini
Route::get('/keranjang', [ProductController::class, 'index'])->name('products.index');

// Rute penanganan aksi manipulasi produk (tambah, edit, delete)
Route::post('/products', [ProductController::class, 'store'])->name('products.store');
Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');

// Ganti baris rute GET keranjang lo menjadi seperti ini:
Route::get('/keranjang', [ProductController::class, 'index'])->name('keranjang.index');

// Pastiin ada ->name('keranjang.index') di ujungnya!
Route::get('/keranjang', [CartController::class, 'index'])->name('keranjang.index');
Route::post('/keranjang', [CartController::class, 'store'])->name('keranjang.store');

// Pastikan ditaruh di dalam middleware admin jika lu pakai
Route::post('/products/{product}/ingredients', [IngredientController::class, 'store'])->name('products.ingredients.store');



// Ganti rute laporan lu menjadi dua baris ini:
Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
Route::get('/laporan/export', [LaporanController::class, 'exportCSV'])->name('laporan.export');


// RUTE BARU: Untuk memproses Restock / Barang Masuk (POST)
Route::post('/inventory/{id}/tambah-stok', [InventoryController::class, 'tambahStok'])->name('inventory.tambah-stok');

Route::get('/laporan', [\App\Http\Controllers\LaporanController::class, 'index'])->name('laporan.index');
Route::get('/laporan/export-csv', [\App\Http\Controllers\LaporanController::class, 'exportCSV'])->name('laporan.export');
