@extends('layouts.app')

@section('content')
    <div class="p-8 max-w-2xl mx-auto">
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('inventory.index') }}" class="text-gray-400 hover:text-gray-700 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Tambah Bahan Baku</h1>
                <p class="text-sm text-gray-500 mt-1">Masukkan data bahan baku baru ke dalam sistem inventori.</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden p-6">
            <form action="{{ route('inventory.store') }}" method="POST">
                @csrf

                <div class="space-y-6">
                    <div>
                        <label for="nama_bahan" class="block text-sm font-medium text-gray-700 mb-1">Nama Bahan</label>
                        <input type="text" name="nama_bahan" id="nama_bahan" value="{{ old('nama_bahan') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1b3e2b] focus:border-[#1b3e2b] outline-none transition-all"
                            placeholder="Contoh: Bubuk Matcha Premium" required>
                        @error('nama_bahan')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="satuan" class="block text-sm font-medium text-gray-700 mb-1">Satuan</label>
                        <input type="text" name="satuan" id="satuan" value="{{ old('satuan') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1b3e2b] focus:border-[#1b3e2b] outline-none transition-all"
                            placeholder="Contoh: Gram, Kg, Liter, Pcs" required>
                        @error('satuan')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="stok_awal" class="block text-sm font-medium text-gray-700 mb-1">Stok Awal</label>
                            <input type="number" name="stok_awal" id="stok_awal" value="{{ old('stok_awal') }}" min="0"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1b3e2b] focus:border-[#1b3e2b] outline-none transition-all"
                                required>
                            @error('stok_awal')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="stok_saat_ini" class="block text-sm font-medium text-gray-700 mb-1">Stok Saat Ini</label>
                            <input type="number" name="stok_saat_ini" id="stok_saat_ini" value="{{ old('stok_saat_ini') }}" min="0"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1b3e2b] focus:border-[#1b3e2b] outline-none transition-all"
                                required>
                            @error('stok_saat_ini')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                    <a href="{{ route('inventory.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-[#1b3e2b] rounded-lg hover:bg-[#142e20] transition-colors shadow-sm">
                        Simpan Bahan Baku
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
