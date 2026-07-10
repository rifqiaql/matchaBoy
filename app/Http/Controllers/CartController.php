<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\BahanBaku; // Pastiin model ini di-import
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Menampilkan halaman keranjang belanja
     */
    public function index(): View
    {
        // Catatan: Sementara kalau lu belum bikin tabel 'carts' atau session keranjang,
        // kita tetep tampilin produk buat keperluan testing halaman lu.
        return view('keranjang.index', [
            'products' => Product::query()->latest()->get(),
        ]);
    }

    public function create(): View
    {
        return view('keranjang.create');
    }

    /**
     * Memproses transaksi / mengurangi stok gudang berdasarkan produk yang dibeli
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validasi input dari form keranjang/checkout lu
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        // 2. Gunakan DB Transaction biar aman. Kalau di tengah jalan stok kurang, database otomatis di-rollback
        DB::beginTransaction();

        try {
            // Ambil data produk beserta relasi bahan bakunya (ingredients)
            $product = Product::with('ingredients')->findOrFail($request->product_id);

            // 3. Loop setiap bahan baku yang menyusun produk ini
            foreach ($product->ingredients as $ingredient) {
                $totalKebutuhan = $ingredient->pivot->jumlah * $request->quantity;

                if ($ingredient->stok < $totalKebutuhan) {
                    throw new \Exception("Stok bahan baku '{$ingredient->nama_bahan}' di gudang tidak cukup!");
                }

                // Ganti decrement dengan cara matematika manual ini:
                $ingredient->stok = $ingredient->stok - $totalKebutuhan;
                $ingredient->save();
            }

            // Simpan transaksi sukses
            DB::commit();

            return redirect()->route('keranjang.index')->with('success', 'Produk berhasil diproses dan stok gudang telah dikurangi!');
        } catch (\Exception $e) {
            // Batalkan semua perubahan jika ada error/stok kurang
            DB::rollBack();

            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
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
