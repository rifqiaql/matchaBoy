@push('scripts')
    <script>
        /**
         * Hapus Produk dari Katalog via AJAX (DELETE)
         * Dipanggil saat tombol hapus di card produk diklik
         */
        function hapusProduk(id, name) {
            if (confirm(`Apakah Anda yakin ingin menghapus produk "${name}" dari katalog?`)) {
                fetch(`/products/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            location.reload();
                        } else {
                            alert("Gagal menghapus produk: " + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert("Terjadi kegagalan sistem saat menghapus produk.");
                    });
            }
        }
    </script>
@endpush
