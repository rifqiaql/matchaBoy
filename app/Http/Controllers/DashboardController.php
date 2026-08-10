<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; 

use App\Models\BahanBaku;
use App\Models\Product;
use App\Models\Order; 

class DashboardController extends Controller
{
    public function index(): View
    {
        $stokGudang = BahanBaku::orderBy('nama_bahan', 'asc')->get();
        
        $topProducts = DB::table('order_items') 
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_sold')
            ->limit(3)
            ->get();

        $salesData = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(id) as total_transactions')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(6)->startOfDay())
            ->groupBy('date')
            ->get();

        $chartLabels = [];
        $chartData = [];

        for ($i = 6; $i >= 0; $i--) {
            $tanggal = Carbon::now()->subDays($i)->format('Y-m-d');
            $labelHari = Carbon::now()->subDays($i)->isoFormat('D MMM'); 
            
            $chartLabels[] = $labelHari;
            
            $transaksiHariIni = $salesData->firstWhere('date', $tanggal);
            
            $chartData[] = $transaksiHariIni ? $transaksiHariIni->total_transactions : 0;
        }

        // Deklarasi mutlak aktivitas transaksi untuk View
        $recentOrders = Order::with('user')->latest()->take(4)->get();

        return view('dashboard.index', compact(
            'stokGudang', 
            'topProducts',
            'chartLabels',
            'chartData',
            'recentOrders'
        ));
    }
}