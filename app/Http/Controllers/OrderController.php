<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductIngredient;
use App\Models\BahanBaku; // Memanggil model bahan baku milik teman lo
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public function checkout(Request $request)
    {
        // 1. Validasi input data keranjang yang dikirim oleh JavaScript (Front-End)
        $request->validate([
            'cart' => 'required|array',
            'cart.*.id' => 'required|integer',
            'cart.*.quantity' => 'required|integer|min:1',
            'cart.*.price' => 'required|integer',
        ]);

        // Mulai Database Transaction demi keamanan data
        DB::beginTransaction();

        try {
            // 2. Hitung Total Uang di Sisi Backend (Biar gak bisa di-hack lewat browser)
            $subtotal = 0;
            foreach ($request->cart as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }
            $tax = $subtotal * 0.10; // Pajak 10% sesuai tampilan kasir lo
            $totalPrice = $subtotal + $tax;

            // 3. Generate Nomor Invoice Otomatis (Contoh: INV-20260706-A1B2)
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(2)));

            // 4. Simpan Data ke Tabel `orders` (Kepala Struk)
            $order = Order::create([
                'invoice_number' => $invoiceNumber,
                'user_id' => Auth::id() ?? 1, // Memakai ID kasir yang login (default 1 jika belum buat sistem login)
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total_price' => $totalPrice,
            ]);

            // 5. Looping Isi Keranjang: Simpan Detail & Potong Stok Gudang Teman Lo
            foreach ($request->cart as $item) {

                // Simpan tiap baris produk ke tabel `order_items`
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ]);

                // Ambil daftar resep komplit menggunakan DB builder
                $recipes = DB::table('product_ingredients')
                    ->where('product_id', '=', $item['id'])
                    ->get();

                foreach ($recipes as $recipe) {
                    // Hitung total kebutuhan bahan (takaran resep x jumlah cup)
                    $totalNeeded = $recipe->quantity_needed * $item['quantity'];

                    // Cari data bahan mentah di tabel gudang menggunakan DB builder
                    $bahan = DB::table('bahan_baku')
                        ->where('id', '=', $recipe->ingredient_id)
                        ->first();

                    // Validasi: Jika bahan gak ketemu atau stok di gudang tidak cukup
                    if (!$bahan || $bahan->stok_saat_ini < $totalNeeded) {
                        // Batalkan semua operasi database
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => "Stok bahan '" . ($bahan ? $bahan->nama_bahan : 'Tidak Diketahui') . "' di gudang tidak cukup!"
                        ], 400);
                    }

                    // POTONG STOK OTOMATIS langsung ke tabel menggunakan query builder
                    DB::table('bahan_baku')
                        ->where('id', '=', $recipe->ingredient_id)
                        ->decrement('stok_saat_ini', $totalNeeded);
                }
            }

            // Jika semua baris produk aman dan stok cukup, kunci perubahan ke database
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi Berhasil, Stok Gudang Otomatis Berkurang!',
                'invoice_number' => $order->invoice_number
            ], 200);
        } catch (\Exception $e) {
            // Jika ada error/bug codingan di tengah jalan, batalkan semua data biar gak berantakan
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    public function storeProduct(Request $request)
    {
        // 1. Validasi input form secara ketat sesuai gambar modal lo
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120', // Batasan maks 5MB
        ]);

        try {
            $imageName = null;

            // 2. Proses upload gambar jika admin memilih file foto produk
            if ($request->hasFile('image')) {
                $imageFile = $request->file('image');
                // Beri nama unik berdasarkan waktu agar tidak bentrok (contoh: 171983012.png)
                $imageName = time() . '.' . $imageFile->getClientOriginalExtension();
                // Simpan fisik file ke dalam folder publik agar bisa diakses dari browser
                Storage::disk('public')->putFileAs('products', $imageFile, $imageName);
            }

            // 3. Masukkan data ke tabel `products` menggunakan Query Builder murni
            DB::table('products')->insert([
                'name' => $request->name,
                'price' => $request->price,
                'image' => $imageName,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Produk baru berhasil ditambahkan ke katalog!'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan produk: ' . $e->getMessage()
            ], 500);
        }
    }
}
