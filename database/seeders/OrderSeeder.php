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
        Schema::disableForeignKeyConstraints();
        DB::table('order_items')->truncate();
        DB::table('orders')->truncate();
        Schema::enableForeignKeyConstraints();

        $totalHari = 60; 

        // ---------------------------------------------------------
        // Asumsi ID: 1 = Matcha OG, 2 = Matcha Caramel, 3 = Matcha Strawberry
        // ---------------------------------------------------------
        $katalogProduk = [
            ['id' => 1, 'price' => 12000],
            ['id' => 2, 'price' => 14000], 
            ['id' => 3, 'price' => 16000], 
        ];

        for ($i = $totalHari; $i >= 0; $i--) {
            $tanggal = Carbon::now()->subDays($i);

            $hariWeekend = $tanggal->isDayOfWeek(Carbon::FRIDAY) ||
                $tanggal->isDayOfWeek(Carbon::SATURDAY) ||
                $tanggal->isDayOfWeek(Carbon::SUNDAY);

            // Constraint Target Produksi: Aman dan Sesuai Wawancara Owner
            $targetCups = $hariWeekend ? rand(20, 30) : rand(10, 15);
            
            $cupsGenerated = 0;
            $j = 0;

            while ($cupsGenerated < $targetCups) {
                $jamAcak = $tanggal->copy()->setHour(rand(10, 21))->setMinute(rand(0, 59));
                $nomorUrut = str_pad($j + 1, 3, '0', STR_PAD_LEFT);
                $invoiceNumber = 'INV-' . $jamAcak->format('Ymd') . '-' . $nomorUrut;

                $produkTerpilih = $katalogProduk[array_rand($katalogProduk)];
                
                $sisaCups = $targetCups - $cupsGenerated;
                $qtyBeli = ($sisaCups >= 2) ? rand(1, 2) : 1;
                $cupsGenerated += $qtyBeli;

                // REVISI LOGIKA KEUANGAN: Pajak di-set ke 0 secara absolut
                $subtotal = $produkTerpilih['price'] * $qtyBeli;
                $tax = 0; 
                $totalPrice = $subtotal + $tax;

                $orderId = DB::table('orders')->insertGetId([
                    'invoice_number' => $invoiceNumber,
                    'user_id' => 1,
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'total_price' => $totalPrice,
                    'status' => 'completed',
                    'created_at' => $jamAcak,
                    'updated_at' => $jamAcak,
                ]);

                DB::table('order_items')->insert([
                    'order_id' => $orderId,
                    'product_id' => $produkTerpilih['id'],
                    'quantity' => $qtyBeli,
                    'price' => $produkTerpilih['price'], 
                    'created_at' => $jamAcak,
                    'updated_at' => $jamAcak,
                ]);
                
                $j++;
            }
        }
    }
}