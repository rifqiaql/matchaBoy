<div id="addModal"
    class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center backdrop-blur-sm transition-opacity">

    <div id="modalContent"
        class="bg-white rounded-xl shadow-lg w-full max-w-lg overflow-hidden transform transition-all scale-95 opacity-0">

        <div class="flex justify-between items-center p-6 border-b border-gray-100">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">Tambah Bahan Baku</h2>
                <p class="text-xs text-gray-500 mt-1">Masukkan data bahan baku baru.</p>
            </div>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>

        <form action="{{ route('inventory.store') }}" method="POST">
            @csrf
            <div class="p-6 space-y-4">
                <div>
                    <label for="nama_bahan" class="block text-sm font-medium text-gray-700 mb-1">Nama Bahan</label>
                    <input type="text" name="nama_bahan" id="nama_bahan" value="{{ old('nama_bahan') }}"
                        class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1b3e2b] outline-none"
                        placeholder="Contoh: Bubuk Matcha" required>
                    @error('nama_bahan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="satuan" class="block text-sm font-medium text-gray-700 mb-1">Satuan</label>
                    <input type="text" name="satuan" id="satuan" value="{{ old('satuan') }}"
                        class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1b3e2b] outline-none"
                        placeholder="Contoh: Gram, Kg, Pcs" required>
                    @error('satuan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="stok_awal" class="block text-sm font-medium text-gray-700 mb-1">Stok Awal</label>
                        <input type="number" name="stok_awal" id="stok_awal" value="{{ old('stok_awal') }}"
                            min="0"
                            class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1b3e2b] outline-none"
                            required>
                        @error('stok_awal')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="stok_saat_ini" class="block text-sm font-medium text-gray-700 mb-1">Stok Saat
                            Ini</label>
                        <input type="number" name="stok_saat_ini" id="stok_saat_ini" value="{{ old('stok_saat_ini') }}"
                            min="0"
                            class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1b3e2b] outline-none"
                            required>
                        @error('stok_saat_ini')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 flex items-center justify-end gap-3 border-t border-gray-100">
                <button type="button" onclick="closeModal()"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-[#1b3e2b] rounded-lg hover:bg-[#142e20] transition-colors shadow-sm">
                    Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Pastikan script hanya berjalan setelah seluruh halaman selesai dimuat
    document.addEventListener("DOMContentLoaded", function() {

        // Daftarkan fungsi ke 'window' agar bisa dibaca oleh atribut onclick HTML
        window.openModal = function() {
            const modal = document.getElementById('addModal');
            const modalContent = document.getElementById('modalContent');

            if (modal && modalContent) {
                modal.classList.remove('hidden');
                // Sedikit delay agar animasi Tailwind berjalan
                setTimeout(() => {
                    modalContent.classList.remove('scale-95', 'opacity-0');
                    modalContent.classList.add('scale-100', 'opacity-100');
                }, 10);
            } else {
                console.error("Elemen modal tidak ditemukan!");
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

        // Jika ada error validasi dari Laravel, otomatis buka modal
        @if ($errors->any())
            window.openModal();
        @endif

    });
</script>
