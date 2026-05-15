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

class OrderController extends Controller
{
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
            'shipping_cost' => 'required|integer|min:0',
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
                'shipping_cost' => $request->shipping_cost,
                'total_amount' => $subtotal + $request->shipping_cost,
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
     * List all orders (Admin).
     */
    public function adminIndex(Request $request)
    {
        $query = Order::query();

        // 1. Search Filter (Order Number, Name, Phone)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        // 2. Status Filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // 3. Date Range Filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // 4. Notes Filter
        if ($request->filled('notes_filter') && $request->notes_filter !== 'all') {
            if ($request->notes_filter === 'with_notes') {
                $query->whereNotNull('notes')->where('notes', '!=', '');
            } else {
                $query->where(function($q) {
                    $q->whereNull('notes')->orWhere('notes', '');
                });
            }
        }

        // 5. Region Filter (Case Bogor)
        if ($request->filled('region') && $request->region !== 'all') {
            if ($request->region === 'bogor') {
                $query->where('customer_address', 'like', '%bogor%');
            } else {
                $query->where('customer_address', 'not like', '%bogor%');
            }
        }

        // 6. Item Count Filter
        if ($request->filled('item_count') && $request->item_count !== 'all') {
            if ($request->item_count === '3+') {
                $query->has('items', '>=', 3);
            } else {
                $query->has('items', '=', (int)$request->item_count);
            }
        }

        // 7. Sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $validSortColumns = ['order_number', 'customer_name', 'created_at', 'total_amount'];
        
        if (in_array($sortBy, $validSortColumns)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $orders = $query->get()->map(function ($order) {
            return [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'customer_name' => $order->customer_name,
                'customer_phone' => $order->customer_phone,
                'customer_address' => $order->customer_address,
                'subtotal' => $order->subtotal,
                'shipping_cost' => $order->shipping_cost,
                'total_amount' => $order->total_amount,
                'status' => $order->status,
                'status_label' => $order->status_label,
                'item_count' => $order->items()->count(),
                'created_at' => $order->created_at->format('Y-m-d H:i'),
                'notes' => $order->notes,
            ];
        });

        // Pass filters back for UI synchronization
        $filters = $request->only(['search', 'status', 'date_from', 'date_to', 'notes_filter', 'region', 'item_count', 'sort_by', 'sort_order']);

        return \Inertia\Inertia::render('Admin/Orders/Index', [
            'orders' => $orders,
            'statusOptions' => Order::getStatusLabels(),
            'initialFilters' => $filters,
        ]);
    }

    /**
     * Show order detail (Admin).
     */
    public function adminShow(Order $order)
    {
        $order->load('items');
        
        return \Inertia\Inertia::render('Admin/Orders/Show', [
            'order' => array_merge($order->toArray(), [
                'status_label' => $order->status_label,
                'created_at' => $order->created_at->format('d M Y, H:i'),
            ]),
            'statusOptions' => Order::getStatusLabels(),
        ]);
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
