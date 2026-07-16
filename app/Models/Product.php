<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    // Secara default Laravel akan membaca tabel bernama 'products'
    protected $table = 'products';

    // Kolom yang boleh diisi secara massal
    protected $fillable = [
        'name',
        'price',
        'image'
    ];

    /**
     * Relasi ke ProductIngredient (Tabel Jembatan Resep)
     * Satu produk bisa memiliki banyak bahan baku di resepnya
     */
    public function ingredients()
    {
        // Parameter: (ModelTujuan, NamaTabelPivot, KunciLokal, KunciTujuan)
        return $this->belongsToMany(BahanBaku::class, 'product_ingredients', 'product_id', 'ingredient_id')
            ->withPivot('quantity_needed')
            ->withTimestamps();
    }



    /**
     * Relasi ke OrderItem (Detail Penjualan)
     * Satu produk bisa terjual di banyak transaksi
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'product_id', 'id');
    }
}
