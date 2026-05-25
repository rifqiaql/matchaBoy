@extends('layouts.app')

@section('content')
    <div class="p-8">

        <div class="grid grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm font-semibold text-gray-500">Bubuk Matcha</span>
                    <div class="relative flex items-center justify-center w-10 h-10">
                        <div class="absolute inset-0 bg-dark-matcha opacity-20 blur-md rounded-xl"></div>
                        <div
                            class="relative flex items-center justify-center w-full h-full bg-white/80 backdrop-blur-sm rounded-xl border border-gray-100 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-leaf-icon lucide-leaf"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/></svg>
                        </div>
                    </div>
                </div>
                <p class="text-3xl font-bold text-dark-matcha">2.5 kg</p>
                <p class="text-xs text-gray-400 mt-2">Premium Grade</p>
                <div class="w-full bg-gray-100 rounded-full h-2 mt-3">
                    <div class="bg-dark-matcha h-2 rounded-full w-3/4"></div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm font-semibold text-gray-500">Full Cream</span>
                    <span class="text-xl">🥛</span>
                </div>
                <p class="text-3xl font-bold text-yellow-500">15 L</p>
                <p class="text-xs text-gray-400 mt-2">Stock Supplier</p>
                <div class="w-full bg-gray-100 rounded-full h-2 mt-3">
                    <div class="bg-yellow-500 h-2 rounded-full w-1/2"></div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm font-semibold text-gray-500">Sirup Strawberry</span>
                    <span class="text-xl">🍓</span>
                </div>
                <p class="text-3xl font-bold text-pink-400">15 L</p>
                <p class="text-xs text-gray-400 mt-2">Tersedia</p>
                <div class="w-full bg-gray-100 rounded-full h-2 mt-3">
                    <div class="bg-pink-400 h-2 rounded-full w-2/3"></div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm font-semibold text-gray-500">Oats</span>
                    <span class="text-xl">🌾</span>
                </div>
                <p class="text-3xl font-bold text-green-500">5.2 kg</p>
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
                        <div>
                            <p class="text-sm font-semibold text-gray-700">Matcha Latte Original</p>
                            <div class="flex items-center justify-between mt-2">
                                <div class="flex-1 bg-gray-100 rounded-full h-2 mr-3">
                                    <div class="bg-[#2D5A34] h-2 rounded-full" style="width: 80%;"></div>
                                </div>
                                <span class="text-xs font-bold text-gray-600">142</span>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-700">Caramel Matcha Latte</p>
                            <div class="flex items-center justify-between mt-2">
                                <div class="flex-1 bg-gray-100 rounded-full h-2 mr-3">
                                    <div class="bg-[#86A789] h-2 rounded-full" style="width: 60%;"></div>
                                </div>
                                <span class="text-xs font-bold text-gray-600">98</span>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-700">Strawberry Cream Matcha</p>
                            <div class="flex items-center justify-between mt-2">
                                <div class="flex-1 bg-gray-100 rounded-full h-2 mr-3">
                                    <div class="bg-pink-400 h-2 rounded-full" style="width: 55%;"></div>
                                </div>
                                <span class="text-xs font-bold text-gray-600">74</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-red-50 rounded-2xl p-5 border border-red-100 shadow-sm">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-red-600">⚠️</span>
                        <p class="text-sm font-bold text-red-700">Peringatan [URGENT]</p>
                    </div>
                    <p class="text-xs text-gray-800 mt-2 font-medium">Sugar will run out in 3 days.</p>
                    <p class="text-xs text-gray-500 mt-1">Last order is on Supplier Soldierhan.</p>
                    <button
                        class="mt-4 w-full bg-dark-matcha text-white text-xs font-bold py-2.5 rounded-lg hover:bg-soft-matcha transition-colors shadow-sm">
                        Buat Pesanan
                    </button>
                </div>
            </div>

        </div>

        <div class="grid grid-cols-2 gap-6">

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold mb-4 text-gray-800">Peringatan Stock</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead>
                            <tr class="border-b border-gray-100 text-gray-400">
                                <th class="pb-3 font-medium">Nama Stock</th>
                                <th class="pb-3 font-medium">Quantity</th>
                                <th class="pb-3 font-medium">Priority</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <tr>
                                <td class="py-4 font-medium text-gray-700">Bubuk Matcha</td>
                                <td class="py-4 text-gray-500">1.2 kg</td>
                                <td class="py-4"><span
                                        class="px-3 py-1 bg-red-50 text-red-600 rounded-full text-xs font-bold">RESTOCK</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-4 font-medium text-gray-700">SKM</td>
                                <td class="py-4 text-gray-500">4 cans</td>
                                <td class="py-4"><span
                                        class="px-3 py-1 bg-yellow-50 text-yellow-600 rounded-full text-xs font-bold">TERSEDIA</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-4 font-medium text-gray-700">Full Cream</td>
                                <td class="py-4 text-gray-500">12 units</td>
                                <td class="py-4"><span
                                        class="px-3 py-1 bg-green-50 text-green-600 rounded-full text-xs font-bold">GOOD</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold mb-6 text-gray-800">Aktivitas Terbaru</h3>
                <div class="space-y-6">
                    <div class="flex gap-4">
                        <div
                            class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center flex-shrink-0 border border-green-100">
                            <span class="text-sm">✅</span>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-800">Matcha Powder stock updated</p>
                            <p class="text-xs text-gray-500 mt-1">Manual stock entry by Admin | Increased by 5.0 kg</p>
                            <p class="text-[10px] font-semibold text-gray-400 mt-2 uppercase tracking-wider">2 minutes ago
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div
                            class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center flex-shrink-0 border border-blue-100">
                            <span class="text-sm">📦</span>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-800">New order placed</p>
                            <p class="text-xs text-gray-500 mt-1">PO #008 2024-001 sent to Supplier. Soldier has received
                            </p>
                            <p class="text-[10px] font-semibold text-gray-400 mt-2 uppercase tracking-wider">1 hour ago</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div
                            class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center flex-shrink-0 border border-red-100">
                            <span class="text-sm">⚠️</span>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-800">Expiry alert for SKM</p>
                            <p class="text-xs text-gray-500 mt-1">Batch #4321 expiring in 3 days. Action required</p>
                            <p class="text-[10px] font-semibold text-gray-400 mt-2 uppercase tracking-wider">6 hours ago
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
