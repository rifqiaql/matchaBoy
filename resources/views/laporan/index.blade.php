@extends('layouts.app')

@section('content')
    <div class="w-full p-8 space-y-6 box-border">

        <!-- HEADER & ACTIONS -->
        <div class="flex justify-between items-center border-b border-gray-200 pb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Analytics & Stock Report</h1>
                <p class="text-sm text-gray-500">Predictive insights and artisanal inventory management</p>
            </div>
            <div class="flex flex-col items-end gap-3">
                <div class="flex items-center space-x-3">
                    <div class="flex items-center gap-2">

                        <!-- Form Filter Dinamis (Tanggal & Window SMA) -->
                        <form action="{{ route('laporan.index') }}" method="GET" id="filterForm"
                            class="m-0 p-0 flex items-center gap-2">
                            <input type="date" name="end_date"
                                value="{{ request('end_date', \Carbon\Carbon::now()->format('Y-m-d')) }}"
                                class="bg-white border border-gray-200 text-gray-700 text-sm rounded-lg focus:ring-[#2E4F4F] focus:border-[#2E4F4F] block px-3 py-1.5 cursor-pointer shadow-sm hover:border-gray-300 transition-colors"
                                onchange="this.form.submit();" max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">

                            <select name="n" onchange="this.form.submit();"
                                class="bg-white border border-gray-200 text-gray-700 text-sm rounded-lg focus:ring-[#2E4F4F] focus:border-[#2E4F4F] block px-3 py-1.5 cursor-pointer shadow-sm hover:border-gray-300 transition-colors">
                                <option value="3" {{ request('n', 3) == 3 ? 'selected' : '' }}>SMA: n = 3 Hari
                                    (Reaktif)</option>
                                <option value="5" {{ request('n', 3) == 5 ? 'selected' : '' }}>SMA: n = 5 Hari
                                    (Moderat)</option>
                                <option value="7" {{ request('n', 3) == 7 ? 'selected' : '' }}>SMA: n = 7 Hari (Stabil)
                                </option>
                            </select>
                        </form>

                        <a href="{{ route('laporan.export') }}"
                            class="bg-[#2E4F4F] text-white px-4 py-1.5 rounded-lg shadow-sm text-sm font-semibold hover:bg-opacity-90 transition inline-block text-center">
                            Export Excel
                        </a>
                    </div>

                    <div x-data="{ activeTab: 'weekly' }"
                        class="flex items-center bg-gray-50 p-1 rounded-lg border border-gray-200 text-xs font-semibold">
                        <button @click="activeTab = 'monthly'; switchChartMode('monthly')"
                            :class="activeTab === 'monthly' ? 'bg-white text-gray-800 shadow-sm border-gray-100' :
                                'text-gray-500 border-transparent hover:text-gray-700 hover:bg-gray-200/50'"
                            class="px-4 py-1 rounded-md border transition-all duration-300 ease-in-out">
                            Monthly
                        </button>
                        <button @click="activeTab = 'weekly'; switchChartMode('weekly')"
                            :class="activeTab === 'weekly' ? 'bg-white text-gray-800 shadow-sm border-gray-100' :
                                'text-gray-500 border-transparent hover:text-gray-700 hover:bg-gray-200/50'"
                            class="px-4 py-1 rounded-md border transition-all duration-300 ease-in-out">
                            Weekly
                        </button>
                    </div>
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
                        <h3 class="text-lg font-bold text-gray-800">Demand Analysis</h3>
                    </div>
                    <p class="text-sm text-gray-400 mb-6">Actual vs. Forecasted consumption</p>

                    <div class="w-full h-64 mt-4 relative">
                        <canvas id="demandChart"></canvas>
                    </div>
                </div>

                <!-- WIDGET STOCK FLUCTUATION DIHAPUS DARI SINI -->

                <!-- AI Summary Prediction Box (100% DINAMIS DENGAN PERKONDISIAN) -->
                @php
                    $latestSma = collect($analisisSma)->last();
                    $prediksiBesok = $latestSma->prediksi ?? 0;
                    $aktualTerakhir = $latestSma->aktual ?? 0;

                    // Logika Perkondisian Arah Tren
                    if ($prediksiBesok > $aktualTerakhir) {
                        $trendStatus = 'Lonjakan';
                        $trendColor = 'text-yellow-400';
                        $trendAdvice = 'Siapkan stok ekstra untuk mengantisipasi potensi kekurangan bahan baku.';
                    } elseif ($prediksiBesok < $aktualTerakhir) {
                        $trendStatus = 'Penurunan';
                        $trendColor = 'text-blue-300';
                        $trendAdvice = 'Tahan restock berlebih untuk meminimalisir risiko bahan baku terbuang (waste).';
                    } else {
                        $trendStatus = 'Stabil';
                        $trendColor = 'text-green-300';
                        $trendAdvice = 'Pertahankan ritme operasional normal.';
                    }
                @endphp
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
                            Estimasi target produksi esok hari berada di angka <span
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
                            return ($item->stok_awal > 0 ? ($item->stok_saat_ini / $item->stok_awal) * 100 : 0) <= 20;
                        })
                        ->count();

                    $priorityItems = $ingredients
                        ->map(function ($item) {
                            $item->persentase =
                                $item->stok_awal > 0 ? round(($item->stok_saat_ini / $item->stok_awal) * 100) : 0;
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

                <!-- Perishable Focus -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-md font-bold text-gray-800">Perishable Focus</h3>
                    </div>
                    <div class="space-y-3">
                        @if ($milkStock)
                            <div class="p-3 bg-red-50 rounded-xl border border-red-100 flex justify-between items-center">
                                <div>
                                    <h4 class="text-xs font-bold text-gray-800">{{ $milkStock->nama_bahan }}</h4>
                                    <p class="text-[10px] text-red-500">Prioritas Pemakaian</p>
                                </div>
                                <span class="font-bold text-xs text-gray-700">{{ $milkStock->stok_saat_ini }}
                                    {{ $milkStock->satuan }}</span>
                            </div>
                        @endif

                        @if ($oatStock)
                            <div class="p-3 bg-gray-50 rounded-xl border border-gray-200 flex justify-between items-center">
                                <div>
                                    <h4 class="text-xs font-bold text-gray-800">{{ $oatStock->nama_bahan }}</h4>
                                    <p class="text-[10px] text-gray-400">Pantau Stok</p>
                                </div>
                                <span class="font-bold text-xs text-gray-700">{{ $oatStock->stok_saat_ini }}
                                    {{ $oatStock->satuan }}</span>
                            </div>
                        @endif

                        @if (!$milkStock && !$oatStock)
                            <div class="p-3 bg-gray-50 rounded-xl flex justify-center items-center">
                                <span class="text-xs text-gray-400 italic">Belum ada data susu/oat terdaftar.</span>
                            </div>
                        @endif
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
                                        class="{{ $prod->persentase <= 20 ? 'text-red-600 font-bold' : 'text-gray-600' }}">{{ $prod->persentase }}%</span>
                                </div>
                                <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                                    <div class="{{ $prod->persentase <= 20 ? 'bg-red-500' : 'bg-[#2E4F4F]' }} h-2 rounded-full transition-all duration-500"
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

        <!-- BOTTOM BLOCK: RESTOCK PLANNING (100% DINAMIS DARI SMA) -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 mt-6 w-full">
            <div class="mb-6">
                <h3 class="text-xl font-bold text-[#2D5A34]">Rencana Produksi & Restock</h3>
                <p class="text-sm text-gray-400 mt-1">Proyeksi penyusutan bahan baku untuk target produksi besok
                    ({{ $prediksiBesok }} Porsi)</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-[10px] uppercase tracking-wider text-gray-400 border-b border-gray-100">
                            <th class="pb-3 font-bold">Nama Bahan Baku</th>
                            <th class="pb-3 font-bold">Sisa Stok Gudang</th>
                            <th class="pb-3 font-bold">Kebutuhan (Per Porsi)</th>
                            <th class="pb-3 font-bold text-red-600">Estimasi Penyusutan Besok</th>
                            <th class="pb-3 font-bold">Sisa Akhir (Proyeksi)</th>
                            <th class="pb-3 font-bold text-center">Rekomendasi Sistem</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm align-top">
                        @forelse($ingredients as $ing)
                            @php
                                // Asumsi sementara sebelum wawancara BOM
                                $takaranResep = str_contains(strtolower($ing->nama_bahan), 'susu') ? 150 : 20;
                                $estimasiPenyusutan = $prediksiBesok * $takaranResep;
                                $proyeksiSisa = $ing->stok_saat_ini - $estimasiPenyusutan;
                                $batasKritis = $ing->stok_awal * 0.2;
                                $butuhRestock = $proyeksiSisa <= $batasKritis;
                            @endphp
                            <tr class="border-b border-gray-50">
                                <td class="py-5 font-bold text-[#2D5A34]">{{ $ing->nama_bahan }}</td>
                                <td class="py-5 font-bold text-gray-800">{{ $ing->stok_saat_ini }} <span
                                        class="text-xs font-normal text-gray-500">{{ $ing->satuan }}</span></td>
                                <td class="py-5 text-gray-500 font-mono text-xs">{{ $takaranResep }} {{ $ing->satuan }}
                                </td>
                                <td class="py-5 font-bold text-red-600">
                                    - {{ $estimasiPenyusutan }} <span
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

        <!-- NEW BLOCK: TABEL AUDIT PEMBUKTIAN SMA -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 mt-6 w-full">
            <div class="mb-6 flex justify-between items-center">
                <div>
                    <h3 class="text-xl font-bold text-[#2D5A34]">Tabel Pembuktian Algoritma SMA</h3>
                    <p class="text-sm text-gray-400 mt-1">Langkah matematis kalkulasi prediksi demand ($n =
                        {{ $n }} \text{ hari}$)</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="text-[10px] uppercase tracking-wider text-gray-400 border-b border-gray-100 bg-gray-50/50">
                            <th class="py-3 px-4 font-bold">Tanggal</th>
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
                                            {{ $n }} hari historis</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-right">
                                    @if ($row->error !== null)
                                        <span
                                            class="px-2.5 py-1 rounded-md text-xs font-bold {{ $row->error > 0 ? 'bg-red-50 text-red-600' : ($row->error < 0 ? 'bg-blue-50 text-blue-600' : 'bg-green-50 text-green-600') }}">
                                            {{ $row->error > 0 ? '+' : '' }}{{ $row->error }}
                                        </span>
                                    @else
                                        -
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
        const dataGrafik = {
            weekly: {
                labels: @json($chartSmaLabels),
                actual: @json($chartSmaAktual),
                forecast: @json($chartSmaPrediksi)
            },
            monthly: {
                labels: @json($chartLabelsMonthly),
                actual: @json($chartDataMonthly),
                forecast: Array.from({
                    length: 30
                }, () => Math.floor(Math.random() * (20 - 5 + 1)) + 5)
            }
        };

        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('demandChart').getContext('2d');
            window.demandChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: dataGrafik.weekly.labels,
                    datasets: [{
                            type: 'line',
                            label: 'Forecast',
                            data: dataGrafik.weekly.forecast,
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
                            data: dataGrafik.weekly.actual,
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
                            beginAtZero: true,
                            grid: {
                                display: false
                            },
                            ticks: {
                                stepSize: 1
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        });

        window.switchChartMode = function(mode) {
            if (window.demandChart) {
                window.demandChart.data.labels = dataGrafik[mode].labels;
                window.demandChart.data.datasets[0].data = dataGrafik[mode].forecast;
                window.demandChart.data.datasets[1].data = dataGrafik[mode].actual;
                window.demandChart.update();
            }
        };
    </script>
@endsection
