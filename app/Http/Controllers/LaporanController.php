<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Order;
use App\Models\BahanBaku;
use Carbon\Carbon; // Wajib untuk manipulasi waktu
use Illuminate\Support\Facades\DB; // Wajib untuk raw query

class LaporanController extends Controller
{
    /**
     * Menampilkan Halaman Analytics & Stock Report
     */
    public function index(): View
    {
        // 1. Ambil data bahan baku untuk tabel Restock Planning
        $ingredients = BahanBaku::all();

        // 2. Hitung Total Orders riil dari database (Kumulatif)
        $totalOrders = Order::count();

        // 3. Ambil bahan mentah spesifik dari database untuk Waste Identification
        $milkStock = BahanBaku::where('nama_bahan', 'like', '%susu%')
            ->orWhere('nama_bahan', 'like', '%milk%')
            ->first();

        $oatStock = BahanBaku::where('nama_bahan', 'like', '%oat%')->first();

        // ====================================================================
        // LOGIKA BARU: Analitik Tren Permintaan (7 Hari Terakhir)
        // ====================================================================

        // Tarik data mentah dari database yang digrup per tanggal (hanya 7 hari terakhir)
        $salesData = Order::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(id) as total_transactions')
        )
            ->where('created_at', '>=', Carbon::now()->subDays(6)->startOfDay())
            ->groupBy('date')
            ->get();

        $chartLabels = [];
        $chartData = [];

        // Looping presisi: Membangun kerangka waktu 7 hari ke belakang tanpa ada yang terlewat
        for ($i = 6; $i >= 0; $i--) {
            $tanggal = Carbon::now()->subDays($i)->format('Y-m-d');
            $labelHari = Carbon::now()->subDays($i)->format('d M'); // Format misal: 21 Jul

            $chartLabels[] = $labelHari;

            // Cocokkan data database dengan kerangka hari (isi 0 jika tidak ada transaksi)
            $transaksiHariIni = $salesData->firstWhere('date', $tanggal);
            $chartData[] = $transaksiHariIni ? $transaksiHariIni->total_transactions : 0;
        }

        // Lempar semua variabel ke view laporan, termasuk data array grafik
        return view('laporan.index', compact(
            'ingredients',
            'totalOrders',
            'milkStock',
            'oatStock',
            'chartLabels',
            'chartData'
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
