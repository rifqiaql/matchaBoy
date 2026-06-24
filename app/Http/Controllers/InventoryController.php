<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InventoryController extends Controller
{
    /**
     * Display a listing of the bahan baku.
     */
    public function index(Request $request): View
    {
        $query = BahanBaku::query();

        if ($request->filled('search')) {
            $query->where('nama_bahan', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $bahanBaku = $query->get();

        return view('inventory.index', [
            'bahanBaku' => $bahanBaku,
        ]);
    }

    /**
     * Export filtered inventory to CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $query = BahanBaku::query();

        if ($request->filled('search')) {
            $query->where('nama_bahan', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $bahanBaku = $query->get();
        $filename = 'inventory-export-' . now()->format('YmdHis') . '.csv';

        return response()->streamDownload(function () use ($bahanBaku) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Nama Bahan', 'Kategori', 'Stok Saat Ini', 'Satuan', 'Batas Limit']);

            foreach ($bahanBaku as $item) {
                fputcsv($handle, [
                    $item->nama_bahan,
                    $item->kategori,
                    $item->stok_saat_ini,
                    $item->satuan,
                    $item->stok_minimum,
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
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
                       ->with('notify', ['success', 'Bahan baku berhasil ditambahkan!', 'type' => 'success']);
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
                         ->with('notify', ['success', 'Bahan baku berhasil diperbarui!', 'type' => 'success']);
    }

    /**
     * Remove the specified bahan baku from storage.
     */
    public function destroy($id): RedirectResponse
    {
        $bahanBaku = BahanBaku::findOrFail($id);
        $bahanBaku->delete();

        return redirect()->route('inventory.index')
                         ->with('notify', ['success', 'Bahan baku berhasil dihapus!', 'type' => 'success']);
    }
}
