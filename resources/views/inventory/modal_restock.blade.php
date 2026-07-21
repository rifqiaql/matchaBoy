<!-- Modal Backdrop -->
<div id="restockModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity backdrop-blur-sm"></div>

    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div id="restockModalContent"
                class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg scale-95 opacity-0 duration-200">

                <form id="restockForm" method="POST" action="">
                    @csrf

                    <!-- Header Modal -->
                    <div class="bg-white px-6 py-5 border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-50 rounded-full flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Catat Barang Masuk</h3>
                                <p class="text-sm text-gray-500">Menambah stok untuk: <span id="restockNamaBahan"
                                        class="font-bold text-gray-800"></span></p>
                            </div>
                        </div>
                    </div>

                    <!-- Body Modal -->
                    <div class="px-6 py-5 space-y-5 bg-gray-50/50">

                        <!-- 1. Input Multiplier (Kemasan x Isi) -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Kemasan <span
                                        class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="number" name="jumlah_kemasan" step="0.1" min="0.1" required
                                        class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
                                        placeholder="Cth: 2">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">Pack/Pcs</span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Isi per Kemasan <span
                                        class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="number" name="isi_per_kemasan" step="0.1" min="0.1" required
                                        class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
                                        placeholder="Cth: 1000">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <span id="labelSatuan1"
                                            class="text-gray-500 sm:text-sm font-semibold">Unit</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="text-xs text-blue-600 bg-blue-50 p-2 rounded border border-blue-100">
                            Sistem akan otomatis mengalikan jumlah kemasan dengan isinya untuk dimasukkan ke stok utama
                            (dalam <span id="labelSatuan2" class="font-bold">Unit</span>).
                        </p>

                        <!-- 2. Input Expired Date -->
                        <div>
                            <label for="tanggal_kedaluwarsa"
                                class="block text-sm font-medium text-gray-700 mb-1">Tanggal Kedaluwarsa
                                (Opsional)</label>
                            <input type="date" name="tanggal_kedaluwarsa" id="tanggal_kedaluwarsa"
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white text-gray-700">
                            <p class="text-xs text-gray-500 mt-1">Kosongkan jika barang tidak bisa basi (misal: Paper
                                Cup, Sedotan).</p>
                        </div>

                        <!-- 3. Input Catatan -->
                        <div>
                            <label for="catatan" class="block text-sm font-medium text-gray-700 mb-1">Referensi /
                                Catatan (Opsional)</label>
                            <textarea name="catatan" id="catatan" rows="2"
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white resize-none"
                                placeholder="Cth: Pembelian dari Supplier ABC, Nota #INV-1234"></textarea>
                        </div>

                    </div>

                    <!-- Footer Modal -->
                    <div class="bg-white px-6 py-4 border-t border-gray-100 flex items-center justify-end gap-3">
                        <button type="button" onclick="closeRestockModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 transition-colors shadow-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            Simpan Stok
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
