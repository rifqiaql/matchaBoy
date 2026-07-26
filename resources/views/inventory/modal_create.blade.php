<div id="addModal"
    class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center backdrop-blur-sm transition-opacity">

    <div id="modalContent"
        class="bg-white rounded-2xl shadow-xl w-full max-w-4xl mx-4 overflow-hidden transform transition-all scale-95 opacity-0">

        <!-- HEADER DIREVISI: Menggunakan warna hijau standar Matchaboy -->
        <div class="flex justify-between items-center px-8 py-5 bg-[#84A07F]">
            <h2 class="text-xl font-bold text-white">Tambah Bahan Baku</h2>
            <button type="button" onclick="closeModal()"
                class="text-white/80 hover:text-white transition-colors bg-transparent hover:bg-white/10 p-2 rounded-full mt-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>

        <form action="{{ route('inventory.store') }}" method="POST">
            @csrf

            <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-10">

                <div>
                    <h3 class="text-sm font-bold text-gray-800 mb-5 uppercase tracking-wider">Informasi Bahan Baku</h3>

                    <div class="mb-5">
                        <label for="nama_bahan" class="block text-sm font-semibold text-gray-700 mb-1.5">Nama
                            Bahan</label>
                        <input type="text" name="nama_bahan" id="nama_bahan" value="{{ old('nama_bahan') }}"
                            class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#365E3F] focus:border-[#365E3F] outline-none transition-all placeholder-gray-400"
                            placeholder="Contoh: Bubuk Matcha Premium" required>
                        @error('nama_bahan')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-5">
                        <label for="kategori" class="block text-sm font-semibold text-gray-700 mb-1.5">Kategori</label>
                        <div class="relative">
                            <!-- KATEGORI DIREVISI: UI disamakan (bg-white) -->
                            <select name="kategori" id="kategori"
                                class="w-full px-4 py-2.5 text-sm border border-gray-200 bg-white text-gray-800 rounded-lg focus:ring-2 focus:ring-[#365E3F] focus:border-[#365E3F] outline-none transition-all appearance-none cursor-pointer"
                                required>
                                <option value="" disabled {{ old('kategori') ? '' : 'selected' }}>Pilih
                                    Kategori...</option>
                                <option value="Bubuk" {{ old('kategori') == 'Bubuk' ? 'selected' : '' }}>Bubuk</option>
                                <option value="Cair" {{ old('kategori') == 'Cair' ? 'selected' : '' }}>Cair</option>
                                <option value="Sirup" {{ old('kategori') == 'Sirup' ? 'selected' : '' }}>Sirup</option>
                                <option value="Toping" {{ old('kategori') == 'Toping' ? 'selected' : '' }}>Toping
                                </option>
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
                        <label for="satuan" class="block text-sm font-semibold text-gray-700 mb-1.5">Satuan
                            Dasar</label>
                        <div class="relative">
                            <select name="satuan" id="satuan"
                                class="w-full px-4 py-2.5 text-sm border border-gray-200 bg-white text-gray-800 rounded-lg focus:ring-2 focus:ring-[#365E3F] focus:border-[#365E3F] outline-none transition-all appearance-none cursor-pointer"
                                required>
                                <option value="" disabled {{ old('satuan') ? '' : 'selected' }}>Pilih Satuan...
                                </option>
                                <option value="Gram" {{ old('satuan') == 'Gram' ? 'selected' : '' }}>Gram (g)
                                </option>
                                <option value="Mililiter" {{ old('satuan') == 'Mililiter' ? 'selected' : '' }}>
                                    Mililiter (ml)</option>
                                <option value="Pcs" {{ old('satuan') == 'Pcs' ? 'selected' : '' }}>Pieces (Pcs)
                                </option>
                            </select>
                            <svg class="absolute right-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-500 pointer-events-none"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Pilih satuan terkecil (Base Unit) untuk resep.</p>
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
                            <label for="jumlah_kemasan" class="block text-sm font-semibold text-gray-700 mb-1.5">Jumlah
                                Kemasan</label>
                            <input type="number" name="jumlah_kemasan" id="jumlah_kemasan"
                                value="{{ old('jumlah_kemasan') }}" min="0" step="0.01"
                                class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#365E3F] focus:border-[#365E3F] outline-none transition-all placeholder-gray-400"
                                placeholder="Contoh: 2" required>
                            @error('jumlah_kemasan')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="isi_per_kemasan" class="block text-sm font-semibold text-gray-700 mb-1.5">Isi
                                per Kemasan</label>
                            <input type="number" name="isi_per_kemasan" id="isi_per_kemasan"
                                value="{{ old('isi_per_kemasan') }}" min="0" step="0.01"
                                class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#365E3F] focus:border-[#365E3F] outline-none transition-all placeholder-gray-400"
                                placeholder="Contoh: 950" required>
                            @error('isi_per_kemasan')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- REVISI NOTE BOX: Warna biru diubah jadi hijau beserta tambahan ikon informatif -->
                    <div class="bg-green-50 border border-green-100 rounded-lg p-3 mb-5 flex items-start gap-2">
                        <svg class="w-4 h-4 text-green-600 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-xs text-green-800 font-medium leading-relaxed">Sistem otomatis mengalikan
                            (Jumlah × Isi) untuk disimpan sebagai Total Stok.</p>
                    </div>

                    <div>
                        <label for="stok_minimum" class="block text-sm font-semibold text-gray-700 mb-1.5">Batas Limit
                            (Stok Minimum)</label>
                        <input type="number" name="stok_minimum" id="stok_minimum"
                            value="{{ old('stok_minimum') }}" min="0"
                            class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#365E3F] focus:border-[#365E3F] outline-none transition-all placeholder-gray-400"
                            placeholder="Contoh: 200" required>
                        <p class="text-xs text-gray-500 mt-2">Peringatan muncul jika total stok (dalam satuan dasar)
                            mencapai batas ini.</p>
                        @error('stok_minimum')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

            </div>

            <div
                class="px-8 py-5 bg-gray-50 flex items-center justify-end gap-3 border-t border-gray-100 rounded-b-2xl">
                <button type="button" onclick="closeModal()"
                    class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-900 transition-colors">
                    Batal
                </button>
                <button type="submit"
                    class="px-5 py-2.5 text-sm font-semibold text-white bg-[#365E3F] rounded-lg hover:bg-[#2a4a31] transition-colors shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                        </path>
                    </svg>
                    Simpan Bahan Baku
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        window.openModal = function() {
            const modal = document.getElementById('addModal');
            const modalContent = document.getElementById('modalContent');

            if (modal && modalContent) {
                modal.classList.remove('hidden');
                setTimeout(() => {
                    modalContent.classList.remove('scale-95', 'opacity-0');
                    modalContent.classList.add('scale-100', 'opacity-100');
                }, 10);
            }
        };

        window.closeModal = function() {
            const modal = document.getElementById('addModal');
            const modalContent = document.getElementById('modalContent');

            if (modal && modalContent) {
                modalContent.classList.remove('scale-100', 'opacity-100');
                modalContent.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 200);
            }
        };

        @if ($errors->any())
            window.openModal();
        @endif
    });
</script>
