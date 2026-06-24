@extends('layouts.app')

@section('content')
    <div class="w-full p-8 space-y-6 box-border">

        <div class="flex justify-between items-center border-b border-gray-200 pb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Analytics & Stock Report</h1>
                <p class="text-sm text-gray-500">Predictive insights and artisanal inventory management</p>
            </div>
            <div class="flex items-center space-x-3">
                <span class="bg-white px-3 py-1.5 rounded-lg shadow-sm text-sm border text-gray-600">📅 Jan 1 - Jan 30</span>
                <button
                    class="bg-[#2E4F4F] text-white px-4 py-1.5 rounded-lg shadow-sm text-sm font-semibold">Export</button>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-6 items-start w-full">

            <div class="col-span-2 space-y-6">

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                    <h3 class="text-lg font-bold mb-1 text-gray-800">Demand Analysis</h3>
                    <p class="text-sm text-gray-400 mb-6">Actual vs. Forecasted consumption</p>

                    <div class="flex items-end justify-between h-56 gap-3 px-2 mt-4">
                        <div class="flex flex-col items-center gap-3 flex-1">
                            <div class="w-full bg-[#2D5A34] rounded-t-lg transition-all hover:bg-green-600"
                                style="height: 75%;"></div>
                            <span class="text-xs font-semibold text-gray-500">MON</span>
                        </div>
                        <div class="flex flex-col items-center gap-3 flex-1">
                            <div class="w-full bg-[#2D5A34] rounded-t-lg transition-all hover:bg-green-600"
                                style="height: 60%;"></div>
                            <span class="text-xs font-semibold text-gray-500">TUE</span>
                        </div>
                        <div class="flex flex-col items-center gap-3 flex-1">
                            <div class="w-full bg-[#2D5A34] rounded-t-lg transition-all hover:bg-green-600"
                                style="height: 85%;"></div>
                            <span class="text-xs font-semibold text-gray-500">WED</span>
                        </div>
                        <div class="flex flex-col items-center gap-3 flex-1">
                            <div class="w-full bg-[#2D5A34] rounded-t-lg transition-all hover:bg-green-600"
                                style="height: 80%;"></div>
                            <span class="text-xs font-semibold text-gray-500">THU</span>
                        </div>
                        <div class="flex flex-col items-center gap-3 flex-1">
                            <div class="w-full bg-[#2D5A34] rounded-t-lg transition-all hover:bg-green-600"
                                style="height: 70%;"></div>
                            <span class="text-xs font-semibold text-gray-500">FRI</span>
                        </div>
                        <div class="flex flex-col items-center gap-3 flex-1">
                            <div class="w-full bg-[#2D5A34] rounded-t-lg transition-all hover:bg-green-600"
                                style="height: 95%;"></div>
                            <span class="text-xs font-semibold text-gray-500">SAT</span>
                        </div>
                        <div class="flex flex-col items-center gap-3 flex-1">
                            <div class="w-full bg-[#2D5A34] rounded-t-lg transition-all hover:bg-green-600"
                                style="height: 82%;"></div>
                            <span class="text-xs font-semibold text-gray-500">SUN</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Stock Fluctuations</h3>
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

            </div>

            <div class="col-span-1 space-y-6">

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

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-md font-bold text-gray-800">Waste Identification</h3>
                        <span class="text-xs font-bold text-red-500 bg-red-50 px-2 py-0.5 rounded">3.5% avg</span>
                    </div>
                    <div class="p-3 bg-red-50 rounded-xl border border-red-100 flex justify-between items-center">
                        <div>
                            <h4 class="text-xs font-bold text-gray-800">Whole Milk (Perishable)</h4>
                            <p class="text-[10px] text-red-500">Expiring in 2 days</p>
                        </div>
                        <span class="font-bold text-xs text-gray-700">12.5L</span>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                    <h3 class="text-md font-bold text-gray-800 mb-4">Product Priority</h3>
                    <div class="space-y-3">
                        <div>
                            <div class="flex justify-between text-xs font-medium mb-1">
                                <span>Ceremonial Matcha</span>
                                <span>88%</span>
                            </div>
                            <div class="w-full bg-gray-100 h-2 rounded-full">
                                <div class="bg-[#2E4F4F] h-2 rounded-full" style="width: 88%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-xs font-medium mb-1">
                                <span>Hojicha Powder</span>
                                <span>62%</span>
                            </div>
                            <div class="w-full bg-gray-100 h-2 rounded-full">
                                <div class="bg-[#2E4F4F] h-2 rounded-full" style="width: 62%"></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="bg-[#2E4F4F] text-white p-6 rounded-2xl shadow-sm flex items-start space-x-4 w-full">
            <div class="p-2 bg-white bg-opacity-10 rounded-xl text-lg">✨</div>
            <div>
                <h4 class="text-md font-bold">Trend Analysis AI Insight</h4>
                <p class="text-sm text-gray-200 mt-1">
                    Demand for <span class="underline font-medium">Ceremonial Matcha</span> is expected to rise by <span
                        class="font-bold text-emerald-300">15% next week</span>. Recommend increasing base order of milk by
                    2 cases.
                </p>
            </div>
        </div>

    </div>
@endsection
