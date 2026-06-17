@props(['show' => false, 'id' => 'modal-add-product'])

<!-- Overlay & Wrapper -->
<div x-data="{ open: false }" @open-add-product-modal.window="open = true" x-show="open" @click="open = false"
    style="display: none" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
    x-transition>
    <!-- Modal Container -->
    <div @click.stop class="w-full max-w-lg bg-white rounded-xl shadow-xl overflow-hidden">
        <!-- Header -->
        <div class="bg-[#8FA88B] p-6 flex justify-between items-start">
            <div>
                <h2 class="text-lg font-semibold text-white">Produk Baru</h2>
                <p class="text-sm text-white/80 mt-1">Tambahkan item baru ke katalog inventaris Matchaboy</p>
            </div>
            <button @click="open = false" class="text-white hover:opacity-80 transition-opacity">
                <x-icon name="x" size="md" class="w-5 h-5" />
            </button>
        </div>

        <!-- Body (Form) -->
        <div class="p-6 space-y-5">
            <!-- Item Name -->
            <div>
                <label class="text-sm text-gray-500 mb-1 block">Item Name</label>
                <input type="text" placeholder="Matcha Latte....."
                    class="w-full bg-[#F6F4EE] border-none rounded-md text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-[#8FA88B] focus:outline-none transition" />
            </div>

            <!-- Grid: Kategori & Harga -->
            <div class="grid grid-cols-2 gap-4">
                <!-- Kategori -->
                <div>
                    <label class="text-sm text-gray-500 mb-1 block">Kategori</label>
                    <select
                        class="w-full bg-[#F6F4EE] border-none rounded-md text-gray-900 focus:ring-2 focus:ring-[#8FA88B] focus:outline-none transition">
                        <option value="">Select category</option>
                        <option value="matcha">Matcha</option>
                        <option value="coffee">Coffee</option>
                        <option value="tea">Tea</option>
                        <option value="snack">Snack</option>
                    </select>
                </div>

                <!-- Harga -->
                <div>
                    <label class="text-sm text-gray-500 mb-1 block">Harga</label>
                    <input type="text" placeholder="Rp ....."
                        class="w-full bg-[#F6F4EE] border-none rounded-md text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-[#8FA88B] focus:outline-none transition" />
                </div>
            </div>

            <!-- Gambar Produk -->
            <div>
                <label class="text-sm text-gray-500 mb-2 block">Gambar Produk</label>
                <div
                    class="border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 flex flex-col items-center justify-center py-8 cursor-pointer hover:border-[#8FA88B] transition-colors">
                    <x-icon name="cloud-upload" size="lg" class="w-8 h-8 text-[#8FA88B] mb-2" />
                    <p class="font-semibold text-gray-700">Upload Gambar</p>
                    <p class="text-xs text-gray-400 mt-1">SVG, PNG, JPG or GIF (max. 800x400px)</p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="px-6 pb-6 flex justify-end gap-3 mt-6 border-t border-gray-100 pt-4">
            <button @click="open = false" class="text-gray-500 hover:text-gray-700 font-medium text-sm transition">
                Batal
            </button>
            <button
                class="bg-[#3B5B43] text-white px-5 py-2 rounded-md text-sm font-medium hover:bg-opacity-90 transition">
                Simpan Produk
            </button>
        </div>
    </div>
</div>
