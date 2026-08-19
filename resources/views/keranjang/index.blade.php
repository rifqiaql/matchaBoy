@extends('layouts.app')

@section('content')
    <div class="p-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Keranjang Kasir</h1>
    </div>

    <div class="px-8 grid grid-cols-12 gap-6 items-start">

        <!-- KIRI: KATALOG PRODUK -->
        <div class="col-span-9">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3"></div>
                <div>
                    @if (auth()->user() && auth()->user()->role === 'admin')
                        <button
                            class="flex items-center gap-2 bg-[#2E4F4F] text-white px-4 py-2 rounded-lg shadow-sm text-sm font-semibold hover:bg-opacity-90 transition-all"
                            title="Tambah Produk Baru" type="button" onclick="tampilModalProduk()">
                            <x-icon name="plus" size="md" class="w-4 h-4 stroke-current" />
                            <span>Tambah Produk</span>
                        </button>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-3 gap-6">
                @forelse ($products as $product)
                    @php
                        // Cek file gambar
                        $productImage =
                            $product->image &&
                            \Illuminate\Support\Facades\Storage::disk('public')->exists('products/' . $product->image)
                                ? asset('storage/products/' . $product->image)
                                : asset('images/logo.png');

                        $ingredientList = '';
                        $maxCups = null; // Variabel untuk menyimpan kalkulasi pembatas (Bottleneck)

                        if ($product->ingredients && $product->ingredients->count() > 0) {
                            $names = [];
                            foreach ($product->ingredients as $ing) {
                                $names[] = $ing->name ?? ($ing->nama_bahan ?? 'Bahan');

                                // KALKULASI LIMITING FACTOR (ESTIMASI CUP)
                                $stock = $ing->stock ?? ($ing->stok_saat_ini ?? 0);
                                // CATATAN: Pastikan nama kolom pivot takaran resep di DB lu sesuai (misal: quantity_needed atau gramasi)
                                $qtyNeeded = $ing->pivot->quantity_needed ?? ($ing->pivot->gramasi ?? 1);

                                if ($qtyNeeded > 0) {
                                    $possibleCups = floor($stock / $qtyNeeded);

                                    // Ambil nilai terkecil dari seluruh bahan baku
                                    if ($maxCups === null || $possibleCups < $maxCups) {
                                        $maxCups = $possibleCups;
                                    }
                                }
                            }
                            $ingredientList = implode(', ', $names);
                        }

                        // Jika tidak ada bahan, default ke 0
                        $maxCups = $maxCups !== null ? $maxCups : 0;
                        $isOutOfStock = $maxCups <= 0;
                    @endphp

                    <div
                        class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 relative group flex flex-col transition-all duration-300 hover:shadow-md">

                        @if (auth()->user() && auth()->user()->role === 'admin')
                            <div
                                class="absolute top-3 right-3 flex items-center gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity duration-200 z-10 bg-white/90 backdrop-blur-sm p-1.5 rounded-lg shadow-sm border border-gray-100">

                                <button type="button"
                                    class="p-1.5 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-md transition-colors"
                                    title="Edit Produk" data-id="{{ $product->id }}" data-name="{{ $product->name }}"
                                    data-category="{{ $product->category }}" data-price="{{ $product->price }}"
                                    data-ingredients="{{ json_encode($product->ingredients) }}"
                                    onclick="bukaModalEdit(this)">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                </button>

                                <button type="button"
                                    class="p-1.5 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-md transition-colors"
                                    title="Hapus Produk"
                                    onclick="hapusProduk('{{ $product->id }}', '{{ addslashes($product->name) }}')">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </button>
                            </div>
                        @endif

                        <div
                            class="h-40 bg-gray-50/60 rounded-xl flex items-center justify-center mb-4 p-3 overflow-hidden relative">
                            <img src="{{ $productImage }}" alt="{{ $product->name }}"
                                class="h-full w-full object-contain drop-shadow-md group-hover:scale-110 transition-transform duration-300 ease-in-out {{ $isOutOfStock ? 'grayscale opacity-60' : '' }}"
                                onerror="this.onerror=null;this.src='{{ asset('images/logo.png') }}';">
                        </div>

                        <div class="flex-1 flex flex-col justify-end">
                            <h4 class="font-bold text-gray-800 mb-0.5 truncate" title="{{ $product->name }}">
                                {{ $product->name }}</h4>

                            <div class="flex items-center justify-between mb-3">
                                <p class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold">
                                    {{ $product->category ?? 'Produk' }}</p>

                                <!-- BADGE ESTIMASI CUP BERDASARKAN BAHAN TERKECIL -->
                                <span
                                    class="px-2 py-0.5 rounded text-[10px] font-bold border shadow-sm {{ $isOutOfStock ? 'bg-red-50 text-red-600 border-red-100' : 'bg-[#2D5A34]/10 text-[#2D5A34] border-[#2D5A34]/20' }}">
                                    {{ $isOutOfStock ? 'Stok Habis' : 'Est: ' . $maxCups . ' Cup' }}
                                </span>
                            </div>

                            <div class="mb-4 space-y-2 border-t border-gray-50 pt-3">
                                @if ($product->ingredients && $product->ingredients->count() > 0)
                                    @foreach ($product->ingredients as $ing)
                                        @php
                                            $stock = $ing->stock ?? ($ing->stok_saat_ini ?? 0);
                                            $minStock = $ing->minimum_stock ?? ($ing->batas_limit ?? 500);
                                            $unit = $ing->unit ?? ($ing->satuan ?? '');

                                            $visualMax = $minStock > 0 ? $minStock * 3 : 1000;
                                            if ($stock > $visualMax) {
                                                $visualMax = $stock;
                                            }
                                            $percent = $visualMax > 0 ? ($stock / $visualMax) * 100 : 0;

                                            $isCritical = $stock <= $minStock;
                                            $isWarning = $stock > $minStock && $stock <= $minStock * 2;

                                            $barColor = 'bg-green-500';
                                            $textColor = 'text-gray-600';

                                            if ($isCritical) {
                                                $barColor = 'bg-red-500';
                                                $textColor = 'text-red-600';
                                            } elseif ($isWarning) {
                                                $barColor = 'bg-[#EAB308]';
                                                $textColor = 'text-[#EAB308]';
                                            }
                                        @endphp
                                        <div class="flex items-center justify-between gap-2"
                                            title="Sisa di gudang: {{ number_format($stock, 0, ',', '.') }} {{ $unit }}">
                                            <span
                                                class="text-[10px] text-gray-500 truncate w-4/12">{{ $ing->name ?? ($ing->nama_bahan ?? 'Bahan') }}</span>

                                            <div class="flex-1 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                                <div class="h-full rounded-full transition-all duration-500 {{ $barColor }}"
                                                    style="width: {{ $percent }}%;"></div>
                                            </div>

                                            <span class="text-[9px] font-bold {{ $textColor }} w-3/12 text-right">
                                                {{ number_format($stock, 0, '', '.') }}
                                            </span>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-[10px] text-gray-400 italic">Resep bahan belum diatur</p>
                                @endif
                            </div>

                            <div class="flex items-center justify-between gap-3 mt-auto border-t border-gray-100 pt-3">
                                <p class="text-sm font-extrabold text-[#2D5A34]">Rp
                                    {{ number_format($product->price, 0, ',', '.') }}</p>

                                <!-- TOMBOL ADD DI-DISABLE JIKA IS OUT OF STOCK -->
                                <button
                                    class="flex items-center gap-1 bg-gray-100 hover:bg-[#2D5A34] text-gray-700 hover:text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-colors add-to-cart disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-gray-100 disabled:hover:text-gray-700"
                                    data-id="{{ $product->id }}" data-name="{{ $product->name }}"
                                    data-price="{{ $product->price }}" data-image="{{ $productImage }}"
                                    data-ingredients-list="{{ $ingredientList }}" {{ $isOutOfStock ? 'disabled' : '' }}
                                    title="{{ $isOutOfStock ? 'Bahan baku tidak mencukupi untuk membuat menu ini' : 'Tambah ke keranjang' }}">
                                    <x-icon name="shopping-cart" size="sm" class="w-3.5 h-3.5 stroke-current" />
                                    <span>Add</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div
                        class="col-span-3 rounded-2xl border-2 border-dashed border-gray-200 bg-gray-50/50 p-12 text-center flex flex-col items-center justify-center">
                        <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        <p class="text-sm text-gray-500 font-medium">Belum ada produk di database.</p>
                        <p class="text-xs text-gray-400 mt-1">Silakan tambah produk baru melalui panel Admin.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- KANAN: RINGKASAN KERANJANG -->
        <div class="col-span-3">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 sticky top-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Ringkasan Keranjang</h3>
                <div id="cart-items-container" class="space-y-3 max-h-[300px] overflow-y-auto mb-4 pr-2">
                    <div class="py-12 text-center flex flex-col items-center justify-center" id="empty-cart-msg">
                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                            <svg class="w-8 h-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-gray-400">Keranjang masih kosong</p>
                    </div>
                </div>

                <div class="border-t border-dashed border-gray-200 pt-4 pb-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500">Subtotal</span>
                        <span class="text-sm font-bold text-gray-800" id="subtotal">Rp 0</span>
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-4 mb-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-bold text-gray-800">Total Pembelian</span>
                        <span class="text-xl font-extrabold text-[#2D5A34]" id="total">Rp 0</span>
                    </div>
                </div>

                <!-- FORM PEMBAYARAN KASIR -->
                <div class="border-t border-gray-100 pt-4 mb-3">
                    <label class="block text-sm font-bold text-gray-800 mb-2">Uang Pembeli (Tunai)</label>
                    <div class="relative">
                        <span
                            class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 font-semibold">Rp</span>
                        <input type="text" id="cash-input"
                            class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#2D5A34] focus:border-transparent outline-none transition-all font-semibold text-gray-800"
                            placeholder="0">
                    </div>
                </div>

                <div class="flex items-center justify-between mb-5">
                    <span class="text-sm font-bold text-gray-800">Kembalian</span>
                    <span class="text-lg font-bold text-gray-400" id="change-display">Rp 0</span>
                </div>

                <button
                    class="w-full flex items-center justify-center gap-2 bg-[#2D5A34] text-white px-4 py-3 rounded-xl font-bold hover:bg-opacity-90 transition-all disabled:opacity-50 disabled:cursor-not-allowed cart-checkout"
                    title="Bayar Sekarang" id="checkout-btn" disabled>
                    <x-icon name="credit-card" size="sm" class="w-4 h-4 stroke-current" />
                    <span>Check Out Pembayaran</span>
                </button>
            </div>
        </div>
    </div>

    @include('keranjang.create')
    @include('keranjang.cartEdit')
    @include('keranjang.cartDelete')

    @push('scripts')
        <script>
            // =========================================================================
            // 1. MODUL HAPUS PRODUK (BUKA & TUTUP)
            // =========================================================================
            function hapusProduk(id) {
                const modal = document.getElementById('deleteModal');
                const form = document.getElementById('confirmDeleteForm');
                const content = document.getElementById('deleteModalContent');

                if (!modal || !form) {
                    alert("Modal hapus tidak ditemukan. Pastikan cartDelete.blade.php sudah di-include!");
                    return;
                }

                form.action = `/products/${id}`;

                modal.classList.remove('hidden');
                modal.classList.add('flex');

                setTimeout(() => {
                    if (content) {
                        content.classList.remove('scale-95', 'opacity-0');
                        content.classList.add('scale-100', 'opacity-100');
                    }
                }, 50);
            }

            function closeDeleteModal() {
                const modal = document.getElementById('deleteModal');
                const content = document.getElementById('deleteModalContent');
                if (!modal) return;

                if (content) {
                    content.classList.remove('scale-100', 'opacity-100');
                    content.classList.add('scale-95', 'opacity-0');
                }

                setTimeout(() => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }, 300);
            }

            // =========================================================================
            // 2. AJAX SUBMIT DATA KE CONTROLLER
            // =========================================================================
            function simpanProdukBaru(event) {
                event.preventDefault();
                let nameElement = document.getElementById('item_name');
                let priceElement = document.getElementById('item_price');
                let categoryElement = document.getElementById('item_category');
                let imageElement = document.getElementById('item_image');

                let nameInput = nameElement.value;
                let priceInput = priceElement.value;
                let categoryInput = categoryElement ? categoryElement.value : '';
                let imageInput = (imageElement && imageElement.files) ? imageElement.files[0] : null;

                let formData = new FormData();
                formData.append('name', nameInput);
                formData.append('price', priceInput);
                formData.append('category', categoryInput);
                if (imageInput) formData.append('image', imageInput);

                const createForm = document.getElementById('formProdukBaru');
                const resepInputs = createForm.querySelectorAll('[name^="ingredients"]');
                resepInputs.forEach(input => formData.append(input.name, input.value));

                fetch("{{ route('products.store') }}", {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            location.reload();
                        } else {
                            alert("Gagal menambahkan produk: " + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert("Terjadi kegagalan sistem saat menyambung ke server.");
                    });
            }

            function simpanPerubahanProduk(event) {
                event.preventDefault();
                let id = document.getElementById('edit_item_id').value;
                let name = document.getElementById('edit_item_name').value;
                let price = document.getElementById('edit_item_price').value;
                let category = document.getElementById('edit_item_category').value;
                let imageElement = document.getElementById('edit_item_image');
                let imageInput = (imageElement && imageElement.files) ? imageElement.files[0] : null;

                let formData = new FormData();
                formData.append('_method', 'PUT');
                formData.append('name', name);
                formData.append('price', price);
                formData.append('category', category);
                if (imageInput) formData.append('image', imageInput);

                const editForm = document.getElementById('formEditProduk');
                const resepInputs = editForm.querySelectorAll('[name^="ingredients"]');
                resepInputs.forEach(input => formData.append(input.name, input.value));

                fetch(`/products/${id}`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            location.reload();
                        } else {
                            alert("Gagal memperbarui produk: " + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert("Terjadi kegagalan sistem saat menghubungi server.");
                    });
            }

            // =========================================================================
            // 3. LOGIKA FRONTEND KERANJANG BELANJA & PEMBAYARAN KASIR
            // =========================================================================
            document.addEventListener('DOMContentLoaded', function() {
                let cart = [];
                let currentTotal = 0;

                const cartContainer = document.getElementById('cart-items-container');
                const subtotalEl = document.getElementById('subtotal');
                const totalEl = document.getElementById('total');
                const checkoutBtn = document.getElementById('checkout-btn');
                const cashInput = document.getElementById('cash-input');
                const changeDisplay = document.getElementById('change-display');

                function formatRupiah(number) {
                    return 'Rp ' + number.toLocaleString('id-ID');
                }

                function formatRibuan(angka) {
                    let number_string = angka.replace(/[^,\d]/g, '').toString(),
                        split = number_string.split(','),
                        sisa = split[0].length % 3,
                        rupiah = split[0].substr(0, sisa),
                        ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                    if (ribuan) {
                        let separator = sisa ? '.' : '';
                        rupiah += separator + ribuan.join('.');
                    }

                    return rupiah;
                }

                function calculatePayment() {
                    const rawCash = cashInput.value.replace(/\./g, '');
                    const cash = parseInt(rawCash) || 0;

                    if (cart.length === 0) {
                        changeDisplay.innerText = 'Rp 0';
                        changeDisplay.className = 'text-lg font-bold text-gray-400';
                        checkoutBtn.disabled = true;
                        return;
                    }

                    const change = cash - currentTotal;

                    if (cash >= currentTotal && currentTotal > 0) {
                        changeDisplay.innerText = formatRupiah(change);
                        changeDisplay.className = 'text-lg font-bold text-green-600';
                        checkoutBtn.disabled = false;
                    } else {
                        changeDisplay.innerText = 'Uang Kurang!';
                        changeDisplay.className = 'text-lg font-bold text-red-600';
                        checkoutBtn.disabled = true;
                    }
                }

                cashInput.addEventListener('input', function() {
                    this.value = formatRibuan(this.value);
                    calculatePayment();
                });

                function updateCartDOM() {
                    if (cart.length === 0) {
                        cartContainer.innerHTML =
                            `<div class="py-12 text-center flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                                    <svg class="w-8 h-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <p class="text-sm font-medium text-gray-400">Keranjang masih kosong</p>
                            </div>`;
                        subtotalEl.innerText = 'Rp 0';
                        totalEl.innerText = 'Rp 0';
                        currentTotal = 0;
                        calculatePayment();
                        return;
                    }

                    cartContainer.innerHTML = '';
                    let subtotal = 0;

                    cart.forEach(item => {
                        subtotal += item.price * item.quantity;
                        const itemEl = document.createElement('div');
                        itemEl.className =
                            'flex items-center justify-between border-b border-gray-50 py-3 gap-2 w-full';

                        itemEl.innerHTML = `
                    <div class="flex items-center gap-3 w-3/5 min-w-0">
                        <div class="w-12 h-12 bg-gray-50 border border-gray-100 rounded-lg flex items-center justify-center p-1.5 shrink-0">
                            <img src="${item.image}" alt="${item.name}" class="w-full h-full object-contain rounded">
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-bold text-gray-800 truncate" title="${item.name}">${item.name}</p>
                            <p class="text-[10px] text-gray-400 truncate mt-0.5" title="${item.ingredientsList}">Bahan: ${item.ingredientsList || '-'}</p>
                            <p class="text-xs font-semibold text-[#2D5A34] mt-1">${formatRupiah(item.price)}</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-2 w-2/5 shrink-0">
                        <div class="flex items-center bg-gray-50 border border-gray-200 rounded-lg p-0.5 shadow-sm">
                            <button type="button" class="w-7 h-7 flex items-center justify-center text-xs font-bold text-gray-600 hover:bg-white hover:shadow-sm rounded transition-all minus-qty" data-id="${item.id}">-</button>
                            <span class="text-xs font-bold text-gray-800 min-w-[20px] text-center">${item.quantity}</span>
                            <button type="button" class="w-7 h-7 flex items-center justify-center text-xs font-bold text-gray-600 hover:bg-white hover:shadow-sm rounded transition-all plus-qty" data-id="${item.id}">+</button>
                        </div>
                        <button type="button" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors remove-item" data-id="${item.id}" title="Hapus item">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 pointer-events-none">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                        </button>
                    </div>
                `;
                        cartContainer.appendChild(itemEl);
                    });

                    currentTotal = subtotal;
                    subtotalEl.innerText = formatRupiah(subtotal);
                    totalEl.innerText = formatRupiah(currentTotal);

                    calculatePayment();
                }

                // Event Add to Cart
                document.addEventListener('click', function(e) {
                    const button = e.target.closest('.add-to-cart');
                    // Jika tombol didisable (karena stok habis), batalkan event
                    if (button && !button.disabled) {
                        const id = button.getAttribute('data-id');
                        const name = button.getAttribute('data-name');
                        const price = parseInt(button.getAttribute('data-price'));
                        const image = button.getAttribute('data-image');
                        const ingredientsList = button.getAttribute('data-ingredients-list');

                        const existingItem = cart.find(item => String(item.id) === String(id));
                        if (existingItem) {
                            existingItem.quantity += 1;
                        } else {
                            cart.push({
                                id,
                                name,
                                price,
                                image,
                                quantity: 1,
                                ingredientsList
                            });
                        }
                        updateCartDOM();
                    }
                });

                // Event Kasir (+ , - , Trash)
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
                            if (cart[itemIndex].quantity <= 0) cart.splice(itemIndex, 1);
                        }
                        updateCartDOM();
                        return;
                    }

                    const removeBtn = e.target.closest('.remove-item');
                    if (removeBtn) {
                        const id = removeBtn.getAttribute('data-id');
                        const itemIndex = cart.findIndex(item => String(item.id) === String(id));
                        if (itemIndex > -1) cart.splice(itemIndex, 1);
                        updateCartDOM();
                        return;
                    }
                });

                // Event Checkout
                if (checkoutBtn) {
                    checkoutBtn.addEventListener('click', function() {
                        if (cart.length === 0) return;

                        const rawCash = cashInput.value.replace(/\./g, '');
                        const cash = parseInt(rawCash) || 0;

                        if (cash < currentTotal) {
                            alert("Uang pembayaran tidak cukup!");
                            return;
                        }

                        const originalText = this.innerHTML;
                        this.disabled = true;
                        this.innerHTML = '<span class="font-medium">Memproses Transaksi...</span>';

                        fetch("{{ route('checkout.process') }}", {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                        .getAttribute('content')
                                },
                                body: JSON.stringify({
                                    cart: cart,
                                    cash_received: cash,
                                    change_returned: cash - currentTotal
                                })
                            })
                            .then(async response => {
                                const data = await response.json();
                                if (!response.ok) throw new Error(data.message ||
                                    'Terjadi kesalahan sistem.');
                                return data;
                            })
                            .then(data => {
                                if (data.success) {
                                    alert(data.message);
                                    cart = [];
                                    cashInput.value = '';
                                    location.reload();
                                }
                            })
                            .catch(error => {
                                console.error('Error Checkout:', error);
                                alert(error.message);
                                this.disabled = false;
                                this.innerHTML = originalText;
                            });
                    });
                }
            });
        </script>
    @endpush
@endsection
