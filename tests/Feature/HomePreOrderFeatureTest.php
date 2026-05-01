<?php

namespace Tests\Feature;

use App\Models\Catalog;
use App\Models\HomePreorder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class HomePreOrderFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_cannot_set_more_than_five_visible_preorders(): void
    {
        $catalogs = collect(range(1, 6))->map(
            fn (int $index): Catalog => $this->createCatalog(
                'po',
                true,
                $index,
            ),
        );

        foreach ($catalogs->take(5)->values() as $index => $catalog) {
            HomePreorder::create([
                'catalog_id' => $catalog->id,
                'status' => 'buka',
                'is_visible' => true,
                'max_slots' => 30,
                'filled_slots' => 10,
                'countdown_ends_at' => now()->addDays(2 + $index),
                'batch_label' => sprintf('#%02d', $index + 1),
                'urutan' => $index + 1,
            ]);
        }

        $response = $this->postJson('/admin/pre-order-home', [
            'catalog_id' => $catalogs->last()?->id,
            'status' => 'buka',
            'is_visible' => true,
            'max_slots' => 24,
            'filled_slots' => 3,
            'countdown_ends_at' => now()->addDays(7)->toISOString(),
            'batch_label' => '#06',
            'urutan' => 10,
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['is_visible']);
    }

    public function test_admin_cannot_set_filled_slots_above_max_slots(): void
    {
        $catalog = $this->createCatalog('po', true, 1);

        $response = $this->postJson('/admin/pre-order-home', [
            'catalog_id' => $catalog->id,
            'status' => 'buka',
            'is_visible' => false,
            'max_slots' => 10,
            'filled_slots' => 11,
            'countdown_ends_at' => now()->addDay()->toISOString(),
            'batch_label' => '#01',
            'urutan' => 1,
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['filled_slots']);
    }

    public function test_home_uses_dynamic_preorder_and_latest_collection_limits(): void
    {
        $catalogs = collect(range(1, 10))->map(function (int $index): Catalog {
            $status = $index <= 7 ? 'po' : 'ready';

            return $this->createCatalog(
                $status,
                true,
                $index,
            );
        });

        $expired = HomePreorder::create([
            'catalog_id' => $catalogs[0]->id,
            'status' => 'buka',
            'is_visible' => true,
            'max_slots' => 20,
            'filled_slots' => 6,
            'countdown_ends_at' => now()->subMinutes(30),
            'batch_label' => '#00',
            'urutan' => 1,
        ]);

        foreach ($catalogs->slice(1, 6)->values() as $index => $catalog) {
            HomePreorder::create([
                'catalog_id' => $catalog->id,
                'status' => $index % 2 === 0 ? 'buka' : 'hampir_habis',
                'is_visible' => true,
                'max_slots' => 20,
                'filled_slots' => 8,
                'countdown_ends_at' => now()->addDays(3 + $index),
                'batch_label' => sprintf('#%02d', $index + 1),
                'urutan' => $index + 2,
            ]);
        }

        HomePreorder::create([
            'catalog_id' => $catalogs[7]->id,
            'status' => 'buka',
            'is_visible' => true,
            'max_slots' => 20,
            'filled_slots' => 5,
            'countdown_ends_at' => now()->addDays(10),
            'batch_label' => '#07',
            'urutan' => 7,
        ]);

        $response = $this->get(route('home'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Home')
                ->has('preorderItems', 5)
                ->has('latestCollections', 8));

        $this->assertDatabaseHas('home_preorders', [
            'id' => $expired->id,
            'is_visible' => false,
        ]);
    }

    private function createCatalog(
        string $status,
        bool $isActive,
        int $sortOrder,
    ): Catalog {
        $catalog = Catalog::create([
            'name' => 'Produk ' . Str::random(8),
            'collection' => 'Lifestyle',
            'description' => null,
            'price' => 1350000,
            'status' => $status,
            'preorder_min_days' => $status === 'po' ? 14 : null,
            'preorder_max_days' => $status === 'po' ? 21 : null,
            'discount_type' => null,
            'discount_value' => null,
            'discount_starts_at' => null,
            'discount_ends_at' => null,
            'popularity_score' => 90,
            'is_active' => $isActive,
            'sort_order' => $sortOrder,
        ]);

        $catalog->sizes()->createMany([
            ['size_eu' => 40],
            ['size_eu' => 42],
        ]);

        return $catalog->fresh(['sizes']);
    }
}
