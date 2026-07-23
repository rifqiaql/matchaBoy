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
            $n = 3;
        }

        // 2. DATA MASTER
        $ingredients = BahanBaku::all();
        $totalOrders = Order::count();

        // 3. LOGIKA SMA DENGAN ZERO-FILLING (MENAMBAL TANGGAL KOSONG)
        $startDate = $endDate->copy()->subDays($n + 13)->startOfDay();

        // Ambil raw data dari DB dan jadikan key-value (tanggal => total)
        $rawData = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->select(
                DB::raw('DATE(orders.created_at) as tanggal'),
                DB::raw('SUM(order_items.quantity) as total_qty')
            )
            ->whereBetween('orders.created_at', [
                $startDate,
                $endDate->copy()->endOfDay()
            ])
            ->groupBy('tanggal')
            ->pluck('total_qty', 'tanggal')
            ->toArray();

        // Generate deret tanggal berurutan tanpa putus
        $historisDemand = [];
        $currentIterDate = $startDate->copy();
        $endIterDate = $endDate->copy()->startOfDay();

        while ($currentIterDate->lessThanOrEqualTo($endIterDate)) {
            $dateString = $currentIterDate->format('Y-m-d');
            $historisDemand[] = (object) [
                'tanggal'   => $dateString,
                'total_qty' => $rawData[$dateString] ?? 0, // Jika nol/kosong, paksa jadi 0
            ];
            $currentIterDate->addDay();
        }

        // 4. KALKULASI SMA
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

        // 5. HITUNG PREDIKSI UNTUK H+1 (BESOK DARI TANGGAL FILTER)
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

        // 6. TENTUKAN TREN UNTUK KOTAK AI SUMMARY
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

        // 7. SIAPKAN GRAFIK 8 HARI (7 Historis + 1 Besok)
        $chartSmaLabels = [];
        $chartSmaAktual = [];
        $chartSmaPrediksi = [];

        $grafik7Hari = array_slice($analisisSma, -7);

        foreach ($grafik7Hari as $row) {
            // Ubah format label agar lebih bersih di chart
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

    public function exportCSV(Request $request)
    {
        // 1. Tangkap Parameter (Default: Bulan dan Tahun saat ini)
        $month = $request->input('month', Carbon::now()->format('m'));
        $year = $request->input('year', Carbon::now()->format('Y'));

        $filename = "Laporan_Penjualan_MatchaBoy_{$year}_{$month}.csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            'No. Invoice',
            'Tanggal Transaksi',
            'Kasir',
            'Detail Pesanan',
            'Total Qty (Cup)',
            'Subtotal',
            'Pajak',
            'Total Bayar',
            'Metode Pembayaran',
            'Status'
        ];

        // Gunakan parameter 'use' untuk melempar variabel ke dalam scope Closure
        $callback = function () use ($columns, $month, $year) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns, ";");

            // 2. Filter data berdasarkan Bulan dan Tahun
            $orders = Order::with(['user', 'orderItems.product'])
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->latest()
                ->cursor();

            foreach ($orders as $order) {
                $detailPesanan = $order->orderItems->map(function ($item) {
                    $namaProduk = $item->product ? $item->product->nama_produk ?? $item->product->name : 'Produk Dihapus';
                    return $item->quantity . 'x ' . $namaProduk;
                })->implode(', ');

                $totalQty = $order->orderItems->sum('quantity');

                fputcsv($file, [
                    $order->invoice_number,
                    $order->created_at->format('Y-m-d H:i:s'),
                    $order->user->name ?? 'Sistem',
                    $detailPesanan,
                    $totalQty,
                    $order->subtotal,
                    $order->tax,
                    $order->total_price,
                    $order->payment_method ?? 'Cash',
                    $order->status
                ], ";");
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
