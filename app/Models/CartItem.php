<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'catalog_id',
        'catalog_size_id',
        'quantity',
        'price_at_time',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price_at_time' => 'integer',
    ];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function catalog(): BelongsTo
    {
        return $this->belongsTo(Catalog::class);
    }

    public function size(): BelongsTo
    {
        return $this->belongsTo(CatalogSize::class, 'catalog_size_id');
    }
}
