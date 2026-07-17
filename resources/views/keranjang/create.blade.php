<div id="modalTambahProduk" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">

    <!-- Backdrop: Dipisah warnanya ke sini, diberi ID dan status awal opacity-0 -->
    <div id="tambahBackdrop"
        class="absolute inset-0 bg-black/40 backdrop-blur-sm opacity-0 transition-opacity duration-300 ease-out"
        onclick="tutupModalProduk()"></div>

    <!-- Modal Container: Diberi ID dan status awal opacity-0 & scale-95 -->
    <div id="tambahModalContent"
        class="w-full max-w-lg overflow-hidden rounded-[20px] bg-white shadow-2xl relative z-10 flex flex-col max-h-[95vh] opacity-0 scale-95 transform transition-all duration-300 ease-out">

        <!-- Header -->
        <div class="bg-[#84A07F] px-6 py-5 flex items-start justify-between shrink-0">
            <div>
                <h3 class="text-[17px] font-semibold text-white">Produk Baru</h3>
                <p class="mt-0.5 text-[13px] text-white/80">Tambahkan item baru ke katalog inventaris Matchaboy</p>
            </div>
            <button type="button" onclick="tutupModalProduk()"
                class="text-white/80 hover:text-white transition-opacity mt-1">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Form Body -->
        <form id="formProdukBaru" onsubmit="simpanProdukBaru(event)" class="overflow-y-auto p-6 space-y-5">

            <!-- Item Name -->
            <div>
                <label for="item_name" class="mb-1.5 block text-[13px] font-medium text-gray-600">Item Name</label>
                <input type="text" id="item_name" required placeholder="Matcha Latte....."
                    class="w-full rounded-xl border-0 bg-[#F6F4EE] px-4 py-3 text-sm text-gray-900 placeholder-gray-400 shadow-sm ring-1 ring-transparent transition focus:outline-none focus:ring-2 focus:ring-[#8FA88B]">
            </div>

            <!-- Grid Kategori & Harga -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="item_category"
                        class="mb-1.5 block text-[13px] font-medium text-gray-600">Kategori</label>
                    <div class="relative">
                        <select id="item_category" required
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
                    <label for="item_price" class="mb-1.5 block text-[13px] font-medium text-gray-600">Harga</label>
                    <input type="number" id="item_price" required placeholder="Rp ......"
                        class="w-full rounded-xl border-0 bg-[#F6F4EE] px-4 py-3 text-sm text-gray-900 placeholder-gray-400 shadow-sm ring-1 ring-transparent transition focus:outline-none focus:ring-2 focus:ring-[#8FA88B]">
                </div>
            </div>

            <!-- ========================================================================= -->
            <!-- SECTION INPUT RESEP DINAMIS (Tetap dipertahankan biar backend ga rusak) -->
            <!-- ========================================================================= -->
            <div class="border-t border-dashed border-gray-200 pt-4">
                <div class="flex items-center justify-between mb-3">
                    <label class="block text-[13px] font-medium text-gray-600">Kebutuhan Bahan Baku</label>
                    <button type="button" id="add-ingredient-btn"
                        class="text-xs font-semibold text-[#6A8466] hover:text-[#3B5B43] transition bg-[#F6F4EE] px-3 py-1 rounded-full">
                        + Tambah Bahan
                    </button>
                </div>

                <div id="resep-container" class="space-y-2">
                    <div class="flex gap-2 items-center resep-row bg-white p-1">
                        <select name="ingredients[0][bahan_baku_id]"
                            class="flex-1 rounded-lg border-0 bg-[#F6F4EE] px-3 py-2.5 text-[13px] text-gray-900 focus:ring-2 focus:ring-[#8FA88B]"
                            required>
                            <option value="">-- Pilih Bahan --</option>
                            @foreach ($all_ingredients as $bahan)
                                <option value="{{ $bahan->id }}">{{ $bahan->nama_bahan }} ({{ $bahan->satuan }})
                                </option>
                            @endforeach
                        </select>
                        <input type="number" name="ingredients[0][quantity_needed]" step="0.01" min="0.01"
                            required placeholder="Qty"
                            class="w-20 rounded-lg border-0 bg-[#F6F4EE] px-3 py-2.5 text-[13px] text-gray-900 focus:ring-2 focus:ring-[#8FA88B] text-center">
                        <button type="button" class="text-gray-400 hover:text-red-500 transition px-1 remove-resep-btn"
                            title="Hapus">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 pointer-events-none" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <!-- ========================================================================= -->

            <!-- Gambar Produk -->
            <div>
                <label class="mb-1.5 block text-[13px] font-medium text-gray-600">Gambar Produk</label>
                <div
                    class="rounded-[20px] border-2 border-dashed border-[#D6E0D4] bg-[#FDFDFD] px-4 py-8 text-center transition-colors hover:border-[#8FA88B] hover:bg-[#F9FBF8]">
                    <label for="item_image" class="flex cursor-pointer flex-col items-center justify-center gap-2">
                        <div class="rounded-full bg-[#EDF2EB] p-3 text-[#6A8466] mb-1">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                            </svg>
                        </div>
                        <span class="text-[14px] font-semibold text-gray-800">Upload Gambar</span>
                        <span class="text-[11px] text-gray-400">SVG, PNG, JPG or GIF (max. 5MB)</span>
                        <span id="item_image_name"
                            class="text-[12px] text-[#6A8466] font-medium mt-2 bg-[#F6F4EE] px-3 py-1 rounded-full hidden"></span>
                    </label>
                    <input type="file" id="item_image" accept="image/*" class="hidden"
                        onchange="updateNamaFileProduk(this)">
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-4 pt-2">
                <button type="button" onclick="tutupModalProduk()"
                    class="text-[14px] font-medium text-gray-500 hover:text-gray-800 transition px-2">
                    Batal
                </button>
                <button type="submit"
                    class="inline-flex items-center justify-center rounded-xl px-8 py-3 text-[14px] font-semibold text-white transition bg-[#415C3E] hover:bg-[#344b32] shadow-md hover:shadow-lg"
                    style="min-width: 140px;">
                    Simpan Produk
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // FUNGSI TAMPIL BERANIMASI
    function tampilModalProduk() {
        const modal = document.getElementById('modalTambahProduk');
        const backdrop = document.getElementById('tambahBackdrop');
        const content = document.getElementById('tambahModalContent');
        if (!modal) return;

        // Munculkan secara teknis (display)
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        // Delay 50ms agar browser siap memutar animasi
        setTimeout(() => {
            if (backdrop) {
                backdrop.classList.remove('opacity-0');
                backdrop.classList.add('opacity-100');
            }
            if (content) {
                content.classList.remove('opacity-0', 'scale-95');
                content.classList.add('opacity-100', 'scale-100');
            }
        }, 50);
    }

    // FUNGSI TUTUP BERANIMASI
    function tutupModalProduk() {
        const modal = document.getElementById('modalTambahProduk');
        const backdrop = document.getElementById('tambahBackdrop');
        const content = document.getElementById('tambahModalContent');
        if (!modal) return;

        // Picu animasi menghilang
        if (backdrop) {
            backdrop.classList.remove('opacity-100');
            backdrop.classList.add('opacity-0');
        }
        if (content) {
            content.classList.remove('opacity-100', 'scale-100');
            content.classList.add('opacity-0', 'scale-95');
        }

        // Tunggu transisi selesai (300ms) baru di-hidden dan reset form
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');

            const form = document.getElementById('formProdukBaru');
            if (form) form.reset();

            const labelFile = document.getElementById('item_image_name');
            if (labelFile) {
                labelFile.textContent = '';
                labelFile.classList.add('hidden');
            }

            const container = document.getElementById('resep-container');
            if (container) {
                container.innerHTML = `
                    <div class="flex gap-2 items-center resep-row bg-white p-1">
                        <select name="ingredients[0][bahan_baku_id]" class="flex-1 rounded-lg border-0 bg-[#F6F4EE] px-3 py-2.5 text-[13px] text-gray-900 focus:ring-2 focus:ring-[#8FA88B]" required>
                            <option value="">-- Pilih Bahan --</option>
                            @foreach ($all_ingredients as $bahan)
                                <option value="{{ $bahan->id }}">{{ $bahan->nama_bahan }} ({{ $bahan->satuan }})</option>
                            @endforeach
                        </select>
                        <input type="number" name="ingredients[0][quantity_needed]" step="0.01" min="0.01" required placeholder="Qty"
                            class="w-20 rounded-lg border-0 bg-[#F6F4EE] px-3 py-2.5 text-[13px] text-gray-900 focus:ring-2 focus:ring-[#8FA88B] text-center">
                        <button type="button" class="text-gray-400 hover:text-red-500 transition px-1 remove-resep-btn" title="Hapus">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </div>
                `;
            }
        }, 300);
    }

    function updateNamaFileProduk(input) {
        const label = document.getElementById('item_image_name');
        if (!label) return;

        if (input.files && input.files.length > 0) {
            label.textContent = input.files[0].name;
            label.classList.remove('hidden');
        } else {
            label.textContent = '';
            label.classList.add('hidden');
        }
    }

    // Handle penambahan baris resep dinamis
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('resep-container');
        const addBtn = document.getElementById('add-ingredient-btn');
        let rowIndex = 1;

        if (addBtn && container) {
            addBtn.addEventListener('click', function() {
                const newRow = document.createElement('div');
                newRow.className = 'flex gap-2 items-center resep-row bg-white p-1 mt-2';
                newRow.innerHTML = `
                    <select name="ingredients[${rowIndex}][bahan_baku_id]" class="flex-1 rounded-lg border-0 bg-[#F6F4EE] px-3 py-2.5 text-[13px] text-gray-900 focus:ring-2 focus:ring-[#8FA88B]" required>
                        <option value="">-- Pilih Bahan --</option>
                        @foreach ($all_ingredients as $bahan)
                            <option value="{{ $bahan->id }}">{{ $bahan->nama_bahan }} ({{ $bahan->satuan }})</option>
                        @endforeach
                    </select>
                    <input type="number" name="ingredients[${rowIndex}][quantity_needed]" step="0.01" min="0.01" required placeholder="Qty"
                        class="w-20 rounded-lg border-0 bg-[#F6F4EE] px-3 py-2.5 text-[13px] text-gray-900 focus:ring-2 focus:ring-[#8FA88B] text-center">
                    <button type="button" class="text-gray-400 hover:text-red-500 transition px-1 remove-resep-btn" title="Hapus">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>
                `;
                container.appendChild(newRow);
                rowIndex++;
            });

            // Hapus baris resep dinamis
            container.addEventListener('click', function(e) {
                if (e.target.closest('.remove-resep-btn')) {
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
