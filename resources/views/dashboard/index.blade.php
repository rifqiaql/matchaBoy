@extends('layouts.app')

@section('content')
    <div class="p-8">

        <!-- Kumpulan Card Atas -->
        <div class="grid grid-cols-4 gap-6 mb-8">
            <!-- CARD 1: BUBUK MATCHA -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm font-semibold text-gray-500">Bubuk Matcha</span>
                    <div class="relative flex items-center justify-center w-10 h-10">
                        <div class="absolute inset-0 bg-dark-matcha opacity-20 blur-md rounded-xl"></div>
                        <div
                            class="relative flex items-center justify-center w-full h-full bg-white/80 backdrop-blur-sm rounded-xl border border-gray-100 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-leaf-icon lucide-leaf">
                                <path
                                    d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z" />
                                <path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12" />
                            </svg>
                        </div>
                    </div>
                </div>
                <p class="text-3xl font-bold text-dark-matcha">
                    {{ $matcha ? $matcha->stok_saat_ini : 0 }}
                    <span class="text-lg">{{ $matcha ? $matcha->satuan : '' }}</span>
                </p>
                <p class="text-xs text-gray-400 mt-2">Premium Grade</p>
                <div class="w-full bg-gray-100 rounded-full h-2 mt-3">
                    <div class="bg-dark-matcha h-2 rounded-full w-3/4"></div>
                </div>
            </div>

            <!-- CARD 2: FULL CREAM -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm font-semibold text-gray-500">Full Cream</span>
                    <div class="relative flex items-center justify-center w-10 h-10">
                        <div class="absolute inset-0 bg-dark-matcha opacity-20 blur-md rounded-xl"></div>
                        <div
                            class="relative flex items-center justify-center w-full h-full bg-white/80 backdrop-blur-sm rounded-xl border border-gray-100 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-milk-icon lucide-milk">
                                <path d="M8 2h8" />
                                <path
                                    d="M9 2v2.789a4 4 0 0 1-.672 2.219l-.656.984A4 4 0 0 0 7 10.212V20a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-9.789a4 4 0 0 0-.672-2.219l-.656-.984A4 4 0 0 1 15 4.788V2" />
                                <path d="M7 15a6.472 6.472 0 0 1 5 0 6.47 6.47 0 0 0 5 0" />
                            </svg>
                        </div>
                    </div>
                </div>
                <p class="text-3xl font-bold text-yellow-500">
                    {{ $fullCream ? $fullCream->stok_saat_ini : 0 }}
                    <span class="text-lg">{{ $fullCream ? $fullCream->satuan : '' }}</span>
                </p>
                <p class="text-xs text-gray-400 mt-2">Stock Supplier</p>
                <div class="w-full bg-gray-100 rounded-full h-2 mt-3">
                    <div class="bg-yellow-500 h-2 rounded-full w-1/2"></div>
                </div>
            </div>

            <!-- CARD 3: SELAI STRAWBERRY -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm font-semibold text-gray-500">Selai Strawberry</span>
                    <div class="relative flex items-center justify-center w-10 h-10">
                        <div class="absolute inset-0 bg-dark-matcha opacity-20 blur-md rounded-xl"></div>
                        <div
                            class="relative flex items-center justify-center w-full h-full bg-white/80 backdrop-blur-sm rounded-xl border border-gray-100 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-vegan-icon lucide-vegan">
                                <path d="M16 8q6 0 6-6-6 0-6 6" />
                                <path d="M17.41 3.59a10 10 0 1 0 3 3" />
                                <path d="M2 2a26.6 26.6 0 0 1 10 20c.9-6.82 1.5-9.5 4-14" />
                            </svg>
                        </div>
                    </div>
                </div>
                <p class="text-3xl font-bold text-pink-400">
                    {{ $strawberry ? $strawberry->stok_saat_ini : 0 }}
                    <span class="text-lg">{{ $strawberry ? $strawberry->satuan : '' }}</span>
                </p>
                <p class="text-xs text-gray-400 mt-2">Tersedia</p>
                <div class="w-full bg-gray-100 rounded-full h-2 mt-3">
                    <div class="bg-pink-400 h-2 rounded-full w-2/3"></div>
                </div>
            </div>

            <!-- CARD 4: ES BATU -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm font-semibold text-gray-500">Es Batu</span>
                    <div class="relative flex items-center justify-center w-10 h-10">
                        <div class="absolute inset-0 bg-dark-matcha opacity-20 blur-md rounded-xl"></div>
                        <div
                            class="relative flex items-center justify-center w-full h-full bg-white/80 backdrop-blur-sm rounded-xl border border-gray-100 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-cookie-icon lucide-cookie">
                                <path d="M12 2a10 10 0 1 0 10 10 4 4 0 0 1-5-5 4 4 0 0 1-5-5" />
                                <path d="M8.5 8.5v.01" />
                                <path d="M16 15.5v.01" />
                                <path d="M12 12v.01" />
                                <path d="M11 17v.01" />
                                <path d="M7 14v.01" />
                            </svg>
                        </div>
                    </div>
                </div>
                <p class="text-3xl font-bold text-green-500">
                    {{ $esBatu ? $esBatu->stok_saat_ini : 0 }}
                    <span class="text-lg">{{ $esBatu ? $esBatu->satuan : '' }}</span>
                </p>
                <p class="text-xs text-gray-400 mt-2">In Stock</p>
                <div class="w-full bg-gray-100 rounded-full h-2 mt-3">
                    <div class="bg-green-500 h-2 rounded-full w-4/5"></div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-6 mb-8">

            <div class="col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold mb-1 text-gray-800">Demand Analysis</h3>
                <p class="text-sm text-gray-400 mb-6">Actual vs. Forecasted consumption</p>

                <div class="flex items-end justify-around h-56 gap-2 mt-4">
                    <div class="flex flex-col items-center gap-3">
                        <div class="w-10 bg-[#2D5A34] rounded-t-lg transition-all hover:bg-green-600"
                            style="height: 120px;"></div>
                        <span class="text-xs font-semibold text-gray-500">MON</span>
                    </div>
                    <div class="flex flex-col items-center gap-3">
                        <div class="w-10 bg-[#2D5A34] rounded-t-lg transition-all hover:bg-green-600"
                            style="height: 100px;"></div>
                        <span class="text-xs font-semibold text-gray-500">TUE</span>
                    </div>
                    <div class="flex flex-col items-center gap-3">
                        <div class="w-10 bg-[#2D5A34] rounded-t-lg transition-all hover:bg-green-600"
                            style="height: 140px;"></div>
                        <span class="text-xs font-semibold text-gray-500">WED</span>
                    </div>
                    <div class="flex flex-col items-center gap-3">
                        <div class="w-10 bg-[#2D5A34] rounded-t-lg transition-all hover:bg-green-600"
                            style="height: 130px;"></div>
                        <span class="text-xs font-semibold text-gray-500">THU</span>
                    </div>
                    <div class="flex flex-col items-center gap-3">
                        <div class="w-10 bg-[#2D5A34] rounded-t-lg transition-all hover:bg-green-600"
                            style="height: 110px;"></div>
                        <span class="text-xs font-semibold text-gray-500">FRI</span>
                    </div>
                    <div class="flex flex-col items-center gap-3">
                        <div class="w-10 bg-[#2D5A34] rounded-t-lg transition-all hover:bg-green-600"
                            style="height: 155px;"></div>
                        <span class="text-xs font-semibold text-gray-500">SAT</span>
                    </div>
                    <div class="flex flex-col items-center gap-3">
                        <div class="w-10 bg-[#2D5A34] rounded-t-lg transition-all hover:bg-green-600"
                            style="height: 135px;"></div>
                        <span class="text-xs font-semibold text-gray-500">SUN</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-6">
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex-1">
                    <h3 class="text-lg font-bold mb-6 text-gray-800">Top 3 Products</h3>
                    <div class="space-y-5">

                        @forelse ($topProducts as $top)
                            <div>
                                <p class="text-sm font-semibold text-gray-700">{{ $top->name }}</p>
                                <div class="flex items-center justify-between mt-2">
                                    <div class="flex-1 bg-gray-100 rounded-full h-2 mr-3">
                                        <div class="bg-[#2D5A34] h-2 rounded-full" style="width: 80%;"></div>
                                    </div>
                                    <span class="text-xs font-bold text-gray-600">{{ $top->total_sold }} Terjual</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-sm text-gray-400 text-center py-4">
                                Belum ada data penjualan dari kasir.
                            </div>
                        @endforelse

                    </div>
                </div>

                <!-- CARD URGENT WARNING DINAMIS -->
                @php
                    $allBahan = \App\Models\BahanBaku::all();
                    $kritisItems = $allBahan->filter(function($item) {
                        return ($item->stok_awal > 0 ? ($item->stok_saat_ini / $item->stok_awal) * 100 : 0) <= 20;
                    });
                    $lowStockCount = $kritisItems->count();
                @endphp

                @if($lowStockCount > 0)
                <div class="bg-red-50 rounded-2xl p-5 border border-red-100 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-red-600">⚠️</span>
                            <p class="text-sm font-bold text-red-700">Peringatan Kritis ({{ $lowStockCount }} Item)</p>
                        </div>
                        <p class="text-xs text-gray-800 mt-2 font-medium">Bahan baku berikut berstatus KRITIS (Stok ≤ 20%):</p>
                        
                        <ul class="mt-3 space-y-2">
                            @foreach($kritisItems->take(3) as $item)
                                <li class="flex items-center justify-between text-xs bg-white/60 p-2 rounded border border-red-100">
                                    <span class="font-semibold text-gray-700">{{ $item->nama_bahan }}</span>
                                    <span class="font-bold text-red-600">{{ $item->stok_saat_ini }} {{ $item->satuan }}</span>
                                </li>
                            @endforeach
                        </ul>
                        
                        @if($lowStockCount > 3)
                            <p class="text-[10px] text-gray-500 mt-2 italic font-medium">+ {{ $lowStockCount - 3 }} item lainnya menipis...</p>
                        @endif
                    </div>
                    <a href="{{ route('inventory.index') }}"
                        class="mt-5 w-full block text-center bg-red-600 text-white text-xs font-bold py-2.5 rounded-lg hover:bg-red-700 transition-colors shadow-sm">
                        Periksa Detail Gudang
                    </a>
                </div>
                @else
                <div class="bg-green-50 rounded-2xl p-5 border border-green-100 shadow-sm flex flex-col justify-between h-full">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-green-600">✅</span>
                            <p class="text-sm font-bold text-green-700">Status Gudang Aman</p>
                        </div>
                        <p class="text-xs text-gray-600 mt-3 leading-relaxed">Seluruh bahan baku saat ini berada dalam kondisi persentase yang aman (> 20%).</p>
                    </div>
                    <a href="{{ route('inventory.index') }}"
                        class="mt-4 w-full block text-center bg-[#365E3F] text-white text-xs font-bold py-2.5 rounded-lg hover:bg-[#2a4a31] transition-colors shadow-sm">
                        Buka Modul Gudang
                    </a>
                </div>
                @endif
            </div>

        </div>

        <!-- BLOK BAWAH DINAMIS -->
        <div class="grid grid-cols-2 gap-6">

            <!-- KIRI: STATUS GUDANG (INVENTORY HEALTH SUMMARY) -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold mb-4 text-gray-800">Status Stok Gudang</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead>
                            <tr class="border-b border-gray-100 text-gray-400">
                                <th class="pb-3 font-medium">Nama Bahan</th>
                                <th class="pb-3 font-medium">Sisa Kuantitas</th>
                                <th class="pb-3 font-medium">Indikator</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @php
                                // Mengambil semua bahan, kalkulasi persentase, dan urutkan dari yang paling sekarat
                                $inventoryStatus = \App\Models\BahanBaku::all()->map(function($item) {
                                    $persentase = $item->stok_awal > 0 ? ($item->stok_saat_ini / $item->stok_awal) * 100 : 0;
                                    $item->persentase = round($persentase);
                                    return $item;
                                })->sortBy('persentase')->take(4);
                            @endphp

                            @forelse($inventoryStatus as $item)
                                @php
                                    if($item->persentase <= 20) {
                                        $badgeClass = 'bg-red-50 text-red-600';
                                        $badgeText = 'KRITIS';
                                    } elseif($item->persentase <= 50) {
                                        $badgeClass = 'bg-yellow-50 text-yellow-600';
                                        $badgeText = 'MENIPIS';
                                    } else {
                                        $badgeClass = 'bg-green-50 text-green-600';
                                        $badgeText = 'AMAN';
                                    }
                                @endphp
                                <tr>
                                    <td class="py-4 font-semibold text-gray-700">{{ $item->nama_bahan }}</td>
                                    <td class="py-4 text-gray-600 font-medium">
                                        {{ $item->stok_saat_ini }} <span class="text-xs text-gray-400">{{ $item->satuan }}</span>
                                    </td>
                                    <td class="py-4">
                                        <span class="px-3 py-1.5 {{ $badgeClass }} rounded-md text-[10px] tracking-wider uppercase font-bold">
                                            {{ $badgeText }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-6 text-center text-gray-400 italic font-medium">Data bahan baku kosong.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- KANAN: AKTIVITAS TERBARU (ORDER KASIR) -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold mb-6 text-gray-800">Aktivitas Transaksi Terbaru</h3>
                <div class="space-y-6">
                    @php
                        // Menarik 4 order transaksi terakhir dari database
                        $recentOrders = \App\Models\Order::with('user')->latest()->take(4)->get();
                    @endphp

                    @forelse($recentOrders as $order)
                        <div class="flex gap-4">
                            <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center flex-shrink-0 border border-blue-100">
                                <span class="text-sm">🧾</span>
                            </div>
                            <div class="flex-1">
                                <div class="flex justify-between items-start">
                                    <p class="text-sm font-bold text-gray-800">Pesanan Masuk #{{ $order->invoice_number }}</p>
                                    <span class="text-xs font-bold text-[#365E3F]">
                                        Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">
                                    Diproses oleh Kasir: {{ $order->user->name ?? 'Admin' }}
                                </p>
                                <p class="text-[10px] font-semibold text-gray-400 mt-2 uppercase tracking-wider">
                                    {{ $order->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="text-sm text-gray-400 text-center py-4 italic">
                            Belum ada riwayat transaksi kasir.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
@endsection