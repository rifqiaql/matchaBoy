@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-100">
    @include('partials.sidebar')

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Header -->
        <div class="bg-green-700 text-white px-8 py-4 flex items-center justify-between shadow">
            <div class="flex items-center gap-4">
                <span class="text-2xl font-bold">🍵</span>
                <div>
                    <p class="text-sm opacity-90">12.02.03 PM</p>
                    <p class="text-sm opacity-90">Minggu, 5 September 2026</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center">
                    <x-icon name="user-circle" size="md" class="w-6 h-6 stroke-current text-green-700" />
                </div>
                <div>
                    <p class="font-semibold">Bu Tini</p>
                    <p class="text-xs opacity-90">Admin</p>
                </div>
                <button class="ml-2 text-lg">➡️</button>
            </div>
        </div>

        <!-- Scrollable Content -->
        <div class="flex-1 overflow-auto">
            <div class="p-8">
                <!-- Stats Cards -->
                <div class="grid grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-lg p-6 shadow">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm text-gray-600">Bubuk Matcha</span>
                            <span class="text-xl">🥄</span>
                        </div>
                        <p class="text-3xl font-bold text-green-700">2.5 kg</p>
                        <p class="text-xs text-gray-500 mt-2">Premium Grade</p>
                        <div class="w-full bg-gray-200 rounded-full h-2 mt-3">
                            <div class="bg-green-500 h-2 rounded-full w-3/4"></div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg p-6 shadow">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm text-gray-600">Full Cream</span>
                            <span class="text-xl">🥛</span>
                        </div>
                        <p class="text-3xl font-bold text-yellow-500">15 L</p>
                        <p class="text-xs text-gray-500 mt-2">Stock Supplier</p>
                        <div class="w-full bg-gray-200 rounded-full h-2 mt-3">
                            <div class="bg-yellow-500 h-2 rounded-full w-1/2"></div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg p-6 shadow">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm text-gray-600">Sirup Strawberry</span>
                            <span class="text-xl">🍓</span>
                        </div>
                        <p class="text-3xl font-bold text-pink-400">15 L</p>
                        <p class="text-xs text-gray-500 mt-2">Tersedia</p>
                        <div class="w-full bg-gray-200 rounded-full h-2 mt-3">
                            <div class="bg-pink-400 h-2 rounded-full w-2/3"></div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg p-6 shadow">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm text-gray-600">Oats</span>
                            <span class="text-xl">🌾</span>
                        </div>
                        <p class="text-3xl font-bold text-green-500">5.2 kg</p>
                        <p class="text-xs text-gray-500 mt-2">In Stock</p>
                        <div class="w-full bg-gray-200 rounded-full h-2 mt-3">
                            <div class="bg-green-500 h-2 rounded-full w-4/5"></div>
                        </div>
                    </div>
                </div>

                <!-- Demand Analysis & Top Products -->
                <div class="grid grid-cols-3 gap-6 mb-8">
                    <!-- Chart -->
                    <div class="col-span-2 bg-white rounded-lg p-6 shadow">
                        <h3 class="text-lg font-semibold mb-4 text-gray-800">Demand Analysis</h3>
                        <p class="text-sm text-gray-500 mb-4">Actual vs. Forecasted consumption</p>
                        <div class="flex items-end justify-around h-64 gap-2">
                            <div class="flex flex-col items-center gap-2">
                                <div class="w-12 bg-green-700 rounded" style="height: 120px;"></div>
                                <span class="text-xs text-gray-600">MON</span>
                            </div>
                            <div class="flex flex-col items-center gap-2">
                                <div class="w-12 bg-green-700 rounded" style="height: 100px;"></div>
                                <span class="text-xs text-gray-600">TUE</span>
                            </div>
                            <div class="flex flex-col items-center gap-2">
                                <div class="w-12 bg-green-700 rounded" style="height: 140px;"></div>
                                <span class="text-xs text-gray-600">WED</span>
                            </div>
                            <div class="flex flex-col items-center gap-2">
                                <div class="w-12 bg-green-700 rounded" style="height: 130px;"></div>
                                <span class="text-xs text-gray-600">THU</span>
                            </div>
                            <div class="flex flex-col items-center gap-2">
                                <div class="w-12 bg-green-700 rounded" style="height: 110px;"></div>
                                <span class="text-xs text-gray-600">FRI</span>
                            </div>
                            <div class="flex flex-col items-center gap-2">
                                <div class="w-12 bg-green-700 rounded" style="height: 155px;"></div>
                                <span class="text-xs text-gray-600">SAT</span>
                            </div>
                            <div class="flex flex-col items-center gap-2">
                                <div class="w-12 bg-green-700 rounded" style="height: 135px;"></div>
                                <span class="text-xs text-gray-600">SUN</span>
                            </div>
                        </div>
                    </div>

                    <!-- Top Products -->
                    <div class="bg-white rounded-lg p-6 shadow">
                        <h3 class="text-lg font-semibold mb-4 text-gray-800">Top 3 Products</h3>
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm font-medium text-gray-700">Matcha Latte Original</p>
                                <div class="flex items-center justify-between mt-2">
                                    <div class="flex-1 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-gray-800 h-2 rounded-full" style="width: 80%;"></div>
                                    </div>
                                    <span class="text-xs font-semibold">142</span>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">Caramel Matcha Latte</p>
                                <div class="flex items-center justify-between mt-2">
                                    <div class="flex-1 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-yellow-600 h-2 rounded-full" style="width: 60%;"></div>
                                    </div>
                                    <span class="text-xs font-semibold">98</span>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">Strawberry Cream Matcha</p>
                                <div class="flex items-center justify-between mt-2">
                                    <div class="flex-1 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-pink-400 h-2 rounded-full" style="width: 55%;"></div>
                                    </div>
                                    <span class="text-xs font-semibold">74</span>
                                </div>
                            </div>
                        </div>

                        <!-- Peringatan -->
                        <div class="mt-6 pt-6 border-t">
                            <div class="bg-red-50 rounded-lg p-4">
                                <p class="text-sm font-semibold text-red-700">⚠️ Peringatan</p>
                                <p class="text-xs text-red-600 mt-2">[URGENT ACTION]</p>
                                <p class="text-xs text-gray-700 mt-1">Sugar will run out in 3 days.</p>
                                <p class="text-xs text-gray-600 mt-1">Last order is on Supplier Soldierhan the next</p>
                                <button class="mt-3 w-full bg-green-700 text-white text-xs py-2 rounded hover:bg-green-800 transition">
                                    Buat Pesanan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Section -->
                <div class="grid grid-cols-2 gap-6">
                    <!-- Peringatant Stock -->
                    <div class="bg-white rounded-lg p-6 shadow">
                        <h3 class="text-lg font-semibold mb-4 text-gray-800">Peringatant Stock</h3>
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left py-2 text-gray-600">Nama Stock</th>
                                    <th class="text-left py-2 text-gray-600">Quantity</th>
                                    <th class="text-left py-2 text-gray-600">Priority</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                <tr>
                                    <td class="py-3">Bubuk Matcha</td>
                                    <td>1.2 kg</td>
                                    <td><span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-semibold">RESTOCK</span></td>
                                </tr>
                                <tr>
                                    <td class="py-3">SKM</td>
                                    <td>4 cans</td>
                                    <td><span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs font-semibold">TERSEDIA</span></td>
                                </tr>
                                <tr>
                                    <td class="py-3">Full Cream</td>
                                    <td>12 units</td>
                                    <td><span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-semibold">GOOD</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Aktivitas Terbaru -->
                    <div class="bg-white rounded-lg p-6 shadow">
                        <h3 class="text-lg font-semibold mb-4 text-gray-800">Aktivitas Terbaru</h3>
                        <div class="space-y-4">
                            <div class="flex gap-3">
                                <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                                    <span class="text-sm">✅</span>
                                </div>
                                <div class="text-sm">
                                    <p class="font-medium text-gray-800">Matcha Powder stock updated</p>
                                    <p class="text-gray-600">Manual stock entry by Admin | Increased by 5.0 kg</p>
                                    <p class="text-xs text-gray-500 mt-1">2 minutes ago</p>
                                </div>
                            </div>
                            <div class="flex gap-3">
                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                    <span class="text-sm">📦</span>
                                </div>
                                <div class="text-sm">
                                    <p class="font-medium text-gray-800">New order placed</p>
                                    <p class="text-gray-600">PO #008 2024-001 sent to Supplier. Soldier has received</p>
                                    <p class="text-xs text-gray-500 mt-1">1 hour ago</p>
                                </div>
                            </div>
                            <div class="flex gap-3">
                                <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                                    <span class="text-sm">⚠️</span>
                                </div>
                                <div class="text-sm">
                                    <p class="font-medium text-gray-800">Expiry alert for SKM</p>
                                    <p class="text-gray-600">Batch #4321 expiring in 3 days. Action required</p>
                                    <p class="text-xs text-gray-500 mt-1">6 hours ago</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
