@if (auth()->user() && auth()->user()->role === 'admin')
    <div id="modalEditProduk" class="fixed inset-0 z-50 flex items-center justify-center hidden">
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="tutupModalEdit()"></div>

        <div
            class="bg-white rounded-2xl w-full max-w-md p-6 shadow-2xl relative z-10 m-4 border border-gray-100 animate-fade-in">
            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Edit Produk</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Perbarui data katalog produk Matchachaboy</p>
                </div>
                <button type="button" onclick="tutupModalEdit()"
                    class="text-gray-400 hover:text-gray-600 transition-colors text-2xl p-1">&times;</button>
            </div>

            <form id="formEditProduk" onsubmit="simpanPerubahanProduk(event)" class="mt-4 space-y-4">
                <input type="hidden" id="edit_item_id">

                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider">Item Name</label>
                    <input type="text" id="edit_item_name" required
                        class="w-full px-3 py-2.5 mt-1 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-600/20 focus:border-green-600 bg-gray-50/50 text-sm">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider">Harga</label>
                    <div class="relative mt-1">
                        <span
                            class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 text-sm font-medium">Rp</span>
                        <input type="number" id="edit_item_price" required
                            class="w-full pl-9 pr-3 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-600/20 focus:border-green-600 bg-gray-50/50 text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Gambar
                        Produk (Opsional)</label>
                    <div
                        class="border border-dashed border-gray-300 rounded-xl p-3 bg-gray-50 flex flex-col items-center justify-center hover:bg-gray-100/70 transition-colors relative">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-6 h-6 text-gray-400 mb-1">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                        </svg>
                        <span class="text-xs text-gray-500 font-medium mb-1">Klik untuk mengganti gambar</span>
                        <input type="file" id="edit_item_image" accept="image/*"
                            class="absolute inset-0 opacity-0 cursor-pointer w-full h-full">
                        <span id="edit_file_name_preview"
                            class="text-[11px] text-green-600 font-medium text-center truncate max-w-full px-2"></span>
                    </div>
                </div>

                <div class="flex justify-end pt-3 space-x-2 border-t border-gray-100">
                    <button type="button" onclick="tutupModalEdit()"
                        class="px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">Batal</button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white rounded-xl bg-green-700 hover:bg-green-800 shadow-sm transition-colors">Simpan
                        Perubahan</button>
                </div>
            </form>
        </div>
    </div>
@endif
