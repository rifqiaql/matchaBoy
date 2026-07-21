<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; // WAJIB: Untuk manipulasi rentang waktu 7 hari

// WAJIB SESUAIKAN NAMA MODEL INI DENGAN YANG ADA DI FOLDER app/Models LU!
use App\Models\BahanBaku;
use App\Models\Product;
use App\Models\Order; // WAJIB: Untuk narik data transaksi harian

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
        $esBatu = BahanBaku::where('nama_bahan', 'es batu')->first(); 
        
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

        // =====================================================================
        // 3. LOGIKA GRAFIK DASHBOARD (7 Hari Terakhir)
        // =====================================================================
        $salesData = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(id) as total_transactions')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(6)->startOfDay())
            ->groupBy('date')
            ->get();

        $chartLabels = [];
        $chartData = [];

        // Looping mundur dari 6 hari lalu sampai hari ini (0)
        for ($i = 6; $i >= 0; $i--) {
            $tanggal = Carbon::now()->subDays($i)->format('Y-m-d');
            
            // Format label hari menjadi format singkat (misal: 15 Jul, 16 Jul)
            // Ini jauh lebih profesional dan akurat daripada sekadar 'MON', 'TUE' statis
            $labelHari = Carbon::now()->subDays($i)->isoFormat('D MMM'); 
            
            $chartLabels[] = $labelHari;
            
            // Cari data di database yang cocok dengan tanggal di loop
            $transaksiHariIni = $salesData->firstWhere('date', $tanggal);
            
            // Jika ada transaksi masukkan angkanya, jika tidak ada masukkan 0
            $chartData[] = $transaksiHariIni ? $transaksiHariIni->total_transactions : 0;
        }

        // Lempar semua variabel data ke view dashboard
        return view('dashboard.index', compact(
            'matcha',
            'fullCream',
            'strawberry',
            'esBatu',
            'topProducts',
            'chartLabels', // Data array Label Sumbu X (Tanggal)
            'chartData'    // Data array Sumbu Y (Jumlah Transaksi)
        ));
    }
}