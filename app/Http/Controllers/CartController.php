<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\BahanBaku;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Menampilkan halaman keranjang belanja
     */
    public function index(): View
    {
        return view('keranjang.index', [
            'products' => Product::query()->latest()->get(),
            'all_ingredients' => \App\Models\BahanBaku::all(), // Tambah baris ini
        ]);
    }

    public function create(): View
    {
        return view('keranjang.create');
    }

    /**
     * Memproses transaksi (Checkout) dari Array Keranjang via AJAX
     */
    public function store(Request $request): JsonResponse
    {
        // 1. Validasi input keranjang dan metode pembayaran
        $request->validate([
            'cart'            => 'required|array',
            'cart.*.id'       => 'required|exists:products,id',
            'cart.*.quantity' => 'required|integer|min:1',
            'payment_method'  => 'nullable|string'
        ]);

        DB::beginTransaction();

        try {
            $subtotal = 0;
            $tax = 0;
            $orderItemsData = []; // Wadah sementara biar gak query 2x

            // 2. Loop Pertama: Verifikasi Resep & Hitung Finansial
            foreach ($request->cart as $item) {
                $product = Product::with('ingredients')->findOrFail($item['id']);

                if ($product->ingredients->isEmpty()) {
                    throw new \Exception("Sistem Gagal: Produk '{$product->name}' belum memiliki data resep di database!");
                }

                // Kalkulasi harga dari backend (aman dari manipulasi hacker)
                $itemTotalPrice = $product->price * $item['quantity'];
                $subtotal += $itemTotalPrice;

                // Simpan struktur item untuk dieksekusi setelah nota induk dibuat
                $orderItemsData[] = [
                    'product'  => $product,
                    'quantity' => $item['quantity'],
                    'price'    => $product->price // Snapshot harga saat transaksi
                ];
            }

            $totalPrice = $subtotal + $tax;

            // 3. Buat Data Transaksi Induk (Tabel orders)
            $order = \App\Models\Order::create([
                'invoice_number' => 'INV-' . date('Ymd') . '-' . rand(1000, 9999),
                'user_id'        => auth()->id(), // Mencatat kasir yang bertugas
                'subtotal'       => $subtotal,
                'tax'            => $tax,
                'total_price'    => $totalPrice,
                'payment_method' => $request->payment_method ?? 'cash',
                'status'         => 'completed',
            ]);

            // 4. Loop Kedua: Simpan Rincian Belanja & Eksekusi Pemotongan Stok
            foreach ($orderItemsData as $data) {
                $product = $data['product'];
                $qty = $data['quantity'];

                // A. Catat ke tabel order_items
                \App\Models\OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $product->id,
                    'quantity'   => $qty,
                    'price'      => $data['price'],
                ]);

                // B. Potong stok di tabel bahan_baku
                foreach ($product->ingredients as $ingredient) {
                    $totalKebutuhan = $ingredient->pivot->quantity_needed * $qty;

                    if ($ingredient->stok_saat_ini < $totalKebutuhan) {
                        throw new \Exception("Stok '{$ingredient->nama_bahan}' tidak cukup untuk {$product->name}! Butuh: {$totalKebutuhan}, Sisa: {$ingredient->stok_saat_ini}");
                    }

                    $ingredient->stok_saat_ini -= $totalKebutuhan;
                    $ingredient->save();
                }
            }

            // 5. Kunci semua perubahan database
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi sukses! Nota tercetak dan stok otomatis dipotong.'
            ]);
        } catch (\Exception $e) {
            // Batalkan pencatatan uang DAN pemotongan barang jika ada 1 saja error
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Menghapus item (Bisa disesuaikan nanti fungsinya)
     */
    public function destroy($id): RedirectResponse
    {
        // Logika hapus item keranjang lu di sini nanti
        return redirect()->route('keranjang.index');
    }
}
