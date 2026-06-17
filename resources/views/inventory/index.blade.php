@extends('layouts.app')

@section('content')
    <div class="p-8">
        <!-- Header Section -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Gudang Inventory</h1>
                <p class="text-sm text-gray-500 mt-1">Kelola stok bahan baku tea house</p>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center gap-3">
                <span class="text-green-600 text-xl">✓</span>
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Top Action Bar -->
        <div class="flex items-center justify-between gap-4 mb-8">
            <div class="flex items-center gap-3 flex-1">
                <!-- Search Input -->
                <div class="flex-1 relative">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" placeholder="Cari bahan baku..."
                        class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#365E3F] focus:border-transparent transition-all bg-white text-gray-900 placeholder-gray-500">
                </div>

                <!-- Category Dropdown -->
                <select
                    class="px-4 py-2.5 rounded-lg border border-gray-200 text-gray-700 font-medium hover:bg-gray-50 transition-colors bg-white focus:outline-none focus:ring-2 focus:ring-[#365E3F] focus:border-transparent">
                    <option value="">Kategori</option>
                    <option value="bahan-pokok">Bahan Pokok</option>
                    <option value="bumbu">Bumbu</option>
                    <option value="topping">Topping</option>
                    <option value="packaging">Packaging</option>
                </select>
            </div>

            <div class="flex items-center gap-3">
                <button
                    class="px-4 py-2.5 rounded-lg border border-gray-200 text-gray-700 font-medium hover:bg-gray-50 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                        </path>
                    </svg>
                    <span class="text-sm">Filter</span>
                </button>
                <button
                    class="px-4 py-2.5 rounded-lg border border-gray-200 text-gray-700 font-medium hover:bg-gray-50 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                    <span class="text-sm">Export</span>
                </button>
                <button type="button" onclick="openModal()"
                    class="btn-icon primary bg-[#365E3F] text-white px-4 py-2.5 rounded-lg flex items-center gap-2 hover:bg-[#2a4a31] transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span class="text-sm font-medium">Tambah Bahan Baku</span>
                </button>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-3 gap-6 mb-8">
            <!-- Card 1: Total Bahan -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Total Bahan</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $bahanBaku->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m0 0l8-4m0 0l8 4m-8 4l-8-4v4a8 8 0 0016 0v-4"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-xs text-gray-600"><span class="text-green-600 font-semibold">+2</span> last month</p>
            </div>

            <!-- Card 2: Low Stock -->
            @php
                $lowStockCount = $bahanBaku
                    ->filter(function ($item) {
                        $persentase = $item->stok_awal > 0 ? round(($item->stok_saat_ini / $item->stok_awal) * 100) : 0;
                        return $persentase <= 50;
                    })
                    ->count();
                $hasLowStock = $lowStockCount > 0;
            @endphp
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Low Stock</p>
                        <p class="text-3xl font-bold {{ $hasLowStock ? 'text-red-600' : 'text-gray-900' }} mt-2">
                            {{ $lowStockCount }}</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4v2m0 4v2m0-8a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-xs {{ $hasLowStock ? 'text-red-600 font-semibold' : 'text-green-600 font-semibold' }}">
                    {{ $hasLowStock ? 'Requires attention' : 'All stock safe' }}
                </p>
            </div>

            <!-- Card 3: Monthly Restock -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Monthly Restock</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">12</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-xs text-gray-600">Status: <span class="text-[#365E3F] font-semibold">On schedule</span></p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            @if ($bahanBaku->isEmpty())
                <div class="p-12 text-center">
                    <p class="text-gray-500 text-sm mb-4">Belum ada bahan baku. Silakan tambahkan terlebih dahulu.</p>
                    <a href="{{ route('inventory.create') }}" class="text-dark-matcha font-semibold hover:underline">
                        Tambah Bahan Baku Sekarang →
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-100 bg-gray-50">
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Nama Bahan</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Category</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Stock</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Unit</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Limit</th>
                                <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700">Status</th>
                                <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($bahanBaku as $item)
                                @php
                                    $persentase =
                                        $item->stok_awal > 0
                                            ? round(($item->stok_saat_ini / $item->stok_awal) * 100)
                                            : 0;

                                    if ($persentase <= 20) {
                                        $statusClass = 'bg-red-50 text-red-600';
                                        $statusLabel = 'KRITIS';
                                    } elseif ($persentase <= 50) {
                                        $statusClass = 'bg-yellow-50 text-yellow-600';
                                        $statusLabel = 'RENDAH';
                                    } else {
                                        $statusClass = 'bg-green-50 text-green-600';
                                        $statusLabel = 'BAIK';
                                    }
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <p class="font-medium text-gray-800">{{ $item->nama_bahan }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm text-gray-600">Bahan Pokok</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex flex-col items-start gap-1">
                                            <span
                                                class="text-sm font-semibold text-gray-800">{{ $item->stok_saat_ini }}</span>
                                            {{-- <div class="w-24 bg-gray-100 rounded-full h-2">
                                                <div class="bg-dark-matcha h-2 rounded-full"
                                                    style="width: {{ $persentase }}%"></div>
                                            </div> --}}
                                            {{-- <span class="text-xs text-gray-500">{{ $persentase }}%</span> --}}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm text-gray-600">{{ $item->satuan }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-left">
                                        <span class="text-sm font-medium text-gray-700">{{ $item->stok_awal }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-3 py-1.5 rounded-full text-xs font-bold {{ $statusClass }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <button
                                            class="p-1 hover:bg-gray-100 rounded transition-colors text-gray-400 hover:text-gray-600">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z">
                                                </path>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Footer -->
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
                    <p class="text-sm text-gray-600">Showing <span class="font-semibold">1</span> to <span
                            class="font-semibold">10</span> of <span
                            class="font-semibold">{{ $bahanBaku->count() }}</span> entries</p>
                    <div class="flex items-center gap-2">
                        <button
                            class="px-3 py-2 rounded-lg border border-gray-200 text-gray-700 text-sm font-medium hover:bg-gray-100 transition-colors">Previous</button>
                        <button class="px-3 py-2 rounded-lg bg-[#365E3F] text-white text-sm font-medium">1</button>
                        <button
                            class="px-3 py-2 rounded-lg border border-gray-200 text-gray-700 text-sm font-medium hover:bg-gray-100 transition-colors">2</button>
                        <button
                            class="px-3 py-2 rounded-lg border border-gray-200 text-gray-700 text-sm font-medium hover:bg-gray-100 transition-colors">3</button>
                        <button
                            class="px-3 py-2 rounded-lg border border-gray-200 text-gray-700 text-sm font-medium hover:bg-gray-100 transition-colors">Next</button>
                    </div>
                </div>
            @endif
        </div>
    </div>
    @include('inventory.modal_create')
@endsection
