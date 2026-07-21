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
                        <x-date_picker />

                        <a href="{{ route('laporan.export') }}"
                            class="bg-[#2E4F4F] text-white px-4 py-1.5 rounded-lg shadow-sm text-sm font-semibold hover:bg-opacity-90 transition inline-block text-center">
                            Export Excel
                        </a>
                    </div>

                    <!-- REVISI: Default activeTab diubah ke 'weekly' agar sinkron dengan data asli dari database (7 hari) -->
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
                <!-- REVISI: Demand Analysis dengan Chart.js -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="flex justify-between items-center mb-1">
                        <h3 class="text-lg font-bold text-gray-800">Demand Analysis</h3>
                    </div>
                    <p class="text-sm text-gray-400 mb-6">Actual vs. Forecasted consumption</p>

                    <!-- Container Grafik: Fixed Height agar layout stabil saat diganti bulanan -->
                    <div class="w-full h-64 mt-4 relative">
                        <canvas id="demandChart"></canvas>
                    </div>
                </div>

                <!-- Stock Fluctuations Widget -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-800">Stock Fluctuations</h3>
                        <span class="text-xs font-bold text-green-600 bg-green-50 px-2 py-0.5 rounded-full">+8.4%
                            Efficiency</span>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <span class="text-xs text-gray-400 font-bold block uppercase">Available Stock</span>
                            <span class="text-2xl font-extrabold text-gray-900">1,284 <span
                                    class="text-sm font-normal text-gray-500">Units</span></span>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <span class="text-xs text-gray-400 font-bold block uppercase">Reserve Capacity</span>
                            <span class="text-2xl font-extrabold text-gray-900">24%</span>
                        </div>
                    </div>
                </div>

                <!-- AI Summary Prediction Box -->
                <div class="bg-dark-matcha text-white p-6 rounded-2xl shadow-sm flex items-start space-x-4 w-full">
                    <div class="p-2.5 bg-white/20 rounded-xl flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-md font-bold">Summary Prediction AI Insight</h4>
                        <p class="text-sm text-gray-200 mt-1">
                            Demand for <span class="underline font-medium">Ceremonial Matcha</span> is expected to rise by
                            <span class="font-bold text-white">15% next week</span> due to local holiday foot traffic.
                            Recommend increasing base order of milk by 2 cases.
                        </p>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: PERFORMANCE METRICS & WASTES -->
            <div class="col-span-1 space-y-6">
                <!-- Performance Metrics Circle Indicators -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Performance Metrics</h3>
                    <div class="grid grid-cols-2 gap-2 text-center">
                        <div class="p-3 border rounded-xl flex flex-col items-center">
                            <div
                                class="w-12 h-12 rounded-full border-4 border-[#86A789] flex items-center justify-center font-bold text-sm">
                                4.2x</div>
                            <span class="text-[11px] text-gray-500 mt-2">Inventory Turnover</span>
                        </div>
                        <div class="p-3 border rounded-xl flex flex-col items-center">
                            <div
                                class="w-12 h-12 rounded-full border-4 border-[#86A789] flex items-center justify-center font-bold text-sm">
                                94%</div>
                            <span class="text-[11px] text-gray-500 mt-2">Prediction Accuracy</span>
                        </div>
                    </div>
                </div>

                <!-- Waste Identification Panel -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-md font-bold text-gray-800">Waste Identification</h3>
                        <span class="text-xs font-bold text-red-500 bg-red-50 px-2 py-0.5 rounded">3.5% avg</span>
                    </div>
                    <div class="space-y-3">
                        <div class="p-3 bg-red-50 rounded-xl border border-red-100 flex justify-between items-center">
                            <div>
                                <h4 class="text-xs font-bold text-gray-800">Whole Milk (Perishable)</h4>
                                <p class="text-[10px] text-red-500">Expiring in 2 days</p>
                            </div>
                            <span class="font-bold text-xs text-gray-700">12.5L</span>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-xl flex justify-between items-center">
                            <div>
                                <h4 class="text-xs font-bold text-gray-800">Oat Milk</h4>
                                <p class="text-[10px] text-gray-400">Overstock surplus</p>
                            </div>
                            <span class="font-bold text-xs text-gray-700">4.2L</span>
                        </div>
                    </div>
                </div>

                <!-- Product Priority Usage -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                    <h3 class="text-md font-bold text-gray-800 mb-4">Product Priority</h3>
                    <div class="space-y-3">
                        @foreach (['Ceremonial Matcha' => '88%', 'Hojicha Powder' => '62%', 'Bamboo Whisks' => '45%'] as $prod => $pct)
                            <div>
                                <div class="flex justify-between text-xs font-medium mb-1">
                                    <span>{{ $prod }}</span>
                                    <span>{{ $pct }}</span>
                                </div>
                                <div class="w-full bg-gray-100 h-2 rounded-full">
                                    <div class="bg-[#2E4F4F] h-2 rounded-full" style="width: {{ $pct }}"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- BOTTOM BLOCK: DINAMIS RESTOCK PLANNING FROM DATABASE -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 mt-6 w-full">
            <div class="mb-6">
                <h3 class="text-xl font-bold text-[#2D5A34]">Restock Planning</h3>
                <p class="text-sm text-gray-400 mt-1">Inventory replenishment logic based on time-series forecasting</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-[10px] uppercase tracking-wider text-gray-400 border-b border-gray-100">
                            <th class="pb-3 font-bold">Ingredient Name</th>
                            <th class="pb-3 font-bold">Current Stock</th>
                            <th class="pb-3 font-bold">Average Usage</th>
                            <th class="pb-3 font-bold">Predicted Depletion</th>
                            <th class="pb-3 font-bold">Reorder Qty</th>
                            <th class="pb-3 font-bold">Status</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm align-top">
                        @forelse($ingredients as $ing)
                            <tr class="border-b border-gray-50">
                                <td class="py-5 font-bold text-[#2D5A34]">{{ $ing->nama_bahan }}</td>
                                <td class="py-5 font-bold text-gray-800">{{ $ing->stok_saat_ini }}</td>
                                <td class="py-5 text-gray-500">450g /<br>week</td>
                                <td class="py-5 font-bold text-red-600 flex items-start gap-1">
                                    <span class="text-gray-700 font-normal">Estimasi Sistem</span>
                                </td>
                                <td class="py-5 font-bold text-gray-800">5.0 kg</td>
                                <td class="py-5">
                                    @if ($ing->stok_saat_ini < 1000)
                                        <span
                                            class="bg-red-50 text-red-500 px-3 py-1.5 rounded-full text-[10px] font-bold tracking-wider uppercase">Restock</span>
                                    @else
                                        <span
                                            class="bg-green-50 text-green-600 px-3 py-1.5 rounded-full text-[10px] font-bold tracking-wider uppercase">Cukup</span>
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

    </div>

    <!-- REVISI: Inisialisasi Script Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // 1. Deklarasi Objek Data Grafik Global agar bisa dibaca semua fungsi
        const dataGrafik = {
            weekly: {
                // INJEKSI PHP: Menarik array tanggal 7 hari terakhir dan data transaksinya
                labels: @json($chartLabels),
                actual: @json($chartData),
                // Data statis sementara untuk garis Forecast (karena ini UI AI dummy)
                forecast: [10, 15, 20, 25, 30, 25, 15]
            },
            monthly: {
                labels: Array.from({
                    length: 30
                }, (_, i) => `${i + 1} Jul`),
                actual: [120, 100, 140, 130, 110, 155, 135, 140, 130, 125, 150, 160, 145, 130, 140, 155, 165, 170, 150,
                    140, 135, 145, 160, 155, 150, 165, 175, 180, 170, 160
                ],
                forecast: [115, 105, 135, 130, 115, 150, 140, 135, 135, 130, 145, 155, 150, 135, 145, 150, 160, 165,
                    155, 145, 140, 140, 155, 150, 155, 160, 170, 175, 175, 165
                ]
            }
        };

        // 2. Inisialisasi Chart saat halaman selesai diload
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('demandChart').getContext('2d');
            window.demandChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    // Default menggunakan data Weekly (Real Database Data)
                    labels: dataGrafik.weekly.labels,
                    datasets: [{
                            type: 'line',
                            label: 'Forecast',
                            data: dataGrafik.weekly.forecast,
                            borderColor: '#86A789',
                            borderDash: [5, 5],
                            backgroundColor: 'transparent',
                            tension: 0.4,
                            pointRadius: 2
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
                            } // Memaksa y-axis tidak desimal (1 transaksi bukan 1.5)
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

        // 3. Fungsi Global untuk diakses oleh tombol Alpine.js
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
