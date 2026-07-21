<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('stok_masuks', function (Blueprint $table) {
            $table->id();

            // Relasi ke barang apa yang di-restock
            // (Pastikan nama tabel 'bahan_baku' sesuai dengan yang ada di database lu)
            $table->foreignId('bahan_baku_id')->constrained('bahan_baku')->onDelete('cascade');

            // Relasi ke siapa admin yang melakukan restock (Audit Trail)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Pakai decimal agar bisa support pecahan (misal: 1.5 kg)
            $table->decimal('jumlah_tambah', 10, 2);

            // Nullable karena tidak semua barang punya expired date (contoh: Paper Cup)
            $table->date('tanggal_kedaluwarsa')->nullable();

            // Untuk mencatat nomor nota/kuitansi/nama supplier
            $table->string('catatan')->nullable();

            // Timestamps otomatis mencatat 'created_at' sebagai waktu barang masuk
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_masuks');
    }
};
