<div id="deleteModal"
    class="fixed inset-0 z-[100] hidden flex items-start justify-center pt-20 bg-black/50 backdrop-blur-sm transition-opacity">
    <div id="deleteModalContent"
        class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4 transform transition-all scale-95 opacity-0 p-6 text-center">

        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <x-icons.trash class="w-8 h-8 text-red-600" />
        </div>

        <h3 class="text-xl font-bold text-gray-900 mb-2">Hapus Produk</h3>
        <p class="text-sm text-gray-600 mb-6">Apakah Anda yakin ingin menghapus produk ini? Tindakan ini tidak dapat
            dibatalkan.</p>

        <div class="flex items-center justify-center gap-3">
            <button type="button" onclick="closeDeleteModal()"
                class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                Batal
            </button>

            <form id="confirmDeleteForm" method="POST" onsubmit="hapusProdukAjax(event)">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="px-6 py-2.5 text-sm font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors shadow-sm">
                    Ya, Hapus
                </button>
            </form>
            <script>
                function hapusProdukAjax(event) {
                    event.preventDefault();
                    const form = event.target;

                    fetch(form.action, {
                            method: 'POST',
                            body: new FormData(form),
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                closeDeleteModal();
                                // Hapus elemen produk dari DOM tanpa reload halaman
                                location.reload(); // Atau hapus item dari list dengan JS
                            }
                        });
                }
            </script>
        </div>
    </div>
</div>
