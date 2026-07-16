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
        // 1. Validasi input JSON dari form keranjang/checkout (Validasi Array)
        $request->validate([
            'cart'            => 'required|array',
            'cart.*.id'       => 'required|exists:products,id',
            'cart.*.quantity' => 'required|integer|min:1',
        ]);

        // 2. Gunakan DB Transaction biar aman
        DB::beginTransaction();

        try {
            // 3. Looping setiap item yang ada di dalam keranjang belanja
            foreach ($request->cart as $item) {
                // Ambil data produk beserta relasi bahan bakunya (ingredients)
                $product = Product::with('ingredients')->findOrFail($item['id']);

                // =========================================================
                // BLOK INI YANG LU LEWATKAN KEMARIN. JANGAN DIHAPUS!
                // =========================================================
                if ($product->ingredients->isEmpty()) {
                    throw new \Exception("Sistem Gagal: Produk '{$product->name}' belum memiliki data resep (bahan baku) di database!");
                }
                // =========================================================

                // 4. Loop setiap bahan baku yang menyusun produk ini
                foreach ($product->ingredients as $ingredient) {

                    // PENTING: Gunakan 'quantity_needed' sesuai tabel product_ingredients
                    $totalKebutuhan = $ingredient->pivot->quantity_needed * $item['quantity'];

                    // PENTING: Gunakan 'stok_saat_ini' sesuai tabel bahan_baku
                    if ($ingredient->stok_saat_ini < $totalKebutuhan) {
                        throw new \Exception("Stok bahan baku '{$ingredient->nama_bahan}' tidak cukup untuk membuat {$product->name}!");
                    }

                    // PENTING: Potong kolom 'stok_saat_ini'
                    $ingredient->stok_saat_ini = $ingredient->stok_saat_ini - $totalKebutuhan;
                    $ingredient->save();
                }
            }

            // Simpan transaksi sukses
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil diproses dan stok gudang telah dikurangi!'
            ]);
        } catch (\Exception $e) {
            // Batalkan semua perubahan jika ada error/stok kurang
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
