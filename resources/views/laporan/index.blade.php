@extends('layouts.app')

@section('content')
    <div class="w-full p-8 space-y-6 box-border">

        <!-- HEADER & ACTIONS -->
        <div class="flex justify-between items-center border-b border-gray-200 pb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Laporan Analitis & Analisis Stok</h1>
                <p class="text-sm text-gray-500">Wawasan prediksi permintaan dan manajemen persediaan bahan baku</p>
            </div>
            <div class="flex flex-col items-end gap-3">
                <div class="flex items-center gap-3">

                    <!-- FORM 1: FILTER TAMPILAN GRAFIK (SMA MINGGUAN) -->
                    <form action="{{ route('laporan.index') }}" method="GET" id="filterForm"
                        class="m-0 p-0 flex items-center gap-2">

                        <!-- REVISI PENGUJI: Input diubah ke type="week" untuk memilih periode minggu -->
                        <input type="week" name="end_date"
                            value="{{ request('end_date', \Carbon\Carbon::now()->format('Y-\WW')) }}"
                            class="bg-white border border-gray-200 text-gray-700 text-sm rounded-lg focus:ring-[#2E4F4F] focus:border-[#2E4F4F] block px-3 py-1.5 cursor-pointer shadow-sm hover:border-gray-300 transition-colors"
                            onchange="this.form.submit();" max="{{ \Carbon\Carbon::now()->format('Y-\WW') }}">

                        <!-- REVISI: Opsi dropdown disesuaikan menjadi 2, 3, dan 4 Minggu -->
                        <select name="n" onchange="this.form.submit();"
                            class="bg-white border border-gray-200 text-gray-700 text-sm rounded-lg focus:ring-[#2E4F4F] focus:border-[#2E4F4F] block px-3 py-1.5 cursor-pointer shadow-sm hover:border-gray-300 transition-colors">
                            <option value="2" {{ request('n', 3) == 2 ? 'selected' : '' }}>SMA: n = 2 Minggu (Reaktif)
                            </option>
                            <option value="3" {{ request('n', 3) == 3 ? 'selected' : '' }}>SMA: n = 3 Minggu (Moderat)
                            </option>
                            <option value="4" {{ request('n', 3) == 4 ? 'selected' : '' }}>SMA: n = 4 Minggu (Stabil /
                                1 Bulan)</option>
                        </select>
                    </form>

                    <!-- PEMBATAS VISUAL -->
                    <div class="h-8 border-l border-gray-300 mx-1"></div>

                    <!-- FORM 2: EXPORT EXCEL DINAMIS -->
                    <form action="{{ route('laporan.export') }}" method="GET" class="m-0 p-0 flex items-center gap-2">
                        <input type="hidden" name="n" value="{{ $n }}">

                        <!-- Dropdown Bulan -->
                        <select name="month"
                            class="bg-white border border-gray-200 text-gray-700 text-sm rounded-lg focus:ring-[#2E4F4F] focus:border-[#2E4F4F] block px-3 py-1.5 cursor-pointer shadow-sm hover:border-gray-300 transition-colors">
                            <option value="01" {{ date('m') == '01' ? 'selected' : '' }}>Januari</option>
                            <option value="02" {{ date('m') == '02' ? 'selected' : '' }}>Februari</option>
                            <option value="03" {{ date('m') == '03' ? 'selected' : '' }}>Maret</option>
                            <option value="04" {{ date('m') == '04' ? 'selected' : '' }}>April</option>
                            <option value="05" {{ date('m') == '05' ? 'selected' : '' }}>Mei</option>
                            <option value="06" {{ date('m') == '06' ? 'selected' : '' }}>Juni</option>
                            <option value="07" {{ date('m') == '07' ? 'selected' : '' }}>Juli</option>
                            <option value="08" {{ date('m') == '08' ? 'selected' : '' }}>Agustus</option>
                            <option value="09" {{ date('m') == '09' ? 'selected' : '' }}>September</option>
                            <option value="10" {{ date('m') == '10' ? 'selected' : '' }}>Oktober</option>
                            <option value="11" {{ date('m') == '11' ? 'selected' : '' }}>November</option>
                            <option value="12" {{ date('m') == '12' ? 'selected' : '' }}>Desember</option>
                        </select>

                        <!-- Dropdown Tahun -->
                        <select name="year"
                            class="bg-white border border-gray-200 text-gray-700 text-sm rounded-lg focus:ring-[#2E4F4F] focus:border-[#2E4F4F] block px-3 py-1.5 cursor-pointer shadow-sm hover:border-gray-300 transition-colors">
                            @php $currentYear = date('Y'); @endphp
                            @for ($y = $currentYear; $y >= $currentYear - 2; $y--)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>

                        <button type="submit"
                            class="bg-[#2E4F4F] text-white px-4 py-1.5 rounded-lg shadow-sm text-sm font-semibold hover:bg-opacity-90 transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            Export
                        </button>
                    </form>

                </div>
            </div>
        </div>

        <!-- MAIN ANALYTICS BLOCK -->
        <div class="grid grid-cols-3 gap-6 items-start w-full">

            <!-- LEFT COLUMN: GRAPH & INSIGHTS -->
            <div class="col-span-2 space-y-6">
                <!-- Demand Analysis -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="flex justify-between items-center mb-1">
                        <h3 class="text-lg font-bold text-gray-800">Grafik Perbandingan Volume Penjualan Aktual vs. Prediksi
                        </h3>
                    </div>
                    <p class="text-sm text-gray-400 mb-6">Berikut adalah grafik perbandingan antara volume penjualan aktual
                        dan hasil prediksi peramalan permintaan (SMA).</p>

                    <div class="w-full h-64 mt-4 relative">
                        <canvas id="demandChart"></canvas>
                    </div>
                </div>

                <!-- AI Summary Prediction Box -->
                <div class="bg-dark-matcha text-white p-6 rounded-2xl shadow-sm flex items-start space-x-4 w-full"
                    style="background-color: #2D5A34;">
                    <div class="p-2.5 bg-white/20 rounded-xl flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-md font-bold">System Actionable Insight</h4>
                        <p class="text-sm text-gray-200 mt-1">
                            Algoritma mendeteksi potensi <span
                                class="font-bold {{ $trendColor }}">{{ $trendStatus }}</span> permintaan.
                            Estimasi target produksi minggu depan berada di angka <span
                                class="font-bold text-white underline text-lg">{{ $prediksiBesok }} Porsi</span>.
                            {{ $trendAdvice }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: PERFORMANCE METRICS & WASTES -->
            <div class="col-span-1 space-y-6">
                @php
                    $kritisCount = $ingredients
                        ->filter(function ($item) {
                            return $item->stok_saat_ini <= $item->stok_minimum;
                        })
                        ->count();

                    $priorityItems = $ingredients
                        ->map(function ($item) {
                            $item->persentase =
                                $item->stok_awal > 0 ? round(($item->stok_saat_ini / $item->stok_awal) * 100) : 0;
                            $item->is_kritis = $item->stok_saat_ini <= $item->stok_minimum;
                            return $item;
                        })
                        ->sortBy('persentase')
                        ->take(3);
                @endphp

                <!-- Performance Metrics -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Real-Time Metrics</h3>
                    <div class="grid grid-cols-2 gap-2 text-center">
                        <div class="p-3 border rounded-xl flex flex-col items-center">
                            <div
                                class="w-12 h-12 rounded-full border-4 border-[#86A789] flex items-center justify-center font-bold text-sm text-gray-800">
                                {{ $ingredients->count() }}
                            </div>
                            <span class="text-[11px] text-gray-500 mt-2">Total Item Aktif</span>
                        </div>
                        <div class="p-3 border rounded-xl flex flex-col items-center">
                            <div
                                class="w-12 h-12 rounded-full border-4 {{ $kritisCount > 0 ? 'border-red-500 text-red-600' : 'border-[#86A789] text-gray-800' }} flex items-center justify-center font-bold text-sm">
                                {{ $kritisCount }}
                            </div>
                            <span
                                class="text-[11px] {{ $kritisCount > 0 ? 'text-red-500 font-bold' : 'text-gray-500' }} mt-2">Status
                                Kritis</span>
                        </div>
                    </div>
                </div>

                <!-- Restock Priority -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                    <h3 class="text-md font-bold text-gray-800 mb-1">Restock Priority</h3>
                    <p class="text-[10px] text-gray-400 mb-4">Item dengan sisa kapasitas terendah</p>
                    <div class="space-y-3">
                        @forelse ($priorityItems as $prod)
                            <div>
                                <div class="flex justify-between text-xs font-medium mb-1">
                                    <span class="text-gray-700">{{ $prod->nama_bahan }}</span>
                                    <span
                                        class="{{ $prod->is_kritis ? 'text-red-600 font-bold' : 'text-gray-600' }}">{{ $prod->persentase }}%</span>
                                </div>
                                <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                                    <div class="{{ $prod->is_kritis ? 'bg-red-500' : 'bg-[#2E4F4F]' }} h-2 rounded-full transition-all duration-500"
                                        style="width: {{ $prod->persentase }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-center text-gray-400 italic">Data bahan baku kosong.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- BOTTOM BLOCK: RESTOCK PLANNING -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 mt-6 w-full">
            <div class="mb-6">
                <h3 class="text-xl font-bold text-[#2D5A34]">Rencana Produksi & Restock</h3>
                <p class="text-sm text-gray-400 mt-1">Proyeksi penyusutan bahan baku untuk target produksi minggu depan
                    ({{ $prediksiBesok }} Porsi)</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-[10px] uppercase tracking-wider text-gray-400 border-b border-gray-100">
                            <th class="pb-3 font-bold">Nama Bahan Baku</th>
                            <th class="pb-3 font-bold">Sisa Stok Gudang</th>
                            <th class="pb-3 font-bold">Kebutuhan (Per Porsi)</th>
                            <th class="pb-3 font-bold text-red-600">Estimasi Penyusutan Minggu Depan</th>
                            <th class="pb-3 font-bold">Sisa Akhir (Proyeksi)</th>
                            <th class="pb-3 font-bold text-center">Rekomendasi Sistem</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm align-top">
                        @forelse($ingredients as $ing)
                            @php
                                $jumlahPenyusutan = isset($estimasiPenyusutan[$ing->id])
                                    ? $estimasiPenyusutan[$ing->id]
                                    : 0;
                                $proyeksiSisa = $ing->stok_saat_ini - $jumlahPenyusutan;
                                $batasKritis = $ing->stok_minimum;
                                $butuhRestock = $proyeksiSisa <= $batasKritis;
                            @endphp
                            <tr class="border-b border-gray-50">
                                <td class="py-5 font-bold text-[#2D5A34]">{{ $ing->nama_bahan }}</td>
                                <td class="py-5 font-bold text-gray-800">{{ $ing->stok_saat_ini }} <span
                                        class="text-xs font-normal text-gray-500">{{ $ing->satuan }}</span></td>
                                <td class="py-5 text-gray-500 font-mono text-xs">
                                    Dinamis (Sesuai Resep)
                                </td>
                                <td class="py-5 font-bold text-red-600">
                                    - {{ $jumlahPenyusutan }} <span
                                        class="text-xs font-normal">{{ $ing->satuan }}</span>
                                </td>
                                <td class="py-5 font-bold {{ $proyeksiSisa < 0 ? 'text-red-600' : 'text-gray-800' }}">
                                    {{ $proyeksiSisa }} <span
                                        class="text-xs font-normal text-gray-500">{{ $ing->satuan }}</span>
                                </td>
                                <td class="py-5 text-center">
                                    @if ($proyeksiSisa < 0)
                                        <span
                                            class="bg-red-600 text-white px-3 py-1.5 rounded-full text-[10px] font-bold tracking-wider uppercase shadow-sm">KRITIS
                                            - DEFISIT</span>
                                    @elseif ($butuhRestock)
                                        <span
                                            class="bg-red-50 text-red-600 border border-red-200 px-3 py-1.5 rounded-full text-[10px] font-bold tracking-wider uppercase">Segera
                                            Restock</span>
                                    @else
                                        <span
                                            class="bg-green-50 text-green-600 border border-green-200 px-3 py-1.5 rounded-full text-[10px] font-bold tracking-wider uppercase">Stok
                                            Aman</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-5 text-center text-gray-400 italic">Data bahan baku gudang
                                    kosong.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- EVALUASI AKURASI MODEL PREDIKSI (MAPE) -->
        @php
            $totalMape = 0;
            $dataValidMapeCount = 0;

            foreach ($analisisSma as $row) {
                // Hitung APE hanya untuk baris yang memiliki nilai aktual > 0 dan prediksi sudah terbentuk
                if ($row->prediksi !== null && $row->aktual > 0 && $row->error !== null) {
                    $ape = (abs($row->error) / $row->aktual) * 100;
                    $totalMape += $ape;
                    $dataValidMapeCount++;
                }
            }

            $nilaiMape = $dataValidMapeCount > 0 ? round($totalMape / $dataValidMapeCount, 2) : 0;

            // Kriteria Standar Akademis MAPE (Lewis, 1982)
            if ($dataValidMapeCount === 0) {
                $statusMape = 'Belum Ada Evaluasi';
                $badgeMapeColor = 'bg-gray-100 text-gray-700 border-gray-300';
                $penjelasanMape = 'Sistem belum memiliki cukup data historis untuk mengukur tingkat error peramalan.';
            } elseif ($nilaiMape < 10) {
                $statusMape = 'Sangat Akurat (High Accuracy)';
                $badgeMapeColor = 'bg-emerald-50 text-emerald-700 border-emerald-300';
                $penjelasanMape =
                    'Model peramalan ini memiliki tingkat error di bawah 10%, sehingga sangat layak dijadikan acuan utama pengadaan stok mingguan.';
            } elseif ($nilaiMape <= 20) {
                $statusMape = 'Akurat (Good Forecasting)';
                $badgeMapeColor = 'bg-blue-50 text-blue-700 border-blue-300';
                $penjelasanMape =
                    'Akurasi model berada dalam batas wajar industri F&B. Sistem mampu mengantisipasi fluktuasi permintaan standar dengan baik.';
            } elseif ($nilaiMape <= 50) {
                $statusMape = 'Cukup Akurat (Fair Forecasting)';
                $badgeMapeColor = 'bg-amber-50 text-amber-700 border-amber-300';
                $penjelasanMape =
                    'Tingkat penyimpangan berada dalam level moderat. Disarankan untuk membandingkan opsi parameter n lain untuk hasil lebih optimal.';
            } else {
                $statusMape = 'Kurang Akurat (Inaccurate)';
                $badgeMapeColor = 'bg-rose-50 text-rose-700 border-rose-300';
                $penjelasanMape =
                    'Tingkat error melebihi 50% akibat lonjakan permintaan ekstrem atau minimnya data historis. Gunakan prediksi sebagai referensi sekunder.';
            }
        @endphp

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 mt-6 w-full">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                <div>
                    <h3 class="text-xl font-bold text-[#2D5A34]">Evaluasi Model Prediksi (MAPE)</h3>
                    <p class="text-sm text-gray-400 mt-1">Pengujian ilmiah tingkat penyimpangan error peramalan SMA (n =
                        {{ $n }} Minggu)</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs font-semibold text-gray-500">Tingkat Kesalahan (MAPE):</span>
                    <span class="px-3 py-1.5 rounded-lg text-sm font-bold border {{ $badgeMapeColor }}">
                        {{ $nilaiMape }}% &mdash; {{ $statusMape }}
                    </span>
                </div>
            </div>

            <div class="p-4 bg-gray-50 rounded-xl border border-gray-100 flex items-start gap-3">
                <svg class="w-5 h-5 text-[#2D5A34] shrink-0 mt-0.5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="text-xs text-gray-600 leading-relaxed">
                    <span class="font-bold text-gray-800">Catatan Evaluasi Sistem:</span>
                    {{ $penjelasanMape }}
                    Perhitungan didasarkan pada <span class="font-bold text-gray-800">{{ $dataValidMapeCount }} minggu
                        operasional</span> yang telah memiliki nilai aktual dan prediksi (menggunakan standar evaluasi
                    <em>Mean Absolute Percentage Error</em>).
                </div>
            </div>
        </div>

        <!-- TABEL AUDIT PEMBUKTIAN SMA -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 mt-6 w-full">
            <div class="mb-6 flex justify-between items-center">
                <div>
                    <h3 class="text-xl font-bold text-[#2D5A34]">Tabel Pembuktian Algoritma SMA</h3>
                    <p class="text-sm text-gray-400 mt-1">Langkah matematis kalkulasi prediksi demand (n =
                        {{ $n }} Minggu)</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="text-[10px] uppercase tracking-wider text-gray-400 border-b border-gray-100 bg-gray-50/50">
                            <th class="py-3 px-4 font-bold">Periode (Minggu)</th>
                            <th class="py-3 px-4 font-bold text-center">Aktual Terjual (Porsi)</th>
                            <th class="py-3 px-4 font-bold text-center">Langkah Perhitungan (Rumus)</th>
                            <th class="py-3 px-4 font-bold text-center">Hasil Prediksi (SMA)</th>
                            <th class="py-3 px-4 font-bold text-right">Error (Selisih)</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-50">
                        @forelse($analisisSma as $row)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="py-4 px-4 font-semibold text-gray-700">{{ $row->tanggal }}</td>
                                <td class="py-4 px-4 text-center font-bold text-gray-800">{{ $row->aktual }}</td>
                                <td class="py-4 px-4 text-center font-mono text-xs text-gray-500">{{ $row->rumus }}</td>
                                <td class="py-4 px-4 text-center font-bold text-[#2D5A34]">
                                    @if ($row->prediksi !== null)
                                        {{ $row->prediksi }}
                                    @else
                                        <span class="text-gray-300 italic text-xs font-normal">Membutuhkan
                                            {{ $n }} minggu historis</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-right">
                                    @if ($row->error !== null)
                                        @php
                                            if ($row->error > 0) {
                                                $badgeErrorClass =
                                                    'bg-emerald-50 text-emerald-700 border border-emerald-200';
                                            } elseif ($row->error < 0) {
                                                $badgeErrorClass = 'bg-rose-50 text-rose-600 border border-rose-200';
                                            } else {
                                                $badgeErrorClass = 'bg-gray-100 text-gray-700 border border-gray-300';
                                            }
                                        @endphp
                                        <span class="px-2.5 py-1 rounded-md text-xs font-bold {{ $badgeErrorClass }}">
                                            {{ $row->error > 0 ? '+' : '' }}{{ $row->error }}
                                        </span>
                                    @else
                                        <span class="text-gray-300">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-gray-400 italic">Data transaksi belum
                                    mencukupi untuk audit SMA.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- SCRIPT CHART.JS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('demandChart').getContext('2d');
            window.demandChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($chartSmaLabels),
                    datasets: [{
                            type: 'line',
                            label: 'Forecast',
                            data: @json($chartSmaPrediksi),
                            borderColor: '#E53E3E',
                            borderDash: [5, 5],
                            backgroundColor: 'transparent',
                            tension: 0.4,
                            pointRadius: 4,
                            pointBackgroundColor: '#E53E3E'
                        },
                        {
                            type: 'bar',
                            label: 'Actual',
                            data: @json($chartSmaAktual),
                            backgroundColor: '#2D5A34',
                            borderRadius: 6,
                            borderSkipped: false
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            align: 'end'
                        }
                    },
                    scales: {
                        y: {
                            title: {
                                display: true,
                                text: 'Volume Produk Terjual (Porsi)',
                                font: {
                                    weight: 'bold'
                                }
                            },
                            beginAtZero: true,
                            grid: {
                                display: false
                            },
                            ticks: {
                                stepSize: 1
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Timeline (Periode Minggu)',
                                font: {
                                    weight: 'bold'
                                }
                            },
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
