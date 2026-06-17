<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BahanBaku extends Model
{
    /** @use HasFactory<\Database\Factories\BahanBakuFactory> */
    use HasFactory;

    protected $table = 'bahan_baku';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama_bahan',
        'satuan',
        'stok_awal',
        'stok_saat_ini',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'stok_awal' => 'integer',
            'stok_saat_ini' => 'integer',
        ];
    }
}
