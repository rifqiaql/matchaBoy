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

        $transactions = Order::whereMonth('created_at', $month)
                             ->whereYear('created_at', $year)
                             ->orderBy('created_at', 'desc')
                             ->paginate(50)->withQueryString();

        return view('riwayat.index', compact('transactions', 'month', 'year'));
    }

    /**
     * Membatalkan Transaksi (Void) dan Mengembalikan Stok Gudang Dinamis
     */
    public function voidTransaction($id)
    {
        DB::beginTransaction();

        try {
            $order = Order::findOrFail($id);
            $orderItems = OrderItem::where('order_id', $order->id)->get();

            // 1. Loop Detail Transaksi untuk mengembalikan stok berdasarkan resep DB
            foreach ($orderItems as $item) {
                // Load produk beserta relasi resepnya dari DB
                $product = Product::with('ingredients')->find($item->product_id);
                if (!$product) continue;

                $qtyOrdered = $item->quantity;

                // Kembalikan stok secara dinamis membaca tabel pivot resep
                foreach ($product->ingredients as $ingredient) {
                    // Ambil takaran per porsi dari pivot (quantity_needed)
                    $takaran = $ingredient->pivot->quantity_needed ?? 0;
                    $totalDikembalikan = $takaran * $qtyOrdered;

                    // Tambahkan kembali stok ke gudang
                    $ingredient->increment('stok_saat_ini', $totalDikembalikan);
                }
            }

            // 2. Hapus detail pesanan dan transaksi utama
            OrderItem::where('order_id', $order->id)->delete();
            $order->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Transaksi berhasil dibatalkan. Stok bahan baku telah dikembalikan ke Gudang secara presisi.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal membatalkan transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Memproses transaksi (Checkout) & Pemotongan Stok BOM Dinamis dari DB
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

            // 2. Loop Pertama: Hitung Total Uang & Validasi Kecukupan Stok Bahan
            foreach ($request->cart as $item) {
                // Wajib eager load 'ingredients' untuk membaca resep dinamis
                $product = Product::with('ingredients')->findOrFail($item['id']);
                $itemTotalPrice = $product->price * $item['quantity'];
                $subtotal += $itemTotalPrice;

                // Cek Validasi Stok Bahan Baku sebelum transaksi diproses
                foreach ($product->ingredients as $ingredient) {
                    $takaran = $ingredient->pivot->quantity_needed ?? 0;
                    $totalButuh = $takaran * $item['quantity'];

                    if ($ingredient->stok_saat_ini < $totalButuh) {
                        throw new \Exception("Stok '{$ingredient->nama_bahan}' tidak cukup untuk menu {$product->name}! Butuh: {$totalButuh} {$ingredient->satuan}, Sisa: {$ingredient->stok_saat_ini} {$ingredient->satuan}");
                    }
                }

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

            // 5. Loop Kedua: Simpan Detail Transaksi & Potong Stok Gudang secara Dinamis
            foreach ($orderItemsData as $data) {
                $product = $data['product'];
                $qtyOrdered = $data['quantity'];

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $qtyOrdered,
                    'price' => $data['price']
                ]);

                // POTONG STOK OTOMATIS BERDASARKAN RESEP DI DATABASE (PIVOT)
                foreach ($product->ingredients as $ingredient) {
                    $takaran = $ingredient->pivot->quantity_needed ?? 0;
                    $totalPenyusutan = $takaran * $qtyOrdered;

                    // Potong stok secara presisi berdasarkan relasi pivot
                    $ingredient->decrement('stok_saat_ini', $totalPenyusutan);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi Berhasil, Stok Gudang Otomatis Berkurang Presisi!',
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
}