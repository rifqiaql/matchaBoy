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

        <form id="formProdukBaru" onsubmit="simpanProdukBaru(event)" class="p-5 space-y-4">
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
    }

    function updateNamaFileProduk(input) {
        const label = document.getElementById('item_image_name');
        if (!label) return;

        label.textContent = input.files && input.files.length > 0 ? input.files[0].name : 'No file chosen';
    }
</script>
