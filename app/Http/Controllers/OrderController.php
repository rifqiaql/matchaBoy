<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\BahanBaku;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Menampilkan Halaman Riwayat Transaksi (Filter per Bulan)
     */
    public function history(Request $request)
    {
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));

        // Ubah get() menjadi paginate(), angka 50 artinya 50 baris per halaman
        // withQueryString() wajib agar filter bulan/tahun tidak hilang saat pindah halaman
        $transactions = Order::whereMonth('created_at', $month)
                             ->whereYear('created_at', $year)
                             ->orderBy('created_at', 'desc')
                             ->paginate(50)->withQueryString();

        return view('riwayat.index', compact('transactions', 'month', 'year'));
    }

    /**
     * Membatalkan Transaksi (Void) dan Mengembalikan Stok Gudang
     */
    public function voidTransaction($id)
    {
        DB::beginTransaction();

        try {
            $order = Order::findOrFail($id);
            $orderItems = OrderItem::where('order_id', $order->id)->get();
            $semuaBahan = BahanBaku::all();

            // 1. Loop Detail Transaksi untuk mengembalikan stok
            foreach ($orderItems as $item) {
                $product = Product::find($item->product_id);
                if (!$product) continue;

                $qty = $item->quantity;
                $namaMenu = strtolower($product->name);

                // KEMBALIKAN STOK OTOMATIS BERDASARKAN RESEP BAKU (BOM) MATCHA BOY
                // (Mirroring dari method checkout)
                
                // Base BOM:
                $this->kembalikanStokBahan($semuaBahan, 'matcha', 8 * $qty);
                $this->kembalikanStokBahan($semuaBahan, 'full cream', 100 * $qty);
                $this->kembalikanStokBahan($semuaBahan, ['skm', 'kental manis'], 30 * $qty);

                // Tambahan Toping Khusus:
                if (str_contains($namaMenu, 'strawberry')) {
                    $this->kembalikanStokBahan($semuaBahan, 'strawberry', 15 * $qty);
                } elseif (str_contains($namaMenu, 'caramel')) {
                    $this->kembalikanStokBahan($semuaBahan, 'caramel', 15 * $qty);
                }
            }

            // 2. Hapus detail pesanan dan transaksi utama
            OrderItem::where('order_id', $order->id)->delete();
            $order->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Transaksi berhasil dibatalkan. Stok bahan baku telah dikembalikan ke Gudang.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal membatalkan transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Memproses transaksi (Checkout) dari Keranjang & Pemotongan Stok BOM Presisi
     */
    public function checkout(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'cart' => 'required|array',
            'cart.*.id' => 'required|exists:products,id',
            'cart.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            $subtotal = 0;
            $orderItemsData = []; 

            // 2. Loop Pertama: Hitung Total Uang dengan Harga Asli dari Database
            foreach ($request->cart as $item) {
                $product = Product::findOrFail($item['id']);
                $itemTotalPrice = $product->price * $item['quantity'];
                $subtotal += $itemTotalPrice;

                $orderItemsData[] = [
                    'product'  => $product,
                    'quantity' => $item['quantity'],
                    'price'    => $product->price
                ];
            }

            $tax = 0;
            $totalPrice = $subtotal;

            // 3. Generate Nomor Invoice
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(2)));

            // 4. Simpan Data ke Tabel `orders`
            $order = Order::create([
                'invoice_number' => $invoiceNumber,
                'user_id' => Auth::id() ?? 1,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total_price' => $totalPrice,
                'status' => 'completed',
                'payment_method' => $request->payment_method ?? 'cash'
            ]);

            $semuaBahan = BahanBaku::all();

            // 5. Loop Kedua: Simpan Detail Transaksi & Potong Stok Gudang
            foreach ($orderItemsData as $data) {
                $product = $data['product'];
                $qty = $data['quantity'];

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'price' => $data['price']
                ]);

                // POTONG STOK OTOMATIS BERDASARKAN RESEP BAKU
                $namaMenu = strtolower($product->name);

                $this->potongStokBahan($semuaBahan, 'matcha', 8 * $qty);
                $this->potongStokBahan($semuaBahan, 'full cream', 100 * $qty);
                $this->potongStokBahan($semuaBahan, ['skm', 'kental manis'], 30 * $qty);

                if (str_contains($namaMenu, 'strawberry')) {
                    $this->potongStokBahan($semuaBahan, 'strawberry', 15 * $qty);
                } elseif (str_contains($namaMenu, 'caramel')) {
                    $this->potongStokBahan($semuaBahan, 'caramel', 15 * $qty);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi Berhasil, Stok Gudang Otomatis Berkurang!',
                'invoice_number' => $order->invoice_number
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Helper privat untuk MEMOTONG stok (Digunakan saat Checkout)
     */
    private function potongStokBahan($collectionBahan, $keyword, $totalKurang)
    {
        $keywords = is_array($keyword) ? $keyword : [$keyword];

        $bahan = $collectionBahan->first(function ($item) use ($keywords) {
            foreach ($keywords as $kw) {
                if (str_contains(strtolower($item->nama_bahan), strtolower($kw))) {
                    return true;
                }
            }
            return false;
        });

        if (!$bahan) {
            $namaDicari = is_array($keyword) ? implode(' / ', $keyword) : $keyword;
            throw new \Exception("Sistem Gagal: Bahan baku '{$namaDicari}' tidak ditemukan di Gudang.");
        }

        if ($bahan->stok_saat_ini < $totalKurang) {
            throw new \Exception("Stok bahan '{$bahan->nama_bahan}' tidak cukup! Butuh: {$totalKurang} {$bahan->satuan}, Sisa: {$bahan->stok_saat_ini} {$bahan->satuan}");
        }

        $bahan->stok_saat_ini -= $totalKurang;
        $bahan->save();
    }

    /**
     * Helper privat untuk MENGEMBALIKAN stok (Digunakan saat Void/Batal Transaksi)
     */
    private function kembalikanStokBahan($collectionBahan, $keyword, $totalTambah)
    {
        $keywords = is_array($keyword) ? $keyword : [$keyword];

        $bahan = $collectionBahan->first(function ($item) use ($keywords) {
            foreach ($keywords as $kw) {
                if (str_contains(strtolower($item->nama_bahan), strtolower($kw))) {
                    return true;
                }
            }
            return false;
        });

        // Jika bahan ditemukan, kembalikan (increment) stoknya
        if ($bahan) {
            $bahan->stok_saat_ini += $totalTambah;
            $bahan->save();
        }
    }
}