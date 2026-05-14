<?php

namespace App\Http\Controllers;

use App\Models\Catalog;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    /**
     * Display a listing of orders (Admin).
     */
    public function adminIndex(): Response
    {
        $orders = Order::query()
            ->with(['items'])
            ->latest()
            ->get()
            ->map(function (Order $order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_name' => $order->customer_name,
                    'customer_phone' => $order->customer_phone,
                    'total_amount' => $order->total_amount,
                    'status' => $order->status,
                    'status_label' => $order->status_label,
                    'item_count' => $order->items->count(),
                    'created_at' => $order->created_at->format('d M Y H:i'),
                ];
            });

        return Inertia::render('Admin/Orders/Index', [
            'orders' => $orders,
            'statusOptions' => Order::getStatusLabels(),
        ]);
    }

    /**
     * Display the specified order (Admin).
     */
    public function adminShow(Order $order): Response
    {
        $order->load(['items.catalog']);

        return Inertia::render('Admin/Orders/Show', [
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'customer_name' => $order->customer_name,
                'customer_phone' => $order->customer_phone,
                'customer_address' => $order->customer_address,
                'notes' => $order->notes,
                'subtotal' => $order->subtotal,
                'shipping_cost' => $order->shipping_cost,
                'total_amount' => $order->total_amount,
                'status' => $order->status,
                'status_label' => $order->status_label,
                'created_at' => $order->created_at->format('d M Y H:i'),
                'items' => $order->items->map(function (OrderItem $item) {
                    return [
                        'id' => $item->id,
                        'product_name' => $item->product_name,
                        'size' => $item['size'],
                        'price' => $item->price,
                        'quantity' => $item->quantity,
                        'subtotal' => $item->subtotal,
                        'image_url' => $item->catalog?->primary_image_url,
                    ];
                }),
            ],
            'statusOptions' => Order::getStatusLabels(),
        ]);
    }

    /**
     * Handle public checkout.
     */
    public function checkout(Request $request): JsonResponse
    {
        // 1. Honeypot check
        if (!empty($request->input('extra_field'))) {
            return response()->json(['message' => 'Spam detected.'], 422);
        }

        // 2. Rate limiting (max 3 per hour per IP)
        $executed = RateLimiter::attempt(
            'checkout:' . $request->ip(),
            $maxAttempts = 3,
            function() {},
            $decaySeconds = 3600
        );

        if (!$executed) {
            return response()->json(['message' => 'Terlalu banyak pesanan. Silakan coba lagi nanti.'], 429);
        }

        // 3. Validation
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|min:10|max:20',
            'customer_address' => 'required|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.catalog_id' => 'required|exists:catalogs,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.size' => 'required|string',
        ]);

        return DB::transaction(function () use ($request) {
            $subtotal = 0;
            $orderItemsData = [];

            foreach ($request->items as $item) {
                $catalog = Catalog::findOrFail($item['catalog_id']);
                $itemSubtotal = $catalog->price * $item['quantity'];
                $subtotal += $itemSubtotal;

                $orderItemsData[] = [
                    'catalog_id' => $catalog->id,
                    'product_name' => $catalog->name,
                    'size' => $item['size'],
                    'price' => $catalog->price,
                    'quantity' => $item['quantity'],
                    'subtotal' => $itemSubtotal,
                ];
            }

            // Create Order
            $order = Order::create([
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_address' => $request->customer_address,
                'notes' => $request->notes,
                'subtotal' => $subtotal,
                'shipping_cost' => 0, // Could be calculated later
                'total_amount' => $subtotal,
                'status' => Order::STATUS_UNVERIFIED,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Create Items
            foreach ($orderItemsData as $itemData) {
                $order->items()->create($itemData);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Pesanan berhasil dibuat.',
                'data' => [
                    'order_number' => $order->order_number,
                    'wa_link' => $this->generateWhatsAppLink($order),
                ]
            ], 201);
        });
    }

    /**
     * Update order status (Admin).
     */
    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(Order::getStatusLabels())),
        ]);

        $order->update(['status' => $request->status]);

        return response()->json([
            'status' => 'success',
            'message' => 'Status pesanan berhasil diperbarui menjadi: ' . $order->status_label,
            'data' => [
                'status' => $order->status,
                'status_label' => $order->status_label,
            ]
        ]);
    }

    /**
     * Generate WhatsApp confirmation link.
     */
    private function generateWhatsAppLink(Order $order): string
    {
        $adminPhone = config('services.whatsapp.admin_phone', '628123456789'); // Fallback
        
        $message = "Halo Bogor Sneakers, saya ingin konfirmasi pesanan saya:\n\n";
        $message .= "*No Order:* " . $order->order_number . "\n";
        $message .= "*Nama:* " . $order->customer_name . "\n";
        $message .= "*Total:* Rp " . number_format($order->total_amount, 0, ',', '.') . "\n\n";
        $message .= "Mohon informasikan langkah selanjutnya untuk pembayaran. Terima kasih!";

        return "https://wa.me/" . $adminPhone . "?text=" . urlencode($message);
    }
}
