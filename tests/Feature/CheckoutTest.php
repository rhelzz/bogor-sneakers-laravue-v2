<?php

namespace Tests\Feature;

use App\Models\Catalog;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        // Clear rate limiter before each test
        RateLimiter::clear('checkout:');
    }

    /** @test */
    public function test_user_can_checkout_successfully()
    {
        $product = Catalog::factory()->create([
            'price' => 1500000,
            'status' => 'ready'
        ]);

        $payload = [
            'customer_name' => 'John Doe',
            'customer_phone' => '08123456789',
            'customer_address' => 'Jl. Bogor Sneakers No. 1',
            'notes' => 'Tolong dipacking kayu',
            'items' => [
                [
                    'catalog_id' => $product->id,
                    'quantity' => 1,
                    'size' => '42',
                ]
            ],
            'extra_field' => '', // Honeypot empty
        ];

        $response = $this->postJson(route('api.checkout'), $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'order_number',
                    'wa_link'
                ]
            ]);

        $this->assertDatabaseHas('orders', [
            'customer_name' => 'John Doe',
            'customer_phone' => '08123456789',
            'status' => 'unverified'
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_name' => $product->name,
            'price' => 1500000,
            'quantity' => 1,
            'size' => '42'
        ]);
    }

    /** @test */
    public function test_checkout_fails_if_honeypot_is_filled()
    {
        $payload = [
            'customer_name' => 'Bot User',
            'customer_phone' => '08123456789',
            'customer_address' => 'Bot Address',
            'items' => [],
            'extra_field' => 'I am a bot', // Honeypot filled
        ];

        $response = $this->postJson(route('api.checkout'), $payload);

        $response->assertStatus(422);
        $this->assertDatabaseCount('orders', 0);
    }

    /** @test */
    public function test_checkout_has_rate_limiting()
    {
        $product = Catalog::factory()->create();
        $payload = [
            'customer_name' => 'John Doe',
            'customer_phone' => '08123456789',
            'customer_address' => 'Address',
            'items' => [['catalog_id' => $product->id, 'quantity' => 1, 'size' => '42']],
            'extra_field' => '',
        ];

        // Simulate 3 requests (assuming limit is 3)
        for ($i = 0; $i < 3; $i++) {
            $this->postJson(route('api.checkout'), $payload)->assertStatus(201);
        }

        // 4th request should fail
        $this->postJson(route('api.checkout'), $payload)->assertStatus(429);
    }

    /** @test */
    public function test_checkout_fails_with_invalid_phone_format()
    {
        $payload = [
            'customer_name' => 'John Doe',
            'customer_phone' => '123', // Too short/invalid
            'customer_address' => 'Address',
            'items' => [],
            'extra_field' => '',
        ];

        $response = $this->postJson(route('api.checkout'), $payload);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['customer_phone']);
    }

    /** @test */
    public function test_admin_can_update_order_status()
    {
        $order = Order::create([
            'customer_name' => 'John Doe',
            'customer_phone' => '08123456789',
            'customer_address' => 'Address',
            'subtotal' => 1000000,
            'total_amount' => 1000000,
            'status' => 'unverified'
        ]);

        $response = $this->patchJson(route('admin.orders.update-status', $order), [
            'status' => 'processing'
        ]);

        $response->assertStatus(200);
        $this->assertEquals('processing', $order->fresh()->status);
        $this->assertEquals('Sedang Diproses', $order->fresh()->status_label);
    }

    /** @test */
    public function test_order_number_generation_is_sequential_and_unique()
    {
        $order1 = Order::create([
            'customer_name' => 'User 1',
            'customer_phone' => '081',
            'customer_address' => 'Addr',
            'subtotal' => 0, 'total_amount' => 0
        ]);
        
        $order2 = Order::create([
            'customer_name' => 'User 2',
            'customer_phone' => '082',
            'customer_address' => 'Addr',
            'subtotal' => 0, 'total_amount' => 0
        ]);

        $this->assertNotEquals($order1->order_number, $order2->order_number);
        $this->assertStringContainsString('BGS-' . date('Ym'), $order1->order_number);
    }
}
