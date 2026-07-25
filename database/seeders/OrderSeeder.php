<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // Matikan proteksi foreign key sementara agar bisa di-truncate (dikosongkan)
        Schema::disableForeignKeyConstraints();
        DB::table('order_items')->truncate();
        DB::table('orders')->truncate();
        Schema::enableForeignKeyConstraints();

        $totalHari = 60; // Mundur 2 bulan ke belakang
        $produkSampleId = 1; // Pastikan ID 1 adalah "Matcha Original" di database lu
        $hargaSatuan = 15000;

        for ($i = $totalHari; $i >= 0; $i--) {
            $tanggal = Carbon::now()->subDays($i);

            // Logika Pola: Weekend (Jumat, Sabtu, Minggu) vs Weekday (Senin - Kamis)
            $hariWeekend = $tanggal->isDayOfWeek(Carbon::FRIDAY) ||
                $tanggal->isDayOfWeek(Carbon::SATURDAY) ||
                $tanggal->isDayOfWeek(Carbon::SUNDAY);

            if ($hariWeekend) {
                // Target: 18 - 26 cup per hari
                // Kita buat 10 sampai 14 transaksi, rata-rata beli 1-2 cup per transaksi
                $jumlahOrder = rand(10, 14);
            } else {
                // Target Weekday: 8 - 12 cup per hari (Sepi)
                // Kita buat 6 sampai 8 transaksi, rata-rata beli 1-2 cup per transaksi
                $jumlahOrder = rand(6, 8);
            }

            for ($j = 0; $j < $jumlahOrder; $j++) {
                // Acak jam operasional warung (Jam 10 pagi - 9 malam)
                $jamAcak = $tanggal->copy()->setHour(rand(10, 21))->setMinute(rand(0, 59));

                // Generate Nomor Urut Invoice Rapi (001, 002, dst)
                $nomorUrut = str_pad($j + 1, 3, '0', STR_PAD_LEFT);
                $invoiceNumber = 'INV-' . $jamAcak->format('Ymd') . '-' . $nomorUrut;

                // Dinamisasi Finansial (Jumlah Beli Acak 1 atau 2 cup saja biar realistis)
                $qtyBeli = rand(1, 2);
                $subtotal = $hargaSatuan * $qtyBeli;
                $tax = $subtotal * 0.10; // Pajak 10%
                $totalPrice = $subtotal + $tax;

                // 1. Insert ke tabel orders (Kepala Struk) - STATUS COMPLETED!
                $orderId = DB::table('orders')->insertGetId([
                    'invoice_number' => $invoiceNumber,
                    'user_id' => 1,
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'total_price' => $totalPrice,
                    'status' => 'completed', // <--- KRUSIAL: Jangan pakai 'paid'
                    'created_at' => $jamAcak,
                    'updated_at' => $jamAcak,
                ]);

                // 2. Insert ke tabel order_items (Detail Barang)
                DB::table('order_items')->insert([
                    'order_id' => $orderId,
                    'product_id' => $produkSampleId,
                    'quantity' => $qtyBeli,
                    'price' => $hargaSatuan,
                    'created_at' => $jamAcak,
                    'updated_at' => $jamAcak,
                ]);
            }
        }
    }
}
