@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-100">
    @include('partials.sidebar')

    <div class="flex-1 flex flex-col overflow-hidden">
        <div class="bg-green-700 text-white px-8 py-4 flex items-center justify-between shadow">
            <div class="flex items-center gap-4">
                <span class="text-2xl font-bold">Laporan</span>
            </div>
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center">
                    <x-icon name="user-circle" size="md" class="w-6 h-6 stroke-current text-green-700" />
                </div>
                <div>
                    <p class="font-semibold">Bu Tini</p>
                    <p class="text-xs opacity-90">Admin</p>
                </div>
            </div>
        </div>

        <div class="flex-1 overflow-auto">
            <div class="p-8">
                <h1 class="text-2xl font-semibold mb-4">Laporan</h1>
                <p class="text-sm text-gray-600">Ringkasan laporan penjualan, stok, dan operasional akan muncul di sini.</p>
            </div>
        </div>
    </div>
</div>
@endsection
