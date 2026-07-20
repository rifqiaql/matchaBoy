<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // Nomor struk unik untuk pelacakan (Wajib)
            $table->string('invoice_number')->unique();

            // Siapa kasir/admin yang melayani (Relasi ke tabel users)
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            // Komponen Keuangan (Integer murni untuk Rupiah)
            $table->integer('subtotal');
            $table->integer('tax')->default(0);
            $table->integer('total_price');

            // Status dan Metode Pembayaran
            $table->string('status')->default('completed');
            $table->string('payment_method')->default('cash');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
