<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use App\Models\StokMasuk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
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
        $filename = 'Laporan_Gudang_MatchaBoy_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($bahanBaku) {
            $handle = fopen('php://output', 'w');

            // 1. Definisi Header yang Komprehensif
            $columns = [
                'ID Bahan',
                'Nama Bahan',
                'Kategori',
                'Stok Awal (Kapasitas)',
                'Stok Saat Ini',
                'Satuan',
                'Batas Minimum',
                'Status Kondisi',
                'Terakhir Diupdate'
            ];

            // 2. Gunakan Titik Koma (;) agar rapi di Microsoft Excel
            fputcsv($handle, $columns, ';');

            foreach ($bahanBaku as $item) {
                // 3. Penentuan Status Kondisi Otomatis
                $status = 'Aman';
                if ($item->stok_saat_ini <= 0) {
                    $status = 'Habis / Defisit';
                } elseif ($item->stok_saat_ini <= $item->stok_minimum) {
                    $status = 'Kritis (Segera Restock)';
                }

                fputcsv($handle, [
                    $item->id,
                    $item->nama_bahan,
                    $item->kategori,
                    $item->stok_awal,
                    $item->stok_saat_ini,
                    $item->satuan,
                    $item->stok_minimum,
                    $status,
                    $item->updated_at ? $item->updated_at->format('Y-m-d H:i:s') : '-'
                ], ';');
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
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
    public function store(Request $request)
    {
        // 1. Validasi input yang masuk dari form baru
        $request->validate([
            'nama_bahan'      => 'required|string|max:255',
            'kategori'        => 'required|string',
            'satuan'          => 'required|string',
            'jumlah_kemasan'  => 'required|numeric|min:0',
            'isi_per_kemasan' => 'required|numeric|min:0',
            'stok_minimum'    => 'required|numeric|min:0',
        ]);

        // 2. Kalkulasi cerdas di belakang layar
        $totalStok = $request->jumlah_kemasan * $request->isi_per_kemasan;

        // 3. Simpan ke database
        BahanBaku::create([
            'nama_bahan'    => $request->nama_bahan,
            'kategori'      => $request->kategori,
            'satuan'        => $request->satuan,
            'stok_awal'     => $totalStok,
            'stok_saat_ini' => $totalStok,
            'stok_minimum'  => $request->stok_minimum,
        ]);

        return redirect()->route('inventory.index')->with('success', 'Bahan baku berhasil ditambahkan!');
    }

    /**
     * Update the specified bahan baku in storage.
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

    /**
     * Memproses Restock (Barang Masuk) dengan Database Transaction dan Audit Trail
     */
    public function tambahStok(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'jumlah_kemasan' => 'required|numeric|min:0.1',
            'isi_per_kemasan' => 'required|numeric|min:0.1',
            'tanggal_kedaluwarsa' => 'nullable|date',
            'catatan' => 'nullable|string|max:255'
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                $bahan = BahanBaku::findOrFail($id);

                // Kalkulasi total yang masuk (Kemasan x Isi)
                $total_masuk = $request->jumlah_kemasan * $request->isi_per_kemasan;

                // Catat ke tabel riwayat (Audit Trail)
                StokMasuk::create([
                    'bahan_baku_id' => $bahan->id,
                    'user_id' => Auth::id(),
                    'jumlah_tambah' => $total_masuk,
                    'tanggal_kedaluwarsa' => $request->tanggal_kedaluwarsa,
                    'catatan' => $request->catatan,
                ]);

                // Tambahkan stok utama
                $bahan->stok_saat_ini += $total_masuk;
                $bahan->save();
            });

            return redirect()->route('inventory.index')
                ->with('notify', ['success', 'Stok berhasil direkam dan diperbarui!', 'type' => 'success']);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses stok: ' . $e->getMessage());
        }
    }
}
