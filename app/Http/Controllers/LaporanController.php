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
    public function index(Request $request): View
    {
        // 1. TANGKAP PARAMETER DARI URL
        $endDate = $request->has('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now();
        $n = (int) $request->input('n', 3);
        if ($n < 1) {
            $n = 3; // Fallback aman untuk mencegah division by zero
        }

        // 2. DATA MASTER
        $ingredients = BahanBaku::all();
        $totalOrders = Order::count();
        $milkStock = BahanBaku::where('nama_bahan', 'like', '%susu%')->orWhere('nama_bahan', 'like', '%milk%')->first();
        $oatStock = BahanBaku::where('nama_bahan', 'like', '%oat%')->first();

        // 3. LOGIKA SMA UNTUK TABEL AUDIT (14 HARI HISTORIS)
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
        foreach ($historisDemand as $index => $data) {
            $prediksi = null;
            $error = null;
            $rumus = '-';

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

            if ($index >= $n) {
                $analisisSma[] = (object) [
                    'tanggal'  => Carbon::parse($data->tanggal)->isoFormat('D MMM YYYY'),
                    'aktual'   => $data->total_qty,
                    'prediksi' => $prediksi,
                    'rumus'    => $rumus,
                    'error'    => $error,
                ];
            }
        }

        // 4. HITUNG PREDIKSI UNTUK H+1 (BESOK DARI TANGGAL FILTER)
        $totalBesok = 0;
        $totalData = count($historisDemand);

        if ($totalData >= $n) {
            for ($i = 1; $i <= $n; $i++) {
                $totalBesok += $historisDemand[$totalData - $i]->total_qty;
            }
            $prediksiBesok = round($totalBesok / $n);
        } else {
            $prediksiBesok = 0;
        }

        $aktualTerakhir = $totalData > 0 ? $historisDemand[$totalData - 1]->total_qty : 0;

        // 5. TENTUKAN TREN UNTUK KOTAK AI SUMMARY
        if ($prediksiBesok > $aktualTerakhir) {
            $trendStatus = 'Lonjakan';
            $trendColor = 'text-yellow-400';
            $trendAdvice = 'Siapkan stok ekstra untuk mengantisipasi potensi kekurangan bahan baku.';
        } elseif ($prediksiBesok < $aktualTerakhir) {
            $trendStatus = 'Penurunan';
            $trendColor = 'text-blue-300';
            $trendAdvice = 'Tahan restock berlebih untuk meminimalisir risiko bahan terbuang (waste).';
        } else {
            $trendStatus = 'Stabil';
            $trendColor = 'text-green-300';
            $trendAdvice = 'Pertahankan ritme operasional normal.';
        }

        // 6. SIAPKAN GRAFIK 8 HARI (7 Historis + 1 Besok)
        $chartSmaLabels = [];
        $chartSmaAktual = [];
        $chartSmaPrediksi = [];

        $grafik7Hari = array_slice($analisisSma, -7);

        foreach ($grafik7Hari as $row) {
            $chartSmaLabels[] = Carbon::parse($row->tanggal)->isoFormat('D MMM');
            $chartSmaAktual[] = $row->aktual;
            $chartSmaPrediksi[] = $row->prediksi;
        }

        $besokDate = $endDate->copy()->addDay();
        $labelBesok = $besokDate->isoFormat('D MMM');
        $aktualBesok = null;

        if ($besokDate->startOfDay()->lessThanOrEqualTo(Carbon::now()->startOfDay())) {
            $aktualBesokDb = DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->whereDate('orders.created_at', $besokDate->format('Y-m-d'))
                ->sum('order_items.quantity');

            $aktualBesok = (int) $aktualBesokDb;
        } else {
            $labelBesok .= ' (Besok)';
        }

        $chartSmaLabels[] = $labelBesok;
        $chartSmaAktual[] = $aktualBesok;
        $chartSmaPrediksi[] = $prediksiBesok;

        return view('laporan.index', compact(
            'ingredients',
            'totalOrders',
            'milkStock',
            'oatStock',
            'n',
            'analisisSma',
            'chartSmaLabels',
            'chartSmaAktual',
            'chartSmaPrediksi',
            'prediksiBesok',
            'trendStatus',
            'trendColor',
            'trendAdvice'
        ));
    }

    public function exportCSV()
    {
        $filename = "Laporan_Penjualan_MatchaBoy_" . date('Ymd_His') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['No. Invoice', 'Tanggal Transaksi', 'Kasir', 'Subtotal', 'Pajak', 'Total Bayar', 'Status'];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns, ";");

            // Menggunakan cursor() untuk efisiensi memori tingkat tinggi
            $orders = Order::with(['user'])->latest()->cursor();

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
