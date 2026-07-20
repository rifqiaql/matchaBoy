<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    private const MAX_IMAGE_SIZE_KB = 5120;

    /**
     * Menampilkan halaman kasir / keranjang dengan data dinamis dari DB
     */
    public function index()
    {
        // Mengambil semua data produk dari database
        $products = Product::all();

        // Mengirim data produk ke file index.blade.php di folder keranjang
        return view('keranjang.index', compact('products'));
    }

    /**
     * Menyediakan data produk beserta resepnya untuk modal edit (AJAX GET)
     */
    public function edit($id)
    {
        // Ambil produk beserta relasi ingredients/resepnya
        $product = Product::with('ingredients')->find($id);

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan.'], 404);
        }

        return response()->json($product);
    }

    /**
     * Menyimpan produk baru ke database beserta resepnya (AJAX POST)
     */
    public function store(Request $request)
    {
        // 1. Validasi Produk & Resep sekaligus
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string',
            // 'image' => 'nullable|image|max:5120',

            // Validasi Resep
            'ingredients' => 'required|array|min:1',
            'ingredients.*.bahan_baku_id' => 'required|exists:bahan_baku,id',
            'ingredients.*.quantity_needed' => 'required|numeric|min:0.01',
        ]);

        // 2. Simpan Produk Dulu
        $product = Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'category' => $request->category,
            // 'image' => $imagePath, // Sesuaikan dengan logika upload gambar lu
        ]);

        // 3. Eksekusi Resep (Menggunakan logika lu yang sudah benar)
        $syncData = [];
        foreach ($request->ingredients as $item) {
            $syncData[$item['bahan_baku_id']] = [
                'quantity_needed' => $item['quantity_needed']
            ];
        }

        // Relasikan produk dengan bahan bakunya ke tabel pivot
        $product->ingredients()->sync($syncData);

        return response()->json([
            'success' => true,
            'message' => 'Produk dan Resep berhasil ditambahkan!'
        ]);
    }

    /**
     * Memperbarui data produk beserta resepnya (AJAX PUT)
     */
    public function update(Request $request, $id)
    {
        $product = Product::query()->findOrFail($id);

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan.'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'category' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:' . self::MAX_IMAGE_SIZE_KB,
            'ingredients' => 'nullable|array',
            'ingredients.*.bahan_baku_id' => 'required|exists:bahan_baku,id',
            'ingredients.*.quantity_needed' => 'required|numeric|min:0.01',
        ]);

        $product->name = $request->name;
        $product->price = $request->price;
        // Pastikan kategori ikut terupdate

        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada di storage
            if ($product->image && Storage::disk('public')->exists('products/' . $product->image)) {
                Storage::disk('public')->delete('products/' . $product->image);
            }

            $imageName = time() . '.' . $request->image->extension();
            $request->image->storeAs('products', $imageName, 'public');
            $product->image = $imageName;
        }

        $product->save();

        // PROSES UTAMA EDIT: Update resep dinamis (resep lama dihapus, diganti yang baru)
        if ($request->has('ingredients')) {
            $syncData = [];
            foreach ($request->ingredients as $item) {
                $syncData[$item['bahan_baku_id']] = [
                    'quantity_needed' => $item['quantity_needed']
                ];
            }
            $product->ingredients()->sync($syncData);
        } else {
            // Jika semua baris resep dihapus saat edit, kosongkan tabel pivot
            $product->ingredients()->detach();
        }

        return response()->json([
            'success' => true,
            'message' => 'Data produk dan komponen resep berhasil diperbarui!'
        ]);
    }

    /**
     * Menghapus produk dari database (AJAX DELETE)
     */
    public function destroy($id)
    {
        $product = Product::query()->findOrFail($id);

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan.'], 404);
        }

        // Hapus file gambar dari storage disk jika ada
        if ($product->image && Storage::disk('public')->exists('products/' . $product->image)) {
            Storage::disk('public')->delete('products/' . $product->image);
        }

        // Hapus relasi resep di tabel pivot dulu agar tidak terjadi data yatim (foreign key constraint)
        $product->ingredients()->detach();

        Product::destroy($id);

        return response()->json([
            'success' => true,
            'message' => 'Produk "' . $product->name . '" berhasil dihapus dari katalog.'
        ]);
    }
}
