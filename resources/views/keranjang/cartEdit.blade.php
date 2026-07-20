@if (auth()->user() && auth()->user()->role === 'admin')
    <div id="modalEditProduk"
        class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/40 backdrop-blur-sm p-4 transition-opacity">

        <div class="absolute inset-0" onclick="tutupModalEdit()"></div>

        <!-- Modal Container -->
        <div
            class="w-full max-w-lg overflow-hidden rounded-[20px] bg-white shadow-2xl relative z-10 flex flex-col max-h-[95vh]">

            <!-- Header -->
            <div class="bg-[#84A07F] px-6 py-5 flex items-start justify-between shrink-0">
                <div>
                    <h3 class="text-[17px] font-semibold text-white">Edit Produk</h3>
                    <p class="mt-0.5 text-[13px] text-white/80">Perbarui data komponen katalog produk Matchaboy</p>
                </div>
                <button type="button" onclick="tutupModalEdit()"
                    class="text-white/80 hover:text-white transition-opacity mt-1">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Form Body -->
            <form id="formEditProduk" onsubmit="simpanPerubahanProduk(event)" class="overflow-y-auto p-6 space-y-5">
                @csrf
                <!-- Name perlu diset jika pakai form biasa, tapi karena AJAX, id cukup -->
                <input type="hidden" id="edit_item_id" name="id">

                <!-- Item Name -->
                <div>
                    <label for="edit_item_name" class="mb-1.5 block text-[13px] font-medium text-gray-600">Item
                        Name</label>
                    <input type="text" id="edit_item_name" name="name" required placeholder="Matcha Latte....."
                        class="w-full rounded-xl border-0 bg-[#F6F4EE] px-4 py-3 text-sm text-gray-900 placeholder-gray-400 shadow-sm ring-1 ring-transparent transition focus:outline-none focus:ring-2 focus:ring-[#8FA88B]">
                </div>

                <!-- Grid Kategori & Harga -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="edit_item_category"
                            class="mb-1.5 block text-[13px] font-medium text-gray-600">Kategori</label>
                        <div class="relative">
                            <select id="edit_item_category" name="category" required
                                class="w-full appearance-none rounded-xl border-0 bg-[#F6F4EE] px-4 py-3 text-sm text-gray-900 shadow-sm ring-1 ring-transparent transition focus:outline-none focus:ring-2 focus:ring-[#8FA88B]">
                                <option value="">Select category</option>
                                <option value="Signature">Signature</option>
                                <option value="Milk Based">Milk Based</option>
                                <option value="Strawberry">Strawberry</option>
                            </select>
                            <div
                                class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="edit_item_price"
                            class="mb-1.5 block text-[13px] font-medium text-gray-600">Harga</label>
                        <input type="number" id="edit_item_price" name="price" required placeholder="Rp ......"
                            class="w-full rounded-xl border-0 bg-[#F6F4EE] px-4 py-3 text-sm text-gray-900 placeholder-gray-400 shadow-sm ring-1 ring-transparent transition focus:outline-none focus:ring-2 focus:ring-[#8FA88B]">
                    </div>
                </div>

                <!-- SECTION EDIT RESEP DINAMIS -->
                <div class="border-t border-dashed border-gray-200 pt-4">
                    <div class="flex items-center justify-between mb-3">
                        <label class="block text-[13px] font-medium text-gray-600">Kebutuhan Bahan Baku</label>
                        <button type="button" id="edit-add-ingredient-btn"
                            class="text-xs font-semibold text-[#6A8466] hover:text-[#3B5B43] transition bg-[#F6F4EE] px-3 py-1 rounded-full">
                            + Tambah Bahan
                        </button>
                    </div>

                    <div id="edit-resep-container" class="space-y-2">
                        <!-- Baris resep akan dirender secara dinamis oleh JavaScript -->
                        <div class="text-center py-4 text-xs text-gray-400 bg-[#F6F4EE] rounded-xl border border-dashed border-gray-300"
                            id="edit-resep-loading">
                            Memuat data resep dari database...
                        </div>
                    </div>
                </div>

                <!-- Gambar Produk -->
                <div>
                    <label class="mb-1.5 block text-[13px] font-medium text-gray-600">Gambar Produk (Opsional)</label>
                    <div
                        class="rounded-[20px] border-2 border-dashed border-[#D6E0D4] bg-[#FDFDFD] px-4 py-8 text-center transition-colors hover:border-[#8FA88B] hover:bg-[#F9FBF8]">
                        <label for="edit_item_image"
                            class="flex cursor-pointer flex-col items-center justify-center gap-2">
                            <div class="rounded-full bg-[#EDF2EB] p-3 text-[#6A8466] mb-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                                </svg>
                            </div>
                            <span class="text-[14px] font-semibold text-gray-800">Upload Gambar Baru</span>
                            <span class="text-[11px] text-gray-400">SVG, PNG, JPG or GIF (max. 5MB)</span>
                            <span id="edit_item_image_name"
                                class="text-[12px] text-[#6A8466] font-medium mt-2 bg-[#F6F4EE] px-3 py-1 rounded-full hidden"></span>
                        </label>
                        <input type="file" name="image" id="edit_item_image" accept="image/*" class="hidden"
                            onchange="updateNamaFileEdit(this)">
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-4 pt-2">
                    <button type="button" onclick="tutupModalEdit()"
                        class="text-[14px] font-medium text-gray-500 hover:text-gray-800 transition px-2">
                        Batal
                    </button>
                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-xl px-8 py-3 text-[14px] font-semibold text-white transition bg-[#415C3E] hover:bg-[#344b32] shadow-md hover:shadow-lg"
                        style="min-width: 160px;">
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
                nama: "{{ addslashes($bahan->nama_bahan) }}",
                satuan: "{{ addslashes($bahan->satuan) }}"
            },
        @endforeach
    ];

    /**
     * FUNGSI UTAMA: Menerima data dari tombol Edit dan menyuntikkannya ke form
     */
    function bukaModalEdit(button) {
        // 1. Ambil data dari atribut HTML tombol
        const id = button.getAttribute('data-id');
        const name = button.getAttribute('data-name');
        const category = button.getAttribute('data-category');
        const price = button.getAttribute('data-price');
        const ingredientsData = button.getAttribute('data-ingredients');

        // 2. Isi value form dasar
        document.getElementById('edit_item_id').value = id;
        document.getElementById('edit_item_name').value = name;
        document.getElementById('edit_item_category').value = category;
        document.getElementById('edit_item_price').value = price;

        // 3. Render ulang daftar bahan baku
        const container = document.getElementById('edit-resep-container');
        container.innerHTML = ''; // Kosongkan form resep sebelumnya
        editRowIndex = 0;

        try {
            const ingredients = JSON.parse(ingredientsData);

            if (ingredients && ingredients.length > 0) {
                ingredients.forEach(item => {
                    // Pastikan key object (bahan_baku_id / quantity_needed) sesuai dengan return JSON dari API/Controller lu
                    const row = buatBarisResepEdit(item.bahan_baku_id, item.qty_dibutuhkan || item
                        .quantity_needed);
                    container.appendChild(row);
                });
            } else {
                container.innerHTML =
                    '<div class="text-center py-4 text-xs text-gray-400 bg-[#F6F4EE] rounded-xl border border-dashed border-gray-300" id="edit-resep-loading">Belum ada resep terdaftar untuk produk ini.</div>';
            }
        } catch (error) {
            console.error("Gagal mem-parsing data resep: ", error);
            container.innerHTML =
                '<div class="text-center py-4 text-xs text-red-500 bg-red-50 rounded-xl">Terjadi kesalahan memuat resep.</div>';
        }

        // 4. Buka Modal
        const modal = document.getElementById('modalEditProduk');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function tutupModalEdit() {
        const modal = document.getElementById('modalEditProduk');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    function updateNamaFileEdit(input) {
        const label = document.getElementById('edit_item_image_name');
        if (!label) return;

        if (input.files && input.files.length > 0) {
            label.textContent = input.files[0].name;
            label.classList.remove('hidden');
        } else {
            label.textContent = '';
            label.classList.add('hidden');
        }
    }

    function buatBarisResepEdit(selectedBahanId = '', quantityNeeded = '') {
        let optionsHtml = '<option value="">-- Pilih Bahan --</option>';
        opsiBahanBaku.forEach(bahan => {
            const isSelected = String(bahan.id) === String(selectedBahanId) ? 'selected' : '';
            optionsHtml += `<option value="${bahan.id}" ${isSelected}>${bahan.nama} (${bahan.satuan})</option>`;
        });

        const row = document.createElement('div');
        row.className = 'flex gap-2 items-center edit-resep-row bg-white p-1 mt-2';
        row.innerHTML = `
            <select name="ingredients[${editRowIndex}][bahan_baku_id]" class="flex-1 rounded-lg border-0 bg-[#F6F4EE] px-3 py-2.5 text-[13px] text-gray-900 focus:ring-2 focus:ring-[#8FA88B]" required>
                ${optionsHtml}
            </select>
            <input type="number" name="ingredients[${editRowIndex}][quantity_needed]" step="0.01" min="0.01" required placeholder="Qty" value="${quantityNeeded}"
                class="w-20 rounded-lg border-0 bg-[#F6F4EE] px-3 py-2.5 text-[13px] text-gray-900 focus:ring-2 focus:ring-[#8FA88B] text-center">
            <button type="button" class="text-gray-400 hover:text-red-500 transition px-1 remove-edit-resep-btn" title="Hapus">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
            </button>
        `;
        editRowIndex++;
        return row;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('edit-resep-container');
        const addBtn = document.getElementById('edit-add-ingredient-btn');

        if (addBtn && container) {
            addBtn.addEventListener('click', function() {
                const loadingMsg = document.getElementById('edit-resep-loading');
                if (loadingMsg) loadingMsg.remove();
                container.appendChild(buatBarisResepEdit());
            });

            container.addEventListener('click', function(e) {
                if (e.target.closest('.remove-edit-resep-btn')) {
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
