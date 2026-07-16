@if (auth()->user() && auth()->user()->role === 'admin')
    <div id="modalEditProduk"
        class="fixed inset-0 z-50 hidden flex items-start justify-center pt-20 bg-black/50 backdrop-blur-sm p-4 animate-fade-in transition-opacity">

        <div class="absolute inset-0" onclick="tutupModalEdit()"></div>

        <div
            class="w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-2xl relative z-10 transform transition-all duration-300">

            <div class="bg-dark-matcha px-5 py-4 flex items-start justify-between">
                <div>
                    <h3 class="text-base font-semibold text-white">Edit Produk</h3>
                    <p class="mt-0.5 text-xs text-white/80">Perbarui data komponen katalog produk Matchaboy</p>
                </div>
                <button type="button" onclick="tutupModalEdit()"
                    class="text-white/90 hover:text-white transition-opacity focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="formEditProduk" onsubmit="simpanPerubahanProduk(event)"
                class="p-5 space-y-4 max-h-[75vh] overflow-y-auto">
                @csrf
                <input type="hidden" id="edit_item_id">

                <div>
                    <label for="edit_item_name" class="mb-1 block text-xs font-medium text-gray-500">Item Name</label>
                    <input type="text" id="edit_item_name" required placeholder="Matcha Latte....."
                        class="w-full rounded-xl border-0 bg-[#F6F4EE] px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#8FA88B]">
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="edit_item_category"
                            class="mb-1 block text-xs font-medium text-gray-500">Kategori</label>
                        <select id="edit_item_category" required
                            class="w-full rounded-xl border-0 bg-[#F6F4EE] px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#8FA88B]">
                            <option value="">Select category</option>
                            <option value="Signature">Signature</option>
                            <option value="Milk Based">Milk Based</option>
                            <option value="Strawberry">Strawberry</option>
                        </select>
                    </div>
                    <div>
                        <label for="edit_item_price" class="mb-1 block text-xs font-medium text-gray-500">Harga</label>
                        <input type="number" id="edit_item_price" required placeholder="Rp ....."
                            class="w-full rounded-xl border-0 bg-[#F6F4EE] px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#8FA88B]">
                    </div>
                </div>

                <!-- ========================================================================= -->
                <!-- SECTION EDIT RESEP DINAMIS -->
                <!-- ========================================================================= -->
                <div class="border-t border-dashed border-gray-200 pt-3">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-xs font-medium text-gray-500">Resep / Kebutuhan Bahan Baku</label>
                        <button type="button" id="edit-add-ingredient-btn"
                            class="text-[11px] font-semibold text-[#8FA88B] hover:text-[#3B5B43] transition">
                            + Tambah Bahan
                        </button>
                    </div>

                    <div id="edit-resep-container" class="space-y-2">
                        <!-- Baris resep akan dirender secara dinamis oleh JavaScript bawaan saat modal dibuka -->
                        <div class="text-center py-2 text-xs text-gray-400" id="edit-resep-loading">
                            Memuat data resep...
                        </div>
                    </div>
                </div>
                <!-- ========================================================================= -->

                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-500">Gambar Produk (Opsional)</label>
                    <div
                        class="rounded-xl border-2 border-dashed border-gray-200 bg-[#FBFAF6] p-4 text-center transition hover:border-[#8FA88B] hover:bg-[#FAF8F1]">
                        <label for="edit_item_image"
                            class="flex cursor-pointer flex-col items-center justify-center gap-1">
                            <span class="text-xs font-semibold text-gray-700">Choose File</span>
                            <span id="edit_item_image_name" class="text-[11px] text-gray-400 truncate max-w-xs">No file
                                chosen</span>
                            <span class="text-[10px] text-gray-400">PNG, JPG, atau JPEG (max. 5MB)</span>
                        </label>
                        <input type="file" id="edit_item_image" accept="image/*" class="hidden"
                            onchange="updateNamaFileEdit(this)">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-3 mt-3">
                    <button type="button" onclick="tutupModalEdit()"
                        class="rounded-xl px-4 py-2 text-xs font-medium border border-gray-200 bg-white hover:bg-gray-100 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="rounded-xl px-5 py-2 text-xs font-semibold text-white bg-dark-matcha hover:opacity-90 transition-opacity"
                        style="min-width: 120px;">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif

<script>
    let editRowIndex = 0;

    // Array opsi bahan baku dari PHP untuk disuntikkan ke baris baru dinamis di JavaScript
    const opsiBahanBaku = [
        @foreach ($all_ingredients as $bahan)
            {
                id: "{{ $bahan->id }}",
                nama: "{{ $bahan->nama_bahan }}",
                satuan: "{{ $bahan->satuan }}"
            },
        @endforeach
    ];

    function updateNamaFileEdit(input) {
        const label = document.getElementById('edit_item_image_name');
        if (!label) return;
        label.textContent = input.files && input.files.length > 0 ? input.files[0].name : 'No file chosen';
    }

    /**
     * Membuat sebaris elemen HTML pilihan resep dinamis
     */
    function buatBarisResepEdit(selectedBahanId = '', quantityNeeded = '') {
        let optionsHtml = '<option value="">-- Pilih Bahan --</option>';
        opsiBahanBaku.forEach(bahan => {
            const selected = String(bahan.id) === String(selectedBahanId) ? 'selected' : '';
            optionsHtml += `<option value="${bahan.id}" ${selected}>${bahan.nama} (${bahan.satuan})</option>`;
        });

        const row = document.createElement('div');
        row.className = 'flex gap-2 items-center edit-resep-row bg-[#FBFAF6] p-2 rounded-lg ring-1 ring-gray-100';
        row.innerHTML = `
            <select name="ingredients[${editRowIndex}][bahan_baku_id]" class="flex-1 rounded-md border-0 bg-white px-2 py-1.5 text-xs text-gray-900 shadow-sm ring-1 ring-gray-200 focus:outline-none focus:ring-2 focus:ring-[#8FA88B]" required>
                ${optionsHtml}
            </select>
            <input type="number" name="ingredients[${editRowIndex}][quantity_needed]" step="0.01" min="0.01" required placeholder="Total" value="${quantityNeeded}"
                class="w-20 rounded-md border-0 bg-white px-2 py-1.5 text-xs text-gray-900 shadow-sm ring-1 ring-gray-200 focus:outline-none focus:ring-2 focus:ring-[#8FA88B]">
            <button type="button" class="text-red-500 hover:text-red-700 text-xs font-medium px-1 py-0.5 remove-edit-resep-btn">Hapus</button>
        `;
        editRowIndex++;
        return row;
    }

    // Listener tombol tambah bahan baku di form edit
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('edit-resep-container');
        const addBtn = document.getElementById('edit-add-ingredient-btn');

        if (addBtn && container) {
            addBtn.addEventListener('click', function() {
                // Hapus tulisan "memuat" atau "resep kosong" jika ada
                const loadingMsg = document.getElementById('edit-resep-loading');
                if (loadingMsg) loadingMsg.remove();

                container.appendChild(buatBarisResepEdit());
            });

            // Hapus baris resep dinamis
            container.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-edit-resep-btn')) {
                    const row = e.target.closest('.edit-resep-row');
                    if (container.querySelectorAll('.edit-resep-row').length > 1) {
                        row.remove();
                    } else {
                        alert('Setiap produk minimal wajib menyantumkan 1 resep bahan baku.');
                    }
                }
            });
        }
    });
</script>
