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
     * Menampilkan Halaman Analytics, Stock Report & Audit SMA
     */
    public function index(Request $request): View
    {
        // TANGKAP PARAMETER TANGGAL DARI URL
        $endDate = $request->has('end_date')
            ? Carbon::parse($request->input('end_date'))
            : Carbon::now();

        // TANGKAP PARAMETER WINDOW (N) DARI URL
        // Jika tidak ada parameter (baru pertama kali buka halaman), default ke 3
        $n = (int) $request->input('n', 3);

        // 1. Ambil data bahan baku & ringkasan
        $ingredients = BahanBaku::all();
        $totalOrders = Order::count();

        $milkStock = BahanBaku::where('nama_bahan', 'like', '%susu%')
            ->orWhere('nama_bahan', 'like', '%milk%')
            ->first();

        $oatStock = BahanBaku::where('nama_bahan', 'like', '%oat%')->first();

        // ====================================================================
        // LOGIKA KALKULASI SINGLE MOVING AVERAGE (SMA DINAMIS) & TABEL AUDIT
        // ====================================================================

        // PERBAIKAN LOGIKA: Tarik data sejauh ($n + 13) hari ke belakang.
        // Ini memastikan kita punya cukup "bahan bakar" data historis untuk menghitung
        // nilai SMA pertama, sehingga UI selalu mendapat 14 hari data prediksi yang utuh.
        $historisDemand = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->select(
                DB::raw('DATE(orders.created_at) as tanggal'),
                DB::raw('SUM(order_items.quantity) as total_qty')
            )
            ->whereBetween('orders.created_at', [
                $endDate->copy()->subDays($n + 13)->startOfDay(),
                $endDate->copy()->endOfDay()
            ])
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->get();

        $analisisSma = [];
        $chartSmaLabels = [];
        $chartSmaAktual = [];
        $chartSmaPrediksi = [];

        foreach ($historisDemand as $index => $data) {
            $prediksi = null;
            $error = null;
            $rumus = '-';

            // Hitung SMA jika data historis ke belakang sudah mencukupi periode (n)
            if ($index >= $n) {
                $totalSebelumnya = 0;
                $deretAngka = [];

                for ($i = 1; $i <= $n; $i++) {
                    $angka = $historisDemand[$index - $i]->total_qty;
                    $totalSebelumnya += $angka;
                    $deretAngka[] = $angka;
                }

                $prediksi = round($totalSebelumnya / $n);
                $error = $data->total_qty - $prediksi;
                $rumus = "(" . implode(" + ", array_reverse($deretAngka)) . ") / " . $n;
            }

            $tanggalFormatted = Carbon::parse($data->tanggal)->isoFormat('D MMM YYYY');

            // Kita hanya memasukkan data ke View jika index sudah melewati batas buang (fase learning algoritma)
            // Tujuannya agar Tabel dan Grafik bersih, tidak menampilkan hari-hari yang error-nya kosong
            if ($index >= $n) {
                // Data untuk Tabel Audit
                $analisisSma[] = (object) [
                    'tanggal'  => $tanggalFormatted,
                    'aktual'   => $data->total_qty,
                    'prediksi' => $prediksi,
                    'rumus'    => $rumus,
                    'error'    => $error,
                ];

                // Data untuk Grafik Kombinasi (Bar vs Line)
                $chartSmaLabels[]   = Carbon::parse($data->tanggal)->isoFormat('D MMM');
                $chartSmaAktual[]   = $data->total_qty;
                $chartSmaPrediksi[] = $prediksi;
            }
        }


        // ====================================================================
        // DATA GRAFIK BANYAK TRANSAKSI: 7 HARI (WEEKLY)
        // ====================================================================
        $salesData = Order::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(id) as total_transactions')
        )
            ->whereBetween('created_at', [
                $endDate->copy()->subDays(6)->startOfDay(),
                $endDate->copy()->endOfDay()
            ])
            ->groupBy('date')
            ->get();

        $chartLabels = [];
        $chartData = [];

        for ($i = 6; $i >= 0; $i--) {
            $tanggal = $endDate->copy()->subDays($i)->format('Y-m-d');
            $labelHari = $endDate->copy()->subDays($i)->format('d M');

            $chartLabels[] = $labelHari;

            $transaksiHariIni = $salesData->firstWhere('date', $tanggal);
            $chartData[] = $transaksiHariIni ? $transaksiHariIni->total_transactions : 0;
        }


        // ====================================================================
        // DATA GRAFIK BANYAK TRANSAKSI: 30 HARI (MONTHLY)
        // ====================================================================
        $monthlySalesData = Order::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(id) as total_transactions')
        )
            ->whereBetween('created_at', [
                $endDate->copy()->subDays(29)->startOfDay(),
                $endDate->copy()->endOfDay()
            ])
            ->groupBy('date')
            ->get();

        $chartLabelsMonthly = [];
        $chartDataMonthly = [];

        for ($i = 29; $i >= 0; $i--) {
            $tanggal = $endDate->copy()->subDays($i)->format('Y-m-d');
            $labelHari = $endDate->copy()->subDays($i)->format('d M');

            $chartLabelsMonthly[] = $labelHari;

            $transaksiHariIni = $monthlySalesData->firstWhere('date', $tanggal);
            $chartDataMonthly[] = $transaksiHariIni ? $transaksiHariIni->total_transactions : 0;
        }

        // Lempar SEMUA variabel ke view
        return view('laporan.index', compact(
            'ingredients',
            'totalOrders',
            'milkStock',
            'oatStock',
            'chartLabels',
            'chartData',
            'chartLabelsMonthly',
            'chartDataMonthly',
            'n',                   // Parameter SMA Aktif
            'analisisSma',
            'chartSmaLabels',
            'chartSmaAktual',
            'chartSmaPrediksi'
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
