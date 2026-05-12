<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $cartService = $cartService;
        $this->cartService = $cartService;
    }

    public function index(Request $request)
    {
        // Inertia will handle the index via shared props in HandleInertiaRequests
        return back();
    }

    public function add(Request $request)
    {
        $request->validate([
            'catalog_id' => 'required|exists:catalogs,id',
            'catalog_size_id' => 'required|exists:catalog_sizes,id',
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        $cart = $request->attributes->get('cart');
        $this->cartService->addItem(
            $cart,
            $request->catalog_id,
            $request->catalog_size_id,
            $request->quantity
        );

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Produk berhasil ditambahkan ke keranjang',
                'cart' => $this->cartService->getCart($cart)
            ]);
        }

        return back()->with('success', 'Produk berhasil ditambahkan ke keranjang');
    }

    public function update(Request $request, $itemId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0|max:10',
        ]);

        $cart = $request->attributes->get('cart');
        $this->cartService->updateItem($cart, $itemId, $request->quantity);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Keranjang diperbarui',
                'cart' => $this->cartService->getCart($cart)
            ]);
        }

        return back();
    }

    public function remove(Request $request, $itemId)
    {
        $cart = $request->attributes->get('cart');
        $this->cartService->removeItem($cart, $itemId);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Produk dihapus dari keranjang',
                'cart' => $this->cartService->getCart($cart)
            ]);
        }

        return back();
    }
}
