<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokMasuk extends Model
{
    use HasFactory;

    // Mass Assignment Protection
    protected $fillable = [
        'bahan_baku_id',
        'user_id',
        'jumlah_tambah',
        'tanggal_kedaluwarsa',
        'catatan'
    ];

    // Relasi balik ke Bahan Baku
    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class, 'bahan_baku_id');
    }

    // Relasi balik ke Admin/Kasir yang merekam data
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
