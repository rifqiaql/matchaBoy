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
     * Menyimpan produk baru ke database (AJAX POST)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:' . self::MAX_IMAGE_SIZE_KB,
        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->price = $request->price;

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->storeAs('products', $imageName, 'public');
            $product->image = $imageName;
        }

        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Produk baru berhasil ditambahkan ke database!'
        ]);
    }

    /**
     * Memperbarui data produk (AJAX PUT)
     */
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan.'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:' . self::MAX_IMAGE_SIZE_KB,
        ]);

        $product->name = $request->name;
        $product->price = $request->price;

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

        return response()->json([
            'success' => true,
            'message' => 'Data produk berhasil diperbarui!'
        ]);
    }

    /**
     * Menghapus produk dari database (AJAX DELETE)
     */
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan.'], 404);
        }

        // Hapus file gambar dari storage disk jika ada
        if ($product->image && Storage::disk('public')->exists('products/' . $product->image)) {
            Storage::disk('public')->delete('products/' . $product->image);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produk "' . $product->name . '" berhasil dihapus dari katalog.'
        ]);
    }
}
