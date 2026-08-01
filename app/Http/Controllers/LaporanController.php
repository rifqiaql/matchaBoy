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
                'total_qty' => $rawData[$dateString] ?? 0,
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

        // 7. SIAPKAN GRAFIK 8 HARI
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

    /**
     * Export Laporan Eksekutif: Analisis SMA (Skripsi) + Rekapitulasi Penjualan Kasir
     */
    public function exportCSV(Request $request)
    {
        // 1. Parameter Filter
        $month = $request->input('month');
        $year = $request->input('year');
        $n = (int) $request->input('n', 3);
        if ($n < 1) $n = 3;

        $query = Order::with(['user', 'orderItems.product']);

        if ($month && $year) {
            $query->whereMonth('created_at', $month)->whereYear('created_at', $year);
            $namaBulan = Carbon::createFromDate($year, $month, 1)->locale('id')->translatedFormat('F Y');
            $filterEnd = Carbon::createFromDate($year, $month, 1)->endOfMonth();
        } else {
            $namaBulan = 'SEMUA PERIODE TRANSAKSI';
            $filterEnd = Carbon::now();
        }

        $tanggalCetak = Carbon::now('Asia/Jakarta')->translatedFormat('d F Y - H:i') . ' WIB';
        $filename = "Laporan_Eksekutif_MatchaBoy_" . date('Ymd_His') . ".xls";

        // 2. HITUNG LOGIKA SMA & ERROR (MAE & RMSE) KHUSUS UNTUK EXPORT
        $startDateSma = $filterEnd->copy()->subDays($n + 13)->startOfDay();
        $rawData = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->select(
                DB::raw('DATE(orders.created_at) as tanggal'),
                DB::raw('SUM(order_items.quantity) as total_qty')
            )
            ->whereBetween('orders.created_at', [
                $startDateSma,
                $filterEnd->copy()->endOfDay()
            ])
            ->groupBy('tanggal')
            ->pluck('total_qty', 'tanggal')
            ->toArray();

        $historisDemand = [];
        $currentIterDate = $startDateSma->copy();
        while ($currentIterDate->lessThanOrEqualTo($filterEnd->copy()->startOfDay())) {
            $dateString = $currentIterDate->format('Y-m-d');
            $historisDemand[] = (object) [
                'tanggal'   => $dateString,
                'total_qty' => $rawData[$dateString] ?? 0,
            ];
            $currentIterDate->addDay();
        }

        $analisisSma = [];
        $sumAbsoluteError = 0;
        $sumSquaredError = 0;
        $validSmaCount = 0;

        foreach ($historisDemand as $index => $data) {
            if ($index >= $n) {
                $totalSebelumnya = 0;
                for ($i = 1; $i <= $n; $i++) {
                    $totalSebelumnya += $historisDemand[$index - $i]->total_qty;
                }
                $prediksi = round($totalSebelumnya / $n);
                $error = $data->total_qty - $prediksi;

                $sumAbsoluteError += abs($error);
                $sumSquaredError += ($error * $error);
                $validSmaCount++;

                $analisisSma[] = (object) [
                    'tanggal'  => Carbon::parse($data->tanggal)->isoFormat('D MMM YYYY'),
                    'aktual'   => $data->total_qty,
                    'prediksi' => $prediksi,
                    'error'    => $error,
                ];
            }
        }

        $mae  = $validSmaCount > 0 ? round($sumAbsoluteError / $validSmaCount, 2) : 0;
        $rmse = $validSmaCount > 0 ? round(sqrt($sumSquaredError / $validSmaCount), 2) : 0;

        // Prediksi Besok (H+1)
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

        if ($prediksiBesok > $aktualTerakhir) {
            $trendStatus = 'LONJAKAN (UPTREND)';
            $trendAdvice = 'Siapkan stok ekstra untuk mengantisipasi potensi kekurangan bahan baku.';
        } elseif ($prediksiBesok < $aktualTerakhir) {
            $trendStatus = 'PENURUNAN (DOWNTREND)';
            $trendAdvice = 'Tahan restock berlebih untuk meminimalisir risiko bahan terbuang (waste).';
        } else {
            $trendStatus = 'STABIL (NORMAL)';
            $trendAdvice = 'Pertahankan ritme operasional normal.';
        }

        // 3. GENERATE EXCEL HTML TABLE
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        $orders = $query->latest()->get();

        echo '
        <html xmlns:o="urn:schemas-microsoft-com:office:office"
              xmlns:x="urn:schemas-microsoft-com:office:excel"
              xmlns="http://www.w3.org/TR/REC-html40">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
            <!--[if gte mso 9]>
            <xml>
                <x:ExcelWorkbook>
                    <x:ExcelWorksheets>
                        <x:ExcelWorksheet>
                            <x:Name>Laporan Eksekutif</x:Name>
                            <x:WorksheetOptions>
                                <x:DisplayGridlines/>
                            </x:WorksheetOptions>
                        </x:ExcelWorksheet>
                    </x:ExcelWorksheets>
                </x:ExcelWorkbook>
            </xml>
            <![endif]-->
            <style>
                table { border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; }
                .title { font-size: 16pt; font-weight: bold; color: #2D5A34; text-align: left; }
                .subtitle { font-size: 11pt; color: #555555; font-style: italic; text-align: left; }
                .section-header { font-size: 13pt; font-weight: bold; color: #1f3d24; background-color: #e6f7ec; padding: 10px; }
                th { background-color: #2D5A34; color: #FFFFFF; font-weight: bold; border: 1px solid #1f3d24; padding: 8px; text-align: center; vertical-align: middle; }
                td { border: 1px solid #dcdcdc; padding: 6px 8px; vertical-align: middle; }
                .text-center { text-align: center; }
                .text-right { text-align: right; }
                .text-left { text-align: left; }
                .num-fmt { mso-number-format: "\#\,\#\#0"; }
                .force-text { mso-number-format: "\@"; }
            </style>
        </head>
        <body>
            <table>
                <tr><td colspan="8" class="title">LAPORAN EKSEKUTIF OPERASIONAL & PREDIKSI - MATCHA BOY</td></tr>
                <tr><td colspan="8" class="subtitle">Periode: ' . strtoupper($namaBulan) . ' | Waktu Cetak: ' . $tanggalCetak . '</td></tr>
                <tr><td colspan="8"></td></tr>

                <!-- BAGIAN 1: ANALISIS KAJIAN ILMIAH SMA & PREDIKSI (NILAI TA) -->
                <tr><td colspan="8" class="section-header">A. RINGKASAN PREDIKSI & EVALUASI AKURASI SMA (N = ' . $n . ' HARI)</td></tr>
                <tr style="background-color: #f9f9f9; font-weight: bold;">
                    <td colspan="2">Prediksi Demand Besok (H+1):</td>
                    <td colspan="2" class="text-center" style="color: #2D5A34; font-size: 12pt;">' . $prediksiBesok . ' Cup</td>
                    <td colspan="2">Status Tren Permintaan:</td>
                    <td colspan="2" class="text-center">' . $trendStatus . '</td>
                </tr>
                <tr style="background-color: #f9f9f9;">
                    <td colspan="2" style="font-weight: bold;">Rekomendasi Sistem (Insight):</td>
                    <td colspan="6">' . $trendAdvice . '</td>
                </tr>
                <tr style="background-color: #f2f2f2; font-weight: bold;">
                    <td colspan="2">Evaluasi Error MAE (Mean Absolute Error):</td>
                    <td colspan="2" class="text-center">' . $mae . ' Cup</td>
                    <td colspan="2">Evaluasi Error RMSE:</td>
                    <td colspan="2" class="text-center">' . $rmse . ' Cup</td>
                </tr>
                <tr><td colspan="8"></td></tr>

                <!-- TABEL DERET HISTORIS SMA TERAKHIR -->
                <thead>
                    <tr>
                        <th colspan="2">Tanggal Operasional</th>
                        <th colspan="2">Aktual Terjual (Cup)</th>
                        <th colspan="2">Prediksi SMA (Cup)</th>
                        <th colspan="2">Selisih Error (Cup)</th>
                    </tr>
                </thead>
                <tbody>';

        // Tampilkan 7 hari terakhir analisis SMA di excel agar tidak terlalu panjang
        $recentSma = array_slice($analisisSma, -7);
        foreach ($recentSma as $row) {
            echo '
                    <tr>
                        <td colspan="2" class="text-center">' . $row->tanggal . '</td>
                        <td colspan="2" class="text-center"><b>' . $row->aktual . '</b></td>
                        <td colspan="2" class="text-center">' . $row->prediksi . '</td>
                        <td colspan="2" class="text-center" style="color: ' . ($row->error != 0 ? '#c00000' : '#2D5A34') . ';">' . $row->error . '</td>
                    </tr>';
        }

        echo '
                </tbody>
                <tr><td colspan="8"></td></tr>
                <tr><td colspan="8"></td></tr>

                <!-- BAGIAN 2: DATA PENJUALAN KASIR (POS) -->
                <tr><td colspan="8" class="section-header">B. REKAPITULASI DETAIL TRANSAKSI KASIR (POS)</td></tr>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No. Invoice</th>
                        <th>Tanggal Transaksi</th>
                        <th>Kasir</th>
                        <th>Detail Pesanan</th>
                        <th>Total Volume (Cup)</th>
                        <th>Total Bayar (Rp)</th>
                        <th>Metode Pembayaran</th>
                    </tr>
                </thead>
                <tbody>';

        $no = 1;
        $grandTotalQty = 0;
        $grandTotalOmzet = 0;

        foreach ($orders as $order) {
            $totalHargaMurniOrder = 0;
            $detailPesanan = $order->orderItems->map(function ($item) use (&$totalHargaMurniOrder) {
                $namaProduk = $item->product ? $item->product->nama_produk ?? $item->product->name : 'Produk Dihapus';
                $hargaSatuan = $item->product ? $item->product->price : $item->price;
                $totalHargaMurniOrder += ($hargaSatuan * $item->quantity);
                return $item->quantity . 'x ' . $namaProduk;
            })->implode(', ');

            $totalQty = $order->orderItems->sum('quantity');
            $totalBayarTampil = $totalHargaMurniOrder > 0 ? $totalHargaMurniOrder : $order->subtotal;

            $grandTotalQty += $totalQty;
            $grandTotalOmzet += $totalBayarTampil;

            echo '
                    <tr>
                        <td class="text-center">' . $no++ . '</td>
                        <td class="text-center"><b>' . $order->invoice_number . '</b></td>
                        <td class="text-center">' . $order->created_at->format('Y-m-d H:i:s') . '</td>
                        <td class="text-left">' . ($order->user->name ?? 'Kasir') . '</td>
                        <td class="text-left">' . $detailPesanan . '</td>
                        <td class="text-right num-fmt">' . (int)$totalQty . '</td>
                        <td class="text-right force-text"><b>Rp ' . number_format($totalBayarTampil, 0, ',', '.') . '</b></td>
                        <td class="text-center">' . strtoupper($order->payment_method ?? 'CASH') . '</td>
                    </tr>';
        }

        echo '
                    <tr style="background-color: #f2f2f2; font-weight: bold;">
                        <td colspan="5" class="text-right" style="padding: 10px;">TOTAL TERJUAL:</td>
                        <td class="text-right num-fmt">' . $grandTotalQty . ' Cup</td>
                        <td class="text-right force-text" style="color: #2d5a34;">Rp ' . number_format($grandTotalOmzet, 0, ',', '.') . '</td>
                        <td class="text-center">COMPLETED</td>
                    </tr>
                </tbody>
            </table>
        </body>
        </html>';

        exit;
    }
}