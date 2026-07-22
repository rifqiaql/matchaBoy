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
        $produkSampleId = 1;

        for ($i = $totalHari; $i >= 0; $i--) {
            $tanggal = Carbon::now()->subDays($i);
            $hariIni = $tanggal->isDayOfWeek(Carbon::SATURDAY) || $tanggal->isDayOfWeek(Carbon::SUNDAY);
            $hariJumat = $tanggal->isDayOfWeek(Carbon::FRIDAY);

            if ($hariIni) {
                $jumlahOrder = rand(40, 70);
            } elseif ($hariJumat) {
                $jumlahOrder = rand(30, 50);
            } else {
                $jumlahOrder = rand(15, 35);
            }

            for ($j = 0; $j < $jumlahOrder; $j++) {
                $jamAcak = $tanggal->copy()->setHour(rand(10, 21))->setMinute(rand(0, 59));

                // PERBAIKAN: Gunakan str_pad dan $j untuk membuat nomor antrean urut per hari (001, 002, dst)
                $nomorUrut = str_pad($j + 1, 3, '0', STR_PAD_LEFT);
                $invoiceNumber = 'INV-' . $jamAcak->format('Ymd') . '-' . $nomorUrut;

                $orderId = DB::table('orders')->insertGetId([
                    'invoice_number' => $invoiceNumber,
                    'user_id' => 1,
                    'subtotal' => 15000,
                    'tax' => 1500,
                    'total_price' => 16500,
                    'status' => 'paid',
                    'created_at' => $jamAcak,
                    'updated_at' => $jamAcak,
                ]);

                $qtyBeli = rand(1, 3);

                DB::table('order_items')->insert([
                    'order_id' => $orderId,
                    'product_id' => $produkSampleId,
                    'quantity' => $qtyBeli,
                    'price' => 15000,
                    'created_at' => $jamAcak,
                    'updated_at' => $jamAcak,
                ]);
            }
        }
    }
}
