<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends Controller
{
    /**
     * Display a listing of the bahan baku.
     */
    public function index(): View
    {
        $bahanBaku = BahanBaku::all();

        return view('inventory.index', [
        'bahanBaku' => $bahanBaku,
        ]);
    }

    /**
     * Show the form for creating a new bahan baku.
     */
    public function create(): View
    {
        return view('inventory.create');
    }

    /**
     * Store a newly created bahan baku in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            // UBAH 'unique:bahan_bakus' menjadi 'unique:bahan_baku'
            'nama_bahan' => 'required|string|max:255|unique:bahan_baku',
            'kategori' => 'required|string|max:100',
            'satuan' => 'required|string|max:100',
            'stok_awal' => 'required|integer|min:0',
            'stok_saat_ini' => 'required|integer|min:0',
            'stok_minimum' => 'required|integer|min:0',
        ]);

        BahanBaku::create($validated);

        return redirect()->route('inventory.index')
                       ->with('success', 'Bahan baku berhasil ditambahkan!');
    }

    /**
     * Remove the specified bahan baku from storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $validated = $request->validate([
            'nama_bahan' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'satuan' => 'required|string|max:100',
            'stok_awal' => 'required|integer|min:0',
            'stok_saat_ini' => 'required|integer|min:0',
            'stok_minimum' => 'required|integer|min:0',
        ]);

        $bahanBaku = BahanBaku::findOrFail($id);
        $bahanBaku->update($validated);

        return redirect()->route('inventory.index')
                         ->with('success', 'Bahan baku berhasil diperbarui!');
    }

    /**
     * Remove the specified bahan baku from storage.
     */
    public function destroy($id): RedirectResponse
    {
        $bahanBaku = BahanBaku::findOrFail($id);
        $bahanBaku->delete();

        return redirect()->route('inventory.index')
                         ->with('success', 'Bahan baku berhasil dihapus!');
    }
}
