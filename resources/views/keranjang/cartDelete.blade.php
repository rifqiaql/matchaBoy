<div id="deleteModal"
    class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-black/40 backdrop-blur-sm p-4 transition-opacity">

    <!-- Latar transparan yang bisa diklik untuk tutup -->
    <div class="absolute inset-0" onclick="closeDeleteModal()"></div>

    <!-- Modal Content: Dipaksa ke tengah oleh items-center dari parent -->
    <div id="deleteModalContent"
        class="bg-white rounded-[20px] shadow-2xl w-full max-w-sm mx-4 relative z-10 transform transition-all p-7 text-center">

        <div class="w-16 h-16 bg-[#FFF4F2] rounded-full flex items-center justify-center mx-auto mb-4">
            <x-icons.trash class="w-8 h-8 text-[#E04F43]" />
        </div>

        <h3 class="text-[17px] font-bold text-gray-900 mb-1.5">Hapus Produk</h3>
        <p class="text-[13px] text-gray-500 mb-6 px-2">Apakah Anda yakin ingin menghapus produk ini? Tindakan ini tidak
            dapat dibatalkan.</p>

        <div class="flex items-center justify-center gap-3">
            <button type="button" onclick="closeDeleteModal()"
                class="px-5 py-2.5 text-[13px] font-medium text-gray-600 bg-gray-50 hover:bg-gray-100 rounded-xl transition-colors w-full">
                Batal
            </button>

            <form id="confirmDeleteForm" method="POST" onsubmit="hapusProdukAjax(event)" class="w-full">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="w-full px-5 py-2.5 text-[13px] font-semibold text-white bg-[#E04F43] hover:bg-[#c9453a] rounded-xl transition-colors shadow-sm">
                    Ya, Hapus
                </button>
            </form>
        </div>
    </div>
</div>

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
                    location.reload();
                }
            });
    }
</script>
