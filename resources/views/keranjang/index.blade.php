@extends('layouts.app')

@section('content')
    <div class="p-8">
        <h1 class="text-2xl font-semibold mb-6">Keranjang</h1>
    </div>

    <div class="px-8 grid grid-cols-12 gap-6">

        <div class="col-span-9">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <button class="px-3 py-1 bg-white border rounded text-sm hover:bg-gray-50">All items</button>
                    <button class="px-3 py-1 bg-white border rounded text-sm hover:bg-gray-50">Signature</button>
                    <button class="px-3 py-1 bg-white border rounded text-sm hover:bg-gray-50">Milk-based</button>
                    <button class="px-3 py-1 bg-white border rounded text-sm hover:bg-gray-50">Strawberry</button>
                </div>
                <div>
                    <button class="btn-icon primary" title="Tambah Produk Baru" type="button"
                        onclick="tampilModalProduk()">
                        <x-icon name="plus" size="md" class="w-4 h-4 stroke-current" />
                        <span class="text-sm font-medium">Tambah Produk Baru</span>
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-6">
                @forelse ($products as $product)
                    @php
                        $productImage =
                            $product->image &&
                            \Illuminate\Support\Facades\Storage::disk('public')->exists('products/' . $product->image)
                                ? asset('storage/products/' . $product->image)
                                : asset('images/logo.png');
                    @endphp
                    <div class="bg-white rounded-lg p-6 shadow">
                        <div class="h-40 bg-gray-50 rounded flex items-center justify-center mb-4 overflow-hidden">
                            <img src="{{ $productImage }}" alt="{{ $product->name }}" class="h-20 w-auto object-contain"
                                onerror="this.onerror=null;this.src='{{ asset('images/logo.png') }}';">
                        </div>
                        <h4 class="font-semibold mb-1 truncate" title="{{ $product->name }}">{{ $product->name }}</h4>
                        <p class="text-xs text-gray-500 mb-3">Produk</p>
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-bold">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                            <button class="btn-icon primary text-sm add-to-cart" data-id="{{ $product->id }}"
                                data-name="{{ $product->name }}" data-price="{{ $product->price }}"
                                data-image="{{ $productImage }}" title="Tambah ke keranjang">
                                <x-icon name="shopping-cart" size="md" class="w-4 h-4 stroke-current" />
                                <span>Tambah</span>
                            </button>
                        </div>
                    </div>
                @empty
                    <div
                        class="col-span-3 rounded-lg border border-dashed border-gray-200 bg-white p-8 text-center text-sm text-gray-500">
                        Belum ada produk di tabel products.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="col-span-3">
            <div class="bg-white rounded-xl p-6 shadow sticky top-6">
                <h3 class="text-lg font-semibold mb-4">Ringkasan Keranjang</h3>

                <div id="cart-items-container" class="space-y-4 max-h-60 overflow-y-auto mb-4">
                    <div class="py-10 text-center text-gray-400 text-sm" id="empty-cart-msg">
                        Keranjang kosong
                    </div>
                </div>

                <hr class="border-gray-200 my-4">

                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-600">Subtotal</span>
                    <span class="font-semibold" id="subtotal">Rp 0</span>
                </div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-600">Tax (10%)</span>
                    <span class="font-semibold" id="tax">Rp 0</span>
                </div>
                <div class="flex items-center justify-between mt-4 mb-4">
                    <span class="text-sm font-semibold">Total Pembelian</span>
                    <span class="text-xl font-bold" id="total">Rp 0</span>
                </div>

                <button class="w-full btn-icon primary cart-checkout" title="Bayar Sekarang" id="checkout-btn" disabled>
                    <x-icon name="credit-card" size="md" class="w-4 h-4 stroke-current" />
                    <span class="font-medium">Bayar Sekarang</span>
                </button>
            </div>
        </div>
    </div>

    @include('keranjang.create')

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let cart = [];
                const cartContainer = document.getElementById('cart-items-container');
                const subtotalEl = document.getElementById('subtotal');
                const taxEl = document.getElementById('tax');
                const totalEl = document.getElementById('total');
                const checkoutBtn = document.getElementById('checkout-btn');

                function formatRupiah(number) {
                    return 'Rp ' + number.toLocaleString('id-ID');
                }

                // 1. UPDATE DOM: Merender gambar produk di samping deskripsi nama
                function updateCartDOM() {
                    if (cart.length === 0) {
                        cartContainer.innerHTML =
                            `<div class="py-10 text-center text-gray-400 text-sm">Keranjang kosong</div>`;
                        subtotalEl.innerText = 'Rp 0';
                        taxEl.innerText = 'Rp 0';
                        totalEl.innerText = 'Rp 0';
                        checkoutBtn.disabled = true;
                        return;
                    }

                    cartContainer.innerHTML = '';
                    let subtotal = 0;

                    cart.forEach(item => {
                        subtotal += item.price * item.quantity;
                        const itemEl = document.createElement('div');
                        // Menggunakan flex biasa, item dibikin justify-between agar memisah kiri dan kanan
                        itemEl.className =
                            'flex items-center justify-between border-b border-gray-100 py-3 gap-2 w-full';
                        itemEl.innerHTML = `
        <div class="flex items-center gap-2 w-3/5 min-w-0">
            <div class="w-10 h-10 bg-gray-50 border border-gray-100 rounded-lg flex items-center justify-center p-1 shrink-0">
                <img src="${item.image}" alt="${item.name}" class="w-full h-full object-contain rounded">
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold text-gray-800 truncate" title="${item.name}">${item.name}</p>
                <p class="text-xs text-gray-500 mt-0.5">${formatRupiah(item.price)} x ${item.quantity}</p>
            </div>
        </div>

        <div class="flex items-center justify-end gap-1 w-2/5 shrink-0">
            <div class="flex items-center bg-gray-50 border border-gray-200 rounded-lg p-0.5 shadow-sm">
                <button type="button" class="w-7 h-7 flex items-center justify-center text-xs font-bold text-gray-600 hover:bg-gray-200 rounded transition-colors minus-qty" data-id="${item.id}">-</button>
                <span class="text-xs font-bold text-gray-800 min-w-[18px] text-center">${item.quantity}</span>
                <button type="button" class="w-7 h-7 flex items-center justify-center text-xs font-bold text-gray-600 hover:bg-gray-200 rounded transition-colors plus-qty" data-id="${item.id}">+</button>
            </div>

            <button type="button" class="w-8 h-8 flex items-center justify-center text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors remove-item" data-id="${item.id}" title="Hapus item">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 pointer-events-none">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                </svg>
            </button>
        </div>
    `;
                        cartContainer.appendChild(itemEl);
                    });

                    const tax = subtotal * 0.1;
                    const total = subtotal + tax;

                    subtotalEl.innerText = formatRupiah(subtotal);
                    taxEl.innerText = formatRupiah(tax);
                    totalEl.innerText = formatRupiah(total);
                    checkoutBtn.disabled = false;
                }

                // 2. EVENT LISTENER TAMBAH KE KERANJANG: Menangkap path gambar produk
                document.querySelectorAll('.add-to-cart').forEach(button => {
                    button.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        const name = this.getAttribute('data-name');
                        const price = parseInt(this.getAttribute('data-price'));
                        const image = this.getAttribute('data-image'); // Simpan path gambar ke variabel

                        const existingItem = cart.find(item => String(item.id) === String(id));

                        if (existingItem) {
                            existingItem.quantity += 1;
                        } else {
                            cart.push({
                                id,
                                name,
                                price,
                                image, // Masukkan variabel gambar ke dalam object cart
                                quantity: 1
                            });
                        }

                        updateCartDOM();
                    });
                });

                // 3. EVENT DELEGATION: Pengendali internal tombol di dalam keranjang
                cartContainer.addEventListener('click', function(e) {
                    const plusBtn = e.target.closest('.plus-qty');
                    if (plusBtn) {
                        const id = plusBtn.getAttribute('data-id');
                        const item = cart.find(item => String(item.id) === String(id));
                        if (item) item.quantity += 1;
                        updateCartDOM();
                        return;
                    }

                    const minusBtn = e.target.closest('.minus-qty');
                    if (minusBtn) {
                        const id = minusBtn.getAttribute('data-id');
                        const itemIndex = cart.findIndex(item => String(item.id) === String(id));
                        if (itemIndex > -1) {
                            cart[itemIndex].quantity -= 1;
                            if (cart[itemIndex].quantity <= 0) {
                                cart.splice(itemIndex, 1);
                            }
                        }
                        updateCartDOM();
                        return;
                    }

                    const removeBtn = e.target.closest('.remove-item');
                    if (removeBtn) {
                        const id = removeBtn.getAttribute('data-id');
                        const itemIndex = cart.findIndex(item => String(item.id) === String(id));
                        if (itemIndex > -1) {
                            cart.splice(itemIndex, 1);
                        }
                        updateCartDOM();
                        return;
                    }
                });
            });

            function simpanProdukBaru(event) {
                event.preventDefault(); // Mencegah reload halaman bawaan form browser

                // 1. Ambil elemen input secara aman
                let nameElement = document.getElementById('item_name');
                let priceElement = document.getElementById('item_price');
                let imageElement = document.getElementById('item_image');

                // Validasi pencegahan eror jika elemen HTML tidak ditemukan di layar
                if (!nameElement || !priceElement) {
                    alert("Elemen input HTML tidak ditemukan. Periksa kembali ID input di file create.blade.php Anda!");
                    return;
                }

                let nameInput = nameElement.value;
                let priceInput = priceElement.value;

                // PENGAMAN: Ambil file gambar HANYA JIKA elemennya ada dan user sudah memilih file
                let imageInput = (imageElement && imageElement.files) ? imageElement.files[0] : null;

                if (!nameInput || !priceInput) {
                    alert("Nama produk dan Harga wajib diisi!");
                    return;
                }

                // 2. Menggunakan FormData untuk mendukung upload file
                let formData = new FormData();
                formData.append('name', nameInput);
                formData.append('price', priceInput);
                if (imageInput) {
                    formData.append('image', imageInput);
                }

                // 3. Kirim data ke backend Laravel via AJAX Fetch
                fetch("{{ route('products.store') }}", {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            // Token keamanan Laravel wajib diambil dari tag meta head
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: formData
                    })
                    .then(async response => {
                        const responseText = await response.text();
                        let payload = null;

                        try {
                            payload = responseText ? JSON.parse(responseText) : null;
                        } catch (parseError) {
                            payload = null;
                        }

                        if (!response.ok) {
                            const errorMessage = payload?.message || responseText || ('HTTP error, status = ' + response
                                .status);
                            throw new Error('HTTP ' + response.status + ': ' + errorMessage);
                        }

                        return payload;
                    })
                    .then(data => {
                        if (data && data.success) {
                            alert(data.message);
                            location.reload(); // Reload halaman agar menu baru langsung muncul di katalog kasir
                        } else {
                            alert("Gagal: " + (data?.message || 'Respons server tidak valid.'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert(error.message || "Terjadi kegagalan sistem saat menyambung ke server.");
                    });
            }
        </script>
    @endpush
@endsection
