<!-- Modal Backdrop -->
<div id="restockModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- REVISI: Perbaikan Background Opacity agar tabel gudang tetap tembus pandang -->
    <div class="fixed inset-0 bg-black/50 transition-opacity backdrop-blur-sm"></div>

    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div id="restockModalContent"
                class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-xl scale-95 opacity-0 duration-200">

                <!-- Tombol Close (X) - Disesuaikan jadi putih transparan agar terlihat di atas hijau -->
                <button type="button" onclick="closeRestockModal()"
                    class="absolute top-5 right-5 w-8 h-8 flex items-center justify-center rounded-full bg-white/10 text-white/80 hover:bg-white/20 hover:text-white transition-colors focus:outline-none focus:ring-2 focus:ring-white/50 z-20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>

                <form id="restockForm" method="POST" action="">
                    @csrf

                    <!-- Header Modal - REVISI: Diubah menjadi warna Hijau MatchaBoy -->
                    <div class="bg-[#365E3F] px-8 py-6 relative">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                            </div>
                            <div class="pr-8"> <!-- Padding right agar teks tidak tertimpa tombol X -->
                                <h3 class="text-xl font-bold text-white">Catat Barang Masuk</h3>
                                <p class="text-sm text-green-100 mt-1">Menambah stok untuk: <span id="restockNamaBahan"
                                        class="font-semibold text-white"></span></p>
                            </div>
                        </div>
                    </div>

                    <!-- Body Modal -->
                    <div class="px-8 py-6 space-y-6 bg-white">

                        <!-- 1. Input Multiplier (Kemasan x Isi) -->
                        <div class="grid grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Jumlah Kemasan <span
                                        class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="number" name="jumlah_kemasan" step="0.1" min="0.1" required
                                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#365E3F] focus:border-transparent bg-white transition-all text-gray-800"
                                        placeholder="Contoh: 2">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                        <span class="text-gray-400 text-sm font-medium">Pack/Pcs</span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Isi per Kemasan <span
                                        class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="number" name="isi_per_kemasan" step="0.1" min="0.1" required
                                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#365E3F] focus:border-transparent bg-white transition-all text-gray-800"
                                        placeholder="Contoh: 1000">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                        <span id="labelSatuan1" class="text-gray-400 text-sm font-medium">Unit</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Kotak Informasi Kalkulasi -->
                        <div class="flex items-start gap-3 p-4 rounded-xl bg-green-50 border border-green-100">
                            <svg class="w-5 h-5 text-green-600 mt-0.5 shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-sm text-green-800 leading-relaxed">
                                Sistem otomatis mengalikan jumlah kemasan dengan isinya. Stok utama akan bertambah dalam
                                satuan <span id="labelSatuan2" class="font-bold">Unit</span>.
                            </p>
                        </div>

                        <!-- 2. Input Expired Date -->
                        <div>
                            <label for="tanggal_kedaluwarsa"
                                class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Kedaluwarsa <span
                                    class="text-gray-400 font-normal">(Opsional)</span></label>
                            <input type="date" name="tanggal_kedaluwarsa" id="tanggal_kedaluwarsa"
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#365E3F] focus:border-transparent bg-white transition-all text-gray-800">
                            <p class="text-xs text-gray-500 mt-2">Kosongkan jika barang tidak memiliki masa kedaluwarsa
                                (misal: Paper Cup, Sedotan).</p>
                        </div>

                        <!-- 3. Input Catatan -->
                        <div>
                            <label for="catatan" class="block text-sm font-semibold text-gray-700 mb-2">Referensi /
                                Catatan <span class="text-gray-400 font-normal">(Opsional)</span></label>
                            <textarea name="catatan" id="catatan" rows="3"
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#365E3F] focus:border-transparent bg-white transition-all text-gray-800 resize-none"
                                placeholder="Cth: Pembelian dari Supplier ABC, Nota #INV-1234"></textarea>
                        </div>

                    </div>

                    <!-- Footer Modal -->
                    <div class="bg-gray-50/50 px-8 py-5 border-t border-gray-100 flex items-center justify-end gap-3">
                        <button type="button" onclick="closeRestockModal()"
                            class="px-6 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-200">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-6 py-2.5 text-sm font-semibold text-white bg-[#365E3F] border border-transparent rounded-xl hover:bg-[#2a4a31] transition-colors shadow-sm flex items-center gap-2 focus:outline-none focus:ring-2 focus:ring-[#365E3F] focus:ring-offset-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4"></path>
                            </svg>
                            Simpan Stok
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
