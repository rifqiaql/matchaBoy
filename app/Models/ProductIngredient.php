<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductIngredient extends Model
{
    // Pastikan nama tabelnya sesuai dengan yang lo buat di phpMyAdmin tadi
    protected $table = 'product_ingredients';

    protected $fillable = [
        'product_id',
        'ingredient_id',
        'quantity_needed'
    ];

    // Relasi ke bahan baku (Sesuaikan nama model BahanBaku/Ingredient lo)
    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(BahanBaku::class, 'ingredient_id', 'id');
    }
}
