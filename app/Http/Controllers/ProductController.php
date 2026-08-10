<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\BahanBaku;
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
        $products = Product::latest()->get();
        
        // Data bahan baku dikirim untuk opsi dropdown di modal (Create/Edit)
        $all_ingredients = BahanBaku::all();

        return view('keranjang.index', compact('products', 'all_ingredients'));
    }

    /**
     * Menyediakan data produk beserta resepnya untuk modal edit (AJAX GET)
     */
    public function edit($id)
    {
        // Kueri ini SUDAH BENAR menarik relasi resep. 
        // Pastikan model Product lu punya: return $this->belongsToMany(...)->withPivot('quantity_needed');
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
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:' . self::MAX_IMAGE_SIZE_KB,
            'ingredients' => 'required|array|min:1',
            'ingredients.*.bahan_baku_id' => 'required|exists:bahan_baku,id',
            'ingredients.*.quantity_needed' => 'required|numeric|min:0.01',
        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->price = $request->price;
        $product->category = $request->category;

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->storeAs('products', $imageName, 'public');
            $product->image = $imageName;
        }

        $product->save();

        $syncData = [];
        foreach ($request->ingredients as $item) {
            $syncData[$item['bahan_baku_id']] = [
                'quantity_needed' => $item['quantity_needed']
            ];
        }

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
        // REVISI: Ubah findOrFail menjadi find agar blok if(!$product) bisa merespons JSON dengan benar
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan.'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'category' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:' . self::MAX_IMAGE_SIZE_KB,
            'ingredients' => 'nullable|array',
            'ingredients.*.bahan_baku_id' => 'required|exists:bahan_baku,id',
            'ingredients.*.quantity_needed' => 'required|numeric|min:0.01',
        ]);

        $product->name = $request->name;
        $product->price = $request->price;

        if ($request->has('category')) {
            $product->category = $request->category;
        }

        if ($request->hasFile('image')) {
            if ($product->image && Storage::disk('public')->exists('products/' . $product->image)) {
                Storage::disk('public')->delete('products/' . $product->image);
            }

            $imageName = time() . '.' . $request->image->extension();
            $request->image->storeAs('products', $imageName, 'public');
            $product->image = $imageName;
        }

        $product->save();

        if ($request->has('ingredients')) {
            $syncData = [];
            foreach ($request->ingredients as $item) {
                $syncData[$item['bahan_baku_id']] = [
                    'quantity_needed' => $item['quantity_needed']
                ];
            }
            $product->ingredients()->sync($syncData);
        } else {
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
        // REVISI: Ubah findOrFail menjadi find agar blok if(!$product) bisa merespons JSON dengan benar
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan.'], 404);
        }

        if ($product->image && Storage::disk('public')->exists('products/' . $product->image)) {
            Storage::disk('public')->delete('products/' . $product->image);
        }

        $product->ingredients()->detach();
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produk "' . $product->name . '" berhasil dihapus dari katalog.'
        ]);
    }
}