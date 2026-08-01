@extends('layouts.app')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">Riwayat Transaksi Harian</h1>
    </div>

    <!-- Alert Notifikasi -->
    @if(session('success'))
        <div class="mb-4 p-4 text-green-700 bg-green-50 border border-green-200 rounded-lg font-medium flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-4 text-red-700 bg-red-50 border border-red-200 rounded-lg font-medium flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- Container Utama -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        
        <!-- Filter Section -->
        <div class="p-5 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
            <div class="text-sm text-gray-500 font-medium">
                Menampilkan data historis untuk validasi operasional
            </div>
            <form action="{{ route('riwayat.index') }}" method="GET" class="flex items-center gap-3">
                <select name="month" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-[#86A789] focus:border-[#86A789] text-sm outline-none">
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ sprintf('%02d', $i) }}" {{ $month == sprintf('%02d', $i) ? 'selected' : '' }}>
                            Bulan {{ $i }}
                        </option>
                    @endfor
                </select>
                
                <select name="year" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-[#86A789] focus:border-[#86A789] text-sm outline-none">
                    @for($y = 2024; $y <= date('Y'); $y++)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
                
                <button type="submit" class="px-5 py-2 bg-[#86A789] hover:bg-[#739276] text-white text-sm font-semibold rounded-lg transition-colors">
                    Filter Data
                </button>
            </form>
        </div>

        <!-- Table Section -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                        <th class="px-6 py-4 font-semibold">No</th>
                        <th class="px-6 py-4 font-semibold">Waktu Transaksi</th>
                        <th class="px-6 py-4 font-semibold">No. Invoice</th>
                        <th class="px-6 py-4 font-semibold">Kasir</th>
                        <th class="px-6 py-4 font-semibold">Total Pembayaran</th>
                        <th class="px-6 py-4 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($transactions as $index => $t)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $transactions->firstItem() + $index }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ \Carbon\Carbon::parse($t->created_at)->format('d M Y, H:i') }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $t->invoice_number }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $t->user->name ?? 'Kasir' }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-800">Rp {{ number_format($t->total_price, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm text-center">
                                <form action="{{ route('riwayat.void', $t->id) }}" method="POST" onsubmit="return confirm('PERINGATAN KERAS:\n\nYakin ingin membatalkan transaksi ini?\nData akan dihapus permanen dan STOK BAHAN BAKU akan dikembalikan ke gudang sesuai resep asal.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-2 px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-700 font-medium rounded-md transition-colors border border-red-200 focus:ring-2 focus:ring-red-200 outline-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Void Data
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                    <span>Tidak ada data transaksi untuk bulan ini.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- [TAMBAHKAN INI] Pagination Section -->
        @if($transactions->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-white">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection