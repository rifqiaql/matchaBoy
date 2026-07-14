<div id="deleteModal"
    class="fixed inset-0 z-50 hidden flex items-start justify-center pt-20 bg-black/50 backdrop-blur-sm transition-opacity">

    <div id="deleteModalContent"
        class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4 overflow-hidden transform transition-all scale-95 opacity-0 p-6 text-center">

        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <x-icons.trash class="w-8 h-8 text-red-600" />
        </div>

        <h3 class="text-xl font-bold text-gray-900 mb-2">Hapus Bahan Baku</h3>
        <p class="text-sm text-gray-600 mb-6">Apakah Anda yakin ingin menghapus bahan baku ini? Tindakan ini tidak dapat
            dibatalkan.</p>

        <div class="flex items-center justify-center gap-3">
            <button type="button" onclick="closeDeleteModal()"
                class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                Batal
            </button>
            <form id="confirmDeleteForm" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="px-6 py-2.5 text-sm font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors shadow-sm">
                    Ya, Hapus
                </button>
            </form>
        </div>

    </div>
</div>
