<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Order;
use App\Models\BahanBaku;

class LaporanController extends Controller
{
    /**
     * Menampilkan Halaman Analytics & Stock Report
     */
    public function index(): View
    {
        // 1. Ambil data bahan baku untuk tabel Restock Planning
        $ingredients = BahanBaku::all();

        // 2. Hitung Total Orders riil dari database
        $totalOrders = Order::count();

        // 3. Ambil bahan mentah spesifik dari database untuk Waste Identification (Dinamis)
        $milkStock = BahanBaku::where('nama_bahan', 'like', '%susu%')
            ->orWhere('nama_bahan', 'like', '%milk%')
            ->first();

        $oatStock = BahanBaku::where('nama_bahan', 'like', '%oat%')->first();

        // Lempar semua variabel ke view laporan
        return view('laporan.index', compact('ingredients', 'totalOrders', 'milkStock', 'oatStock'));
    }

    /**
     * Mengekspor data riwayat transaksi ke CSV/Excel
     */
    public function exportCSV()
    {
        // Tarik data order beserta relasi kasir (user)
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

            // Tulis header dengan delimiter titik koma (;) agar rapi di Excel Indonesia
            fputcsv($file, $columns, ";");

            // Looping dan tulis data transaksi
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
