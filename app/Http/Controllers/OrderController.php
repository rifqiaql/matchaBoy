<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\BahanBaku;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Memproses transaksi (Checkout) dari Keranjang & Pemotongan Stok BOM Presisi
     */
    public function checkout(Request $request)
    {
        // 1. Validasi input: JANGAN PERNAH menerima harga (price) dari Frontend!
        $request->validate([
            'cart' => 'required|array',
            'cart.*.id' => 'required|exists:products,id',
            'cart.*.quantity' => 'required|integer|min:1',
        ]);

        // Mulai Database Transaction demi keamanan data (Jika 1 gagal, semua dibatalkan)
        DB::beginTransaction();

        try {
            $subtotal = 0;
            $orderItemsData = []; // Wadah sementara untuk eksekusi item

            // 2. Loop Pertama: Hitung Total Uang dengan Harga Asli dari Database
            foreach ($request->cart as $item) {
                $product = Product::findOrFail($item['id']);

                // Kalkulasi harga AMAN (Menggunakan harga asli DB)
                $itemTotalPrice = $product->price * $item['quantity'];
                $subtotal += $itemTotalPrice;

                $orderItemsData[] = [
                    'product'  => $product,
                    'quantity' => $item['quantity'],
                    'price'    => $product->price
                ];
            }

            // Hitung Pajak dan Total Akhir
            $tax = $subtotal * 0.10; // Pajak 10%
            $totalPrice = $subtotal + $tax;

            // 3. Generate Nomor Invoice
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(2)));

            // 4. Simpan Data ke Tabel `orders` (Kepala Struk)
            $order = Order::create([
                'invoice_number' => $invoiceNumber,
                'user_id' => Auth::id() ?? 1,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total_price' => $totalPrice,
                'status' => 'completed',
                'payment_method' => $request->payment_method ?? 'cash'
            ]);

            // Ambil semua data bahan baku dari database sekaligus untuk performa & validasi
            $semuaBahan = BahanBaku::all();

            // 5. Loop Kedua: Simpan Detail Transaksi & Potong Stok Gudang Secara Presisi
            foreach ($orderItemsData as $data) {
                $product = $data['product'];
                $qty = $data['quantity'];

                // Simpan tiap baris produk ke tabel `order_items`
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'price' => $data['price']
                ]);

                // POTONG STOK OTOMATIS BERDASARKAN RESEP BAKU (BOM) MATCHA BOY
                $namaMenu = strtolower($product->name);

                // Semua minuman wajib menggunakan 3 bahan dasar ini (Base BOM):
                // 1. Bubuk Matcha = 8 Gram / Cup
                $this->potongStokBahan($semuaBahan, 'matcha', 8 * $qty);
                // 2. Susu Full Cream = 100 Mililiter / Cup
                $this->potongStokBahan($semuaBahan, 'full cream', 100 * $qty);
                // 3. Susu Kental Manis (SKM) = 30 Gram / Cup
                $this->potongStokBahan($semuaBahan, ['skm', 'kental manis'], 30 * $qty);

                // Tambahan Toping Khusus untuk menu tertentu:
                if (str_contains($namaMenu, 'strawberry')) {
                    // Selai Strawberry = 15 Gram / Cup
                    $this->potongStokBahan($semuaBahan, 'strawberry', 15 * $qty);
                } elseif (str_contains($namaMenu, 'caramel')) {
                    // Sirup Caramel = 15 Gram / Cup
                    $this->potongStokBahan($semuaBahan, 'caramel', 15 * $qty);
                }
            }

            // 6. Jika semua baris produk aman dan stok cukup, kunci perubahan ke database
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi Berhasil, Stok Gudang Otomatis Berkurang!',
                'invoice_number' => $order->invoice_number
            ], 200);

        } catch (\Exception $e) {
            // Batalkan semua operasi database jika ada 1 saja yang melanggar rule / stok kurang
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Helper privat untuk memotong stok dengan pencarian kebal typo & validasi minus
     */
    private function potongStokBahan($collectionBahan, $keyword, $totalKurang)
    {
        $keywords = is_array($keyword) ? $keyword : [$keyword];

        $bahan = $collectionBahan->first(function ($item) use ($keywords) {
            foreach ($keywords as $kw) {
                if (str_contains(strtolower($item->nama_bahan), strtolower($kw))) {
                    return true;
                }
            }
            return false;
        });

        if (!$bahan) {
            $namaDicari = is_array($keyword) ? implode(' / ', $keyword) : $keyword;
            throw new \Exception("Sistem Gagal: Bahan baku '{$namaDicari}' tidak ditemukan di Gudang.");
        }

        if ($bahan->stok_saat_ini < $totalKurang) {
            throw new \Exception("Stok bahan '{$bahan->nama_bahan}' tidak cukup! Butuh: {$totalKurang} {$bahan->satuan}, Sisa: {$bahan->stok_saat_ini} {$bahan->satuan}");
        }

        $bahan->stok_saat_ini -= $totalKurang;
        $bahan->save();
    }
}