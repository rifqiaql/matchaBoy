<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Memproses transaksi (Checkout) dari Keranjang
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
            $orderItemsData = []; // Wadah sementara

            // 2. Loop Pertama: Hitung Total Uang dengan Harga Asli dari Database
            foreach ($request->cart as $item) {
                // Tarik data produk beserta resepnya langsung dari DB
                $product = Product::with('ingredients')->findOrFail($item['id']);

                if ($product->ingredients->isEmpty()) {
                    throw new \Exception("Sistem Gagal: Produk '{$product->name}' belum memiliki resep (BOM) di gudang.");
                }

                // Kalkulasi harga AMAN
                $itemTotalPrice = $product->price * $item['quantity'];
                $subtotal += $itemTotalPrice;

                // Simpan struktur item untuk dieksekusi setelah order induk dibuat
                $orderItemsData[] = [
                    'product'  => $product,
                    'quantity' => $item['quantity'],
                    'price'    => $product->price // Harga asli
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

            // 5. Loop Kedua: Simpan Detail Transaksi & Potong Stok Gudang
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

                // POTONG STOK OTOMATIS BERDASARKAN RESEP (BOM)
                foreach ($product->ingredients as $ingredient) {
                    // Hitung total kebutuhan bahan (takaran resep x jumlah cup pesanan)
                    $totalNeeded = $ingredient->pivot->quantity_needed * $qty;

                    // Validasi: Jika stok di gudang tidak cukup
                    if ($ingredient->stok_saat_ini < $totalNeeded) {
                        throw new \Exception("Stok bahan '{$ingredient->nama_bahan}' tidak cukup untuk memproses '{$product->name}'! Butuh: {$totalNeeded}, Sisa: {$ingredient->stok_saat_ini}");
                    }

                    // Kurangi stok dan simpan
                    $ingredient->stok_saat_ini -= $totalNeeded;
                    $ingredient->save();
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
            // Batalkan semua operasi database jika ada 1 saja yang melanggar rule
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
