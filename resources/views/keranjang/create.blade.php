<div id="modalTambahProduk"
    class="fixed inset-0 z-[100] hidden flex items-start justify-center pt-20 bg-black/50 backdrop-blur-sm transition-opacity">
    <div class="w-full max-w-140 overflow-hidden rounded-2xl bg-white shadow-2xl">
        <div class="bg-dark-matcha px-5 py-4 flex items-start justify-between">
            <div>
                <h3 class="text-lg font-semibold text-white">Produk Baru</h3>
                <p class="mt-0.5 text-sm text-white/80">Tambahkan item baru ke katalog keranjang Matchaboy</p>
            </div>
            <button type="button" onclick="tutupModalProduk()" class="text-white/90 hover:text-white transition-opacity">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form id="formProdukBaru" onsubmit="simpanProdukBaru(event)" class="p-5 space-y-4 max-h-[75vh] overflow-y-auto">
            <div>
                <label for="item_name" class="mb-1 block text-sm font-medium text-gray-500">Item Name</label>
                <input type="text" id="item_name" required placeholder="Matcha Latte....."
                    class="w-full rounded-md border-0 bg-[#F6F4EE] px-4 py-2.5 text-gray-900 placeholder-gray-400 shadow-sm ring-1 ring-transparent transition focus:outline-none focus:ring-2 focus:ring-[#8FA88B]">
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label for="item_category" class="mb-1 block text-sm font-medium text-gray-500">Kategori</label>
                    <select id="item_category"
                        class="w-full rounded-md border-0 bg-[#F6F4EE] px-4 py-2.5 text-gray-900 shadow-sm ring-1 ring-transparent transition focus:outline-none focus:ring-2 focus:ring-[#8FA88B]">
                        <option value="">Select category</option>
                        <option value="Signature">Signature</option>
                        <option value="Milk Based">Milk Based</option>
                        <option value="Strawberry">Strawberry</option>
                    </select>
                </div>

                <div>
                    <label for="item_price" class="mb-1 block text-sm font-medium text-gray-500">Harga</label>
                    <input type="number" id="item_price" required placeholder="Rp ....."
                        class="w-full rounded-md border-0 bg-[#F6F4EE] px-4 py-2.5 text-gray-900 placeholder-gray-400 shadow-sm ring-1 ring-transparent transition focus:outline-none focus:ring-2 focus:ring-[#8FA88B]">
                </div>
            </div>

            <!-- ========================================================================= -->
            <!-- SECTION INPUT RESEP DINAMIS (Koneksi Produk ke Gudang) -->
            <!-- ========================================================================= -->
            <div class="border-t border-dashed border-gray-200 pt-3">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-medium text-gray-500">Resep / Kebutuhan Bahan Baku</label>
                    <button type="button" id="add-ingredient-btn"
                        class="text-xs font-semibold text-[#8FA88B] hover:text-[#3B5B43] transition">
                        + Tambah Bahan
                    </button>
                </div>

                <div id="resep-container" class="space-y-2">
                    <!-- Baris Default Pertama -->
                    <div class="flex gap-2 items-center resep-row bg-[#FBFAF6] p-2 rounded-lg ring-1 ring-gray-100">
                        <select name="ingredients[0][bahan_baku_id]"
                            class="flex-1 rounded-md border-0 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm ring-1 ring-gray-200 focus:outline-none focus:ring-2 focus:ring-[#8FA88B]"
                            required>
                            <option value="">-- Pilih Bahan --</option>
                            @foreach ($all_ingredients as $bahan)
                                <option value="{{ $bahan->id }}">{{ $bahan->nama_bahan }} ({{ $bahan->satuan }})
                                </option>
                            @endforeach
                        </select>
                        <input type="number" name="ingredients[0][quantity_needed]" step="0.01" min="0.01"
                            required placeholder="Total"
                            class="w-24 rounded-md border-0 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm ring-1 ring-gray-200 focus:outline-none focus:ring-2 focus:ring-[#8FA88B]">
                        <button type="button"
                            class="text-red-500 hover:text-red-700 text-sm font-medium px-2 py-1 remove-resep-btn">
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
            <!-- ========================================================================= -->

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-500">Gambar Produk</label>
                <div
                    class="rounded-xl border-2 border-dashed border-gray-200 bg-[#FBFAF6] px-4 py-4 text-center transition hover:border-[#8FA88B] hover:bg-[#FAF8F1]">
                    <label for="item_image"
                        class="flex cursor-pointer flex-col items-center justify-center gap-1 text-center">
                        <span class="text-sm font-semibold text-gray-700">Choose File</span>
                        <span id="item_image_name" class="text-xs text-gray-400">No file chosen</span>
                        <span class="text-xs text-gray-400">PNG, JPG, atau JPEG (max. 5MB)</span>
                    </label>
                    <input type="file" id="item_image" accept="image/*" class="hidden"
                        onchange="updateNamaFileProduk(this)">
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-3">
                <button type="button" onclick="tutupModalProduk()"
                    class="rounded-md px-5 py-2.5 text-sm font-medium  transition bg-white hover:bg-warning-bold">
                    Batal
                </button>
                <button type="submit"
                    class="inline-flex items-center justify-center rounded-md px-6 py-2.5 text-sm font-semibold shadow-sm transition bg-dark-matcha hover:opacity-90 hover:bg-soft-matcha"
                    style="min-width: 160px;  color: #ffffff; border: 1px solid #3B5B43;">
                    Simpan Produk
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function tampilModalProduk() {
        const modal = document.getElementById('modalTambahProduk');
        if (!modal) return;

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function tutupModalProduk() {
        const modal = document.getElementById('modalTambahProduk');
        if (!modal) return;

        modal.classList.add('hidden');
        modal.classList.remove('flex');

        // Reset kontainer resep kembali ke 1 baris kosong saat modal ditutup
        const container = document.getElementById('resep-container');
        if (container) {
            container.innerHTML = `
                <div class="flex gap-2 items-center resep-row bg-[#FBFAF6] p-2 rounded-lg ring-1 ring-gray-100">
                    <select name="ingredients[0][bahan_baku_id]" class="flex-1 rounded-md border-0 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm ring-1 ring-gray-200 focus:outline-none focus:ring-2 focus:ring-[#8FA88B]" required>
                        <option value="">-- Pilih Bahan --</option>
                        @foreach ($all_ingredients as $bahan)
                            <option value="{{ $bahan->id }}">{{ $bahan->nama_bahan }} ({{ $bahan->satuan }})</option>
                        @endforeach
                    </select>
                    <input type="number" name="ingredients[0][quantity_needed]" step="0.01" min="0.01" required placeholder="Total"
                        class="w-24 rounded-md border-0 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm ring-1 ring-gray-200 focus:outline-none focus:ring-2 focus:ring-[#8FA88B]">
                    <button type="button" class="text-red-500 hover:text-red-700 text-sm font-medium px-2 py-1 remove-resep-btn">Hapus</button>
                </div>
            `;
        }
    }

    function updateNamaFileProduk(input) {
        const label = document.getElementById('item_image_name');
        if (!label) return;

        label.textContent = input.files && input.files.length > 0 ? input.files[0].name : 'No file chosen';
    }

    // Handle penambahan baris resep dinamis
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('resep-container');
        const addBtn = document.getElementById('add-ingredient-btn');
        let rowIndex = 1;

        if (addBtn && container) {
            addBtn.addEventListener('click', function() {
                const newRow = document.createElement('div');
                newRow.className =
                    'flex gap-2 items-center resep-row bg-[#FBFAF6] p-2 rounded-lg ring-1 ring-gray-100 mt-2';
                newRow.innerHTML = `
                    <select name="ingredients[${rowIndex}][bahan_baku_id]" class="flex-1 rounded-md border-0 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm ring-1 ring-gray-200 focus:outline-none focus:ring-2 focus:ring-[#8FA88B]" required>
                        <option value="">-- Pilih Bahan --</option>
                        @foreach ($all_ingredients as $bahan)
                            <option value="{{ $bahan->id }}">{{ $bahan->nama_bahan }} ({{ $bahan->satuan }})</option>
                        @endforeach
                    </select>
                    <input type="number" name="ingredients[${rowIndex}][quantity_needed]" step="0.01" min="0.01" required placeholder="Total"
                        class="w-24 rounded-md border-0 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm ring-1 ring-gray-200 focus:outline-none focus:ring-2 focus:ring-[#8FA88B]">
                    <button type="button" class="text-red-500 hover:text-red-700 text-sm font-medium px-2 py-1 remove-resep-btn">Hapus</button>
                `;
                container.appendChild(newRow);
                rowIndex++;
            });

            // Hapus baris resep dinamis
            container.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-resep-btn')) {
                    const row = e.target.closest('.resep-row');
                    if (container.querySelectorAll('.resep-row').length > 1) {
                        row.remove();
                    } else {
                        alert('Setiap produk minimal wajib menyantumkan 1 resep bahan baku.');
                    }
                }
            });
        }
    });
</script>
