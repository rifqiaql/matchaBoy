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
            'satuan' => 'required|string|max:100',
            'stok_awal' => 'required|integer|min:0',
            'stok_saat_ini' => 'required|integer|min:0',
        ]);

        BahanBaku::create($validated);

        return redirect()->route('inventory.index')
                       ->with('success', 'Bahan baku berhasil ditambahkan!');
    }
}
