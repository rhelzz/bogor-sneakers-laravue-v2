<?php

namespace App\Http\Middleware;

use App\Models\Cart;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class AssignCartToken
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $cartToken = $request->cookie('cart_token');
        $cart = null;

        if ($cartToken) {
            $cart = Cart::find($cartToken);
        }

        // If no cart or cart expired, create a new one
        if (! $cart || $cart->isExpired()) {
            $cart = Cart::create([
                'user_agent' => $request->userAgent(),
                'expires_at' => now()->addDays(30),
            ]);
            $cartToken = $cart->id;
        }

        // Share the cart instance with the request for easy access
        $request->attributes->set('cart', $cart);

        $response = $next($request);

        // Attach the cookie to the response if it's new or refreshed
        if ($request->cookie('cart_token') != $cartToken) {
            $response->headers->setCookie(
                cookie()->make(
                    'cart_token',
                    (string) $cartToken,
                    60 * 24 * 30, // 30 days
                    null,
                    null,
                    true, // Secure
                    true, // HttpOnly
                    false,
                    'Lax'
                )
            );
        }

        return $response;
    }
}
