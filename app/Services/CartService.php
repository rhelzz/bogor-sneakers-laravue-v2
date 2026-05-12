<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Catalog;
use App\Models\CatalogSize;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function getCart(Cart $cart)
    {
        $cart->load(['items.catalog.images', 'items.size']);

        $total = $cart->items->sum(function ($item) {
            return $item->quantity * $item->catalog->price; // Always use current price for display
        });

        return [
            'items' => $cart->items,
            'total' => $total,
            'count' => $cart->items->sum('quantity'),
        ];
    }

    public function addItem(Cart $cart, int $catalogId, int $sizeId, int $quantity)
    {
        return DB::transaction(function () use ($cart, $catalogId, $sizeId, $quantity) {
            $catalog = Catalog::findOrFail($catalogId);
            $size = CatalogSize::where('catalog_id', $catalogId)->where('id', $sizeId)->firstOrFail();

            $cartItem = $cart->items()->firstOrNew([
                'catalog_id' => $catalogId,
                'catalog_size_id' => $sizeId,
            ]);

            if ($cartItem->exists) {
                $cartItem->quantity += $quantity;
            } else {
                $cartItem->quantity = $quantity;
                $cartItem->price_at_time = $catalog->price;
            }

            $cartItem->save();

            return $cartItem;
        });
    }

    public function updateItem(Cart $cart, int $itemId, int $quantity)
    {
        $item = $cart->items()->findOrFail($itemId);
        
        if ($quantity <= 0) {
            return $item->delete();
        }

        return $item->update(['quantity' => $quantity]);
    }

    public function removeItem(Cart $cart, int $itemId)
    {
        return $cart->items()->where('id', $itemId)->delete();
    }
}
