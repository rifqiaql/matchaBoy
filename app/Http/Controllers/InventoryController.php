<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use App\Models\StokMasuk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
            $kategoriMap = [
                'Bahan Pokok' => 'Bubuk',
                'Cair'        => 'Cairan',
                'Syrup'       => 'Sirup',
            ];
            
            $kategoriValid = $kategoriMap[$request->kategori] ?? $request->kategori;
            $query->where('kategori', $kategoriValid);
        }

        $bahanBaku = $query->paginate(10)->withQueryString();

        $monthlyRestockCount = StokMasuk::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return view('inventory.index', [
            'bahanBaku'           => $bahanBaku,
            'monthlyRestockCount' => $monthlyRestockCount,
        ]);
    }

    /**
     * Export filtered inventory to Microsoft Excel Asli (.xls) berformat Eksekutif & Prediktif.
     */
    public function export(Request $request)
    {
        $query = BahanBaku::query();

        if ($request->filled('search')) {
            $query->where('nama_bahan', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('kategori')) {
            $kategoriMap = [
                'Bahan Pokok' => 'Bubuk',
                'Cair'        => 'Cairan',
                'Syrup'       => 'Sirup',
            ];
            
            $kategoriValid = $kategoriMap[$request->kategori] ?? $request->kategori;
            $query->where('kategori', $kategoriValid);
        }

        $bahanBaku = $query->get();
        $tanggalCetak = \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('d F Y - H:i') . ' WIB';
        $filename = 'Laporan_Monitoring_Gudang_MatchaBoy_' . date('Ymd_His') . '.xls';

        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo '
        <html xmlns:o="urn:schemas-microsoft-com:office:office"
              xmlns:x="urn:schemas-microsoft-com:office:excel"
              xmlns="http://www.w3.org/TR/REC-html40">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
            <!--[if gte mso 9]>
            <xml>
                <x:ExcelWorkbook>
                    <x:ExcelWorksheets>
                        <x:ExcelWorksheet>
                            <x:Name>Stok Gudang</x:Name>
                            <x:WorksheetOptions>
                                <x:DisplayGridlines/>
                            </x:WorksheetOptions>
                        </x:ExcelWorksheet>
                    </x:ExcelWorksheets>
                </x:ExcelWorkbook>
            </xml>
            <![endif]-->
            <style>
                table {
                    border-collapse: collapse;
                    width: 100%;
                    font-family: Arial, sans-serif;
                }
                .title {
                    font-size: 16pt;
                    font-weight: bold;
                    color: #2D5A34;
                    text-align: left;
                }
                .subtitle {
                    font-size: 11pt;
                    color: #555555;
                    font-style: italic;
                    text-align: left;
                }
                th {
                    background-color: #2D5A34;
                    color: #FFFFFF;
                    font-weight: bold;
                    border: 1px solid #1f3d24;
                    padding: 8px;
                    text-align: center;
                    vertical-align: middle;
                }
                td {
                    border: 1px solid #dcdcdc;
                    padding: 6px 8px;
                    vertical-align: middle;
                }
                .text-center { text-align: center; }
                .text-right { text-align: right; }
                .text-left { text-align: left; }
                .status-kritis {
                    background-color: #ffe6e6;
                    color: #c00000;
                    font-weight: bold;
                    text-align: center;
                }
                .status-aman {
                    background-color: #e6f7ec;
                    color: #2d5a34;
                    font-weight: bold;
                    text-align: center;
                }
                .num-fmt {
                    mso-number-format: "\#\,\#\#0";
                }
                .percent-fmt {
                    mso-number-format: "0\.0%";
                }
            </style>
        </head>
        <body>
            <table>
                <tr>
                    <td colspan="10" class="title">LAPORAN MONITORING STOK GUDANG & ANALISIS KAPASITAS - MATCHA BOY</td>
                </tr>
                <tr>
                    <td colspan="10" class="subtitle">Waktu Cetak: ' . $tanggalCetak . '</td>
                </tr>
                <tr><td colspan="10"></td></tr>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>ID Bahan</th>
                        <th>Nama Bahan Baku</th>
                        <th>Kategori</th>
                        <th>Stok Awal (Kapasitas)</th>
                        <th>Stok Saat Ini</th>
                        <th>Satuan</th>
                        <th>Sisa Kapasitas (%)</th>
                        <th>Batas Minimum</th>
                        <th>Status Kondisi & Rekomendasi</th>
                    </tr>
                </thead>
                <tbody>';

        $no = 1;
        $countKritis = 0;

        foreach ($bahanBaku as $item) {
            $stokAwal = (float) $item->stok_awal;
            $stokSaatIni = (float) $item->stok_saat_ini;
            $stokMin = (float) $item->stok_minimum;

            // Hitung persentase sisa kapasitas
            $persentaseSisa = $stokAwal > 0 ? ($stokSaatIni / $stokAwal) : 0;
            
            $isKritis = $stokSaatIni <= $stokMin;
            if ($isKritis) {
                $countKritis++;
                $statusClass = 'status-kritis';
                $statusText  = 'KRITIS (SEGERA RESTOCK)';
            } else {
                $statusClass = 'status-aman';
                $statusText  = 'AMAN (OPERASIONAL NORMAL)';
            }

            echo '
                    <tr>
                        <td class="text-center">' . $no++ . '</td>
                        <td class="text-center">' . $item->id . '</td>
                        <td class="text-left"><b>' . ucwords($item->nama_bahan) . '</b></td>
                        <td class="text-center">' . ($item->kategori ?? 'Umum') . '</td>
                        <td class="text-right num-fmt">' . $stokAwal . '</td>
                        <td class="text-right num-fmt"><b>' . $stokSaatIni . '</b></td>
                        <td class="text-center">' . $item->satuan . '</td>
                        <td class="text-right percent-fmt">' . number_format($persentaseSisa * 100, 1) . '%</td>
                        <td class="text-right num-fmt">' . $stokMin . '</td>
                        <td class="' . $statusClass . '">' . $statusText . '</td>
                    </tr>';
        }

        $totalItem = $bahanBaku->count();

        echo '
                    <tr style="background-color: #f2f2f2; font-weight: bold;">
                        <td colspan="4" class="text-right" style="padding: 10px;">RINGKASAN EKSEKUTIF GUDANG:</td>
                        <td colspan="3" class="text-center">' . $totalItem . ' Jenis Bahan Baku Terdaftar</td>
                        <td colspan="3" class="text-center" style="color: ' . ($countKritis > 0 ? '#c00000' : '#2d5a34') . ';">
                            Total Item Status Kritis: ' . $countKritis . ' Item
                        </td>
                    </tr>
                </tbody>
            </table>
        </body>
        </html>';

        exit;
    }

    /**
     * Store a newly created bahan baku in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_bahan'      => 'required|string|max:255',
            'kategori'        => 'required|string',
            'satuan'          => 'required|string',
            'jumlah_kemasan'  => 'required|numeric|min:0',
            'isi_per_kemasan' => 'required|numeric|min:0',
            'stok_minimum'    => 'required|numeric|min:0',
        ]);

        $totalStok = $request->jumlah_kemasan * $request->isi_per_kemasan;

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
            'nama_bahan'   => 'required|string|max:255',
            'kategori'     => 'required|string|max:100',
            'satuan'       => 'required|string|max:100',
            'stok_awal'     => 'required|integer|min:0',
            'stok_saat_ini' => 'required|integer|min:0',
            'stok_minimum'  => 'required|integer|min:0',
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
            'jumlah_kemasan'  => 'required|numeric|min:0.1',
            'isi_per_kemasan' => 'required|numeric|min:0.1',
            'catatan'         => 'nullable|string|max:255'
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                $bahan = BahanBaku::findOrFail($id);

                $total_masuk = $request->jumlah_kemasan * $request->isi_per_kemasan;

                StokMasuk::create([
                    'bahan_baku_id' => $bahan->id,
                    'user_id'       => Auth::id(),
                    'jumlah_tambah' => $total_masuk,
                    'catatan'       => $request->catatan,
                ]);

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