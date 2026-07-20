<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    // Mengizinkan mass assignment untuk kolom-kolom ini
    protected $fillable = [
        'invoice_number',
        'user_id',
        'subtotal',
        'tax',
        'total_price',
        'status'
    ];

    // Relasi One-to-Many ke OrderItem
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
