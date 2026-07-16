<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\BahanBaku;
use Illuminate\Http\Request;

class IngredientController extends Controller
{
    /**
     * Menyimpan atau memperbarui resep bahan baku untuk suatu produk
     */
    public function store(Request $request, $productId)
    {
        $request->validate([
            'ingredients' => 'required|array',
            'ingredients.*.bahan_baku_id' => 'required|exists:bahan_baku,id',
            'ingredients.*.quantity_needed' => 'required|numeric|min:0.01',
        ]);

        $product = Product::findOrFail($productId);

        // Siapkan data untuk disinkronkan ke tabel pivot
        $syncData = [];
        foreach ($request->ingredients as $item) {
            $syncData[$item['bahan_baku_id']] = [
                'quantity_needed' => $item['quantity_needed']
            ];
        }

        // sync() otomatis menghapus resep lama dan menggantinya dengan yang baru dimasukkan
        $product->ingredients()->sync($syncData);

        return response()->json([
            'success' => true,
            'message' => 'Resep produk berhasil diperbarui!'
        ]);
    }
}
