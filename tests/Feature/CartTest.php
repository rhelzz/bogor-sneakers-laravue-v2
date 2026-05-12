<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Catalog;
use App\Models\CatalogSize;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Cookie;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    protected $catalog;
    protected $size;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup base data
        $this->catalog = Catalog::create([
            'name' => 'Nike Air Jordan',
            'collection' => 'Classic',
            'price' => 1500000,
            'status' => 'ready',
            'slug' => 'nike-air-jordan',
            'public_id' => 'ulid-test-123'
        ]);

        $this->size = $this->catalog->sizes()->create(['size_eu' => 42]);
    }

    public function test_middleware_automatically_assigns_cart_token_cookie()
    {
        $response = $this->get('/');

        $response->assertCookie('cart_token');
        $this->assertDatabaseCount('carts', 1);
    }

    public function test_can_add_item_to_cart()
    {
        $response = $this->postJson(route('cart.add'), [
            'catalog_id' => $this->catalog->id,
            'catalog_size_id' => $this->size->id,
            'quantity' => 2,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Produk berhasil ditambahkan ke keranjang');

        $this->assertDatabaseHas('cart_items', [
            'catalog_id' => $this->catalog->id,
            'quantity' => 2,
            'price_at_time' => 1500000
        ]);
    }

    public function test_adding_same_item_increments_quantity()
    {
        // First add
        $res1 = $this->postJson(route('cart.add'), [
            'catalog_id' => $this->catalog->id,
            'catalog_size_id' => $this->size->id,
            'quantity' => 1,
        ]);
        
        $cookies = $res1->headers->getCookies();
        $token = null;
        foreach($cookies as $cookie) {
            if ($cookie->getName() === 'cart_token') {
                $token = $cookie->getValue();
            }
        }

        $this->assertNotNull($token);

        // Second add - Manually injecting cookie into the next request
        $response = $this->call('POST', route('cart.add'), [
            'catalog_id' => $this->catalog->id,
            'catalog_size_id' => $this->size->id,
            'quantity' => 2,
        ], ['cart_token' => $token]); // passing it in the $cookies parameter of call()

        $this->assertDatabaseHas('cart_items', [
            'catalog_size_id' => $this->size->id,
            'quantity' => 3
        ]);
    }

    public function test_cannot_add_non_existent_product()
    {
        $response = $this->postJson(route('cart.add'), [
            'catalog_id' => 9999,
            'catalog_size_id' => $this->size->id,
            'quantity' => 1,
        ]);

        $response->assertStatus(422);
    }

    public function test_cannot_add_size_belonging_to_another_product()
    {
        $otherProduct = Catalog::create([
            'name' => 'Adidas', 
            'collection' => 'Classic',
            'price' => 1000000, 
            'status' => 'ready',
            'slug' => 'adidas',
            'public_id' => 'ulid-test-456'
        ]);
        $otherSize = $otherProduct->sizes()->create(['size_eu' => 40]);

        $response = $this->postJson(route('cart.add'), [
            'catalog_id' => $this->catalog->id, // Nike ID
            'catalog_size_id' => $otherSize->id, // Adidas Size ID
            'quantity' => 1,
        ]);

        $response->assertStatus(404);
    }

    public function test_cannot_update_item_belonging_to_another_cart()
    {
        // Cart A
        $cartA = Cart::create();
        $itemA = CartItem::create([
            'cart_id' => $cartA->id,
            'catalog_id' => $this->catalog->id,
            'catalog_size_id' => $this->size->id,
            'quantity' => 1,
            'price_at_time' => 1500000
        ]);

        // Cart B (User Current Session)
        $cartB = Cart::create();
        
        $response = $this->withUnencryptedCookie('cart_token', $cartB->id)
            ->patchJson(route('cart.update', $itemA->id), [
                'quantity' => 5
            ]);

        $response->assertStatus(404);
        $this->assertEquals(1, $itemA->fresh()->quantity);
    }

    public function test_cannot_delete_item_belonging_to_another_cart()
    {
        $cartA = Cart::create();
        $itemA = CartItem::create([
            'cart_id' => $cartA->id,
            'catalog_id' => $this->catalog->id,
            'catalog_size_id' => $this->size->id,
            'quantity' => 1,
            'price_at_time' => 1500000
        ]);

        $cartB = Cart::create();
        
        $response = $this->withUnencryptedCookie('cart_token', $cartB->id)
            ->deleteJson(route('cart.remove', $itemA->id));

        $this->assertDatabaseHas('cart_items', ['id' => $itemA->id]);
    }

    public function test_cart_total_calculation_is_correct()
    {
        $response = $this->postJson(route('cart.add'), [
            'catalog_id' => $this->catalog->id,
            'catalog_size_id' => $this->size->id,
            'quantity' => 2,
        ]);

        $response->assertJsonPath('cart.total', 3000000); // 1.5M * 2
    }

    public function test_invalid_cart_token_cookie_generates_new_cart()
    {
        // Using a non-existent UUID string
        $response = $this->withUnencryptedCookie('cart_token', '00000000-0000-0000-0000-000000000000')
            ->get('/');

        $response->assertCookie('cart_token');
        
        $cookies = $response->headers->getCookies();
        $newToken = null;
        foreach($cookies as $cookie) {
            if ($cookie->getName() === 'cart_token') {
                $newToken = $cookie->getValue();
            }
        }
        
        $this->assertNotEquals('00000000-0000-0000-0000-000000000000', $newToken);
    }

    public function test_expired_cart_triggers_new_cart_creation()
    {
        $expiredCart = Cart::create(['expires_at' => now()->subDay()]);

        $response = $this->withUnencryptedCookie('cart_token', $expiredCart->id)
            ->get('/');

        $cookies = $response->headers->getCookies();
        $newToken = null;
        foreach($cookies as $cookie) {
            if ($cookie->getName() === 'cart_token') {
                $newToken = $cookie->getValue();
            }
        }
        $this->assertNotEquals($expiredCart->id, $newToken);
    }
}
