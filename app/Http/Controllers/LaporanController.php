<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Order;
use App\Models\BahanBaku;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    /**
     * Menampilkan Halaman Analytics & Stock Report
     */
    public function index(): View
    {
        // 1. Ambil data bahan baku
        $ingredients = BahanBaku::all();

        // 2. Hitung Total Orders riil
        $totalOrders = Order::count();

        // 3. Ambil bahan mentah spesifik
        $milkStock = BahanBaku::where('nama_bahan', 'like', '%susu%')
            ->orWhere('nama_bahan', 'like', '%milk%')
            ->first();

        $oatStock = BahanBaku::where('nama_bahan', 'like', '%oat%')->first();

        // ====================================================================
        // DATA GRAFIK: 7 HARI (WEEKLY)
        // ====================================================================
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
            $labelHari = Carbon::now()->subDays($i)->format('d M');

            $chartLabels[] = $labelHari;

            $transaksiHariIni = $salesData->firstWhere('date', $tanggal);
            $chartData[] = $transaksiHariIni ? $transaksiHariIni->total_transactions : 0;
        }

        // ====================================================================
        // DATA GRAFIK: 30 HARI (MONTHLY)
        // ====================================================================
        $monthlySalesData = Order::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(id) as total_transactions')
        )
            ->where('created_at', '>=', Carbon::now()->subDays(29)->startOfDay())
            ->groupBy('date')
            ->get();

        $chartLabelsMonthly = [];
        $chartDataMonthly = [];

        for ($i = 29; $i >= 0; $i--) {
            $tanggal = Carbon::now()->subDays($i)->format('Y-m-d');
            $labelHari = Carbon::now()->subDays($i)->format('d M');

            $chartLabelsMonthly[] = $labelHari;

            $transaksiHariIni = $monthlySalesData->firstWhere('date', $tanggal);
            $chartDataMonthly[] = $transaksiHariIni ? $transaksiHariIni->total_transactions : 0;
        }

        // Lempar SEMUA variabel ke view (Inilah yang tadi bikin sistem lu error karena gak ada)
        return view('laporan.index', compact(
            'ingredients',
            'totalOrders',
            'milkStock',
            'oatStock',
            'chartLabels',
            'chartData',
            'chartLabelsMonthly', // Solusi Error Lu
            'chartDataMonthly'    // Solusi Error Lu
        ));
    }

    /**
     * Mengekspor data riwayat transaksi ke CSV/Excel
     */
    public function exportCSV()
    {
        $orders = Order::with(['user'])->latest()->get();
        $filename = "Laporan_Penjualan_MatchaBoy_" . date('Ymd_His') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['No. Invoice', 'Tanggal Transaksi', 'Kasir', 'Subtotal', 'Pajak', 'Total Bayar', 'Status'];

        $callback = function () use ($orders, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns, ";");

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->invoice_number,
                    $order->created_at->format('Y-m-d H:i:s'),
                    $order->user->name ?? 'Sistem',
                    $order->subtotal,
                    $order->tax,
                    $order->total_price,
                    $order->status
                ], ";");
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
