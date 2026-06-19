<div id="editModal"
    class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center backdrop-blur-sm transition-opacity">

    <div id="editModalContent"
        class="bg-white rounded-2xl shadow-xl w-full max-w-4xl mx-4 overflow-hidden transform transition-all scale-95 opacity-0">

        <div class="flex justify-between items-center px-8 py-5 border-b border-gray-100">
            <h2 class="text-xl font-bold text-gray-800">Edit Bahan Baku</h2>
            <button type="button" onclick="closeEditModal()"
                class="text-gray-400 hover:text-gray-700 transition-colors bg-gray-50 hover:bg-gray-100 p-2 rounded-full">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>

        <form id="editForm" action="{{ route('inventory.update', 0) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-10">

                <div>
                    <h3 class="text-sm font-bold text-gray-800 mb-5 uppercase tracking-wider">Informasi Bahan Baku</h3>

                    <div class="mb-5">
                        <label for="edit_nama_bahan" class="block text-sm font-semibold text-gray-700 mb-1.5">Nama
                            Bahan</label>
                        <input type="text" name="nama_bahan" id="edit_nama_bahan" value="{{ old('nama_bahan') }}"
                            class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#365E3F] focus:border-[#365E3F] outline-none transition-all placeholder-gray-400"
                            placeholder="Contoh: Bubuk Matcha Premium" required>
                        @error('nama_bahan')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-5">
                        <label for="edit_kategori"
                            class="block text-sm font-semibold text-gray-700 mb-1.5">Kategori</label>
                        <div class="relative">
                            <select name="kategori" id="edit_kategori"
                                class="w-full px-4 py-2.5 text-sm border border-[#E8E1D9] bg-[#FAF7F2] text-gray-800 rounded-lg focus:ring-2 focus:ring-[#365E3F] outline-none transition-all appearance-none cursor-pointer"
                                required>
                                <option value="" disabled selected>Pilih Kategori...</option>
                                <option value="Bubuk">Bubuk</option>
                                <option value="Cair">Cair</option>
                                <option value="Sirup">Sirup</option>
                                <option value="Toping">Toping</option>
                            </select>
                            <svg class="absolute right-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-500 pointer-events-none"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                        @error('kategori')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="edit_satuan" class="block text-sm font-semibold text-gray-700 mb-1.5">Satuan</label>
                        <div class="relative">
                            <select name="satuan" id="edit_satuan"
                                class="w-full px-4 py-2.5 text-sm border border-gray-200 bg-white text-gray-800 rounded-lg focus:ring-2 focus:ring-[#365E3F] outline-none transition-all appearance-none cursor-pointer"
                                required>
                                <option value="" disabled {{ old('satuan') ? '' : 'selected' }}>Pilih Satuan...
                                </option>
                                <option value="Gram" {{ old('satuan') == 'Gram' ? 'selected' : '' }}>Gram (g)</option>
                                <option value="Kg" {{ old('satuan') == 'Kg' ? 'selected' : '' }}>Kilogram (Kg)
                                </option>
                                <option value="Mililiter" {{ old('satuan') == 'Mililiter' ? 'selected' : '' }}>Mililiter
                                    (ml)</option>
                                <option value="Liter" {{ old('satuan') == 'Liter' ? 'selected' : '' }}>Liter (L)
                                </option>
                                <option value="Pcs" {{ old('satuan') == 'Pcs' ? 'selected' : '' }}>Pieces (Pcs)
                                </option>
                                <option value="Pack" {{ old('satuan') == 'Pack' ? 'selected' : '' }}>Pack</option>
                            </select>
                            <svg class="absolute right-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-500 pointer-events-none"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                        @error('satuan')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="relative">
                    <div class="hidden md:block absolute -left-5 top-0 bottom-0 w-px bg-gray-100"></div>

                    <h3 class="text-sm font-bold text-gray-800 mb-5 uppercase tracking-wider">Manajemen Stok</h3>

                    <div class="grid grid-cols-2 gap-5 mb-5">
                        <div>
                            <label for="edit_stok_awal" class="block text-sm font-semibold text-gray-700 mb-1.5">Stok
                                Awal</label>
                            <input type="number" name="stok_awal" id="edit_stok_awal" value="{{ old('stok_awal') }}"
                                min="0"
                                class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#365E3F] focus:border-[#365E3F] outline-none transition-all placeholder-gray-400"
                                placeholder="0" required>
                            @error('stok_awal')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="edit_stok_saat_ini"
                                class="block text-sm font-semibold text-gray-700 mb-1.5">Stok
                                Saat Ini</label>
                            <input type="number" name="stok_saat_ini" id="edit_stok_saat_ini"
                                value="{{ old('stok_saat_ini') }}" min="0"
                                class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#365E3F] focus:border-[#365E3F] outline-none transition-all placeholder-gray-400"
                                placeholder="0" required>
                            @error('stok_saat_ini')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="edit_stok_minimum" class="block text-sm font-semibold text-gray-700 mb-1.5">Batas
                            Limit
                            (Stok Minimum)</label>
                        <input type="number" name="stok_minimum" id="edit_stok_minimum"
                            value="{{ old('stok_minimum') }}" min="0"
                            class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#365E3F] focus:border-[#365E3F] outline-none transition-all placeholder-gray-400"
                            placeholder="Contoh: 20" required>
                        <p class="text-xs text-gray-500 mt-2">Sistem akan memberikan peringatan jika stok mencapai
                            batas
                            ini.</p>
                        @error('stok_minimum')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

            </div>

            <div
                class="px-8 py-5 bg-gray-50 flex items-center justify-end gap-3 border-t border-gray-100 rounded-b-2xl">
                <button type="button" onclick="closeEditModal()"
                    class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-900 transition-colors">
                    Batal
                </button>
                <button type="submit"
                    class="px-5 py-2.5 text-sm font-semibold text-white bg-[#365E3F] rounded-lg hover:bg-[#2a4a31] transition-colors shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                        </path>
                    </svg>
                    Update Bahan Baku
                </button>
            </div>
        </form>
    </div>
</div>
