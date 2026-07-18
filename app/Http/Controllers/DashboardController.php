<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

// WAJIB SESUAIKAN NAMA MODEL INI DENGAN YANG ADA DI FOLDER app/Models LU!
use App\Models\BahanBaku;
use App\Models\Product;
use App\Models\OrderDetail; // Model detail transaksi kasir lu

class DashboardController extends Controller
{
    public function index(): View
    {
        // =====================================================================
        // 1. DATA CARD ATAS (STOK GUDANG / BAHAN BAKU)
        // =====================================================================
        $matcha = BahanBaku::where('nama_bahan', 'bubuk matcha')->first();
        $fullCream = BahanBaku::where('nama_bahan', 'susu full cream')->first();
        $strawberry = BahanBaku::where('nama_bahan', 'strawberry slay golden')->first();
        $esBatu = BahanBaku::where('nama_bahan', 'es batu')->first(); // Menggantikan Oats/Gula
        // =====================================================================
        // 2. DATA CARD BAWAH KANAN (TOP 3 PRODUCTS)
        // =====================================================================
        $topProducts = DB::table('order_items') // <-- Menggunakan nama tabel asli lu
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_sold')
            ->limit(3)
            ->get();

        // Lempar semua variabel data ke view dashboard
        return view('dashboard.index', compact(
            'matcha',
            'fullCream',
            'strawberry',
            'esBatu',
            'topProducts'
        ));
    }
}
