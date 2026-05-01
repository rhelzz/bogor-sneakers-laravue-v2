<?php

namespace Database\Seeders;

use App\Models\Catalog;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    /**
     * Seed katalog demo data.
     */
    public function run(): void
    {
        $items = [
            [
                'name' => 'Air Max 97 Silver Bullet',
                'collection' => 'Lifestyle',
                'price' => 1850000,
                'status' => 'po',
                'sizes' => [39, 40, 41, 42, 43],
                'popularity_score' => 94,
            ],
            [
                'name' => 'Samba OG White Gum',
                'collection' => 'Lifestyle',
                'price' => 1290000,
                'status' => 'ready',
                'sizes' => [39, 40, 41, 42],
                'popularity_score' => 98,
            ],
            [
                'name' => 'Jordan 1 Retro High Bred',
                'collection' => 'Basketball',
                'price' => 2100000,
                'status' => 'po',
                'sizes' => [40, 41, 42, 43, 44],
                'popularity_score' => 96,
            ],
            [
                'name' => 'New Balance 574 Core Navy',
                'collection' => 'Lifestyle',
                'price' => 980000,
                'status' => 'ready',
                'sizes' => [39, 40, 41, 42, 43, 44],
                'popularity_score' => 88,
            ],
            [
                'name' => 'Nike Dunk Low Retro Panda',
                'collection' => 'Lifestyle',
                'price' => 1650000,
                'status' => 'habis',
                'sizes' => [],
                'popularity_score' => 91,
            ],
            [
                'name' => 'Adidas Forum Low White Blue',
                'collection' => 'Lifestyle',
                'price' => 1100000,
                'status' => 'ready',
                'sizes' => [39, 40, 41, 42, 43],
                'popularity_score' => 83,
            ],
            [
                'name' => 'Ventela Classic White Low',
                'collection' => 'Lifestyle',
                'price' => 420000,
                'status' => 'ready',
                'sizes' => [39, 40, 41, 42, 43, 44],
                'popularity_score' => 87,
            ],
            [
                'name' => 'Jordan 4 Retro Black Cat',
                'collection' => 'Basketball',
                'price' => 2450000,
                'status' => 'po',
                'sizes' => [41, 42, 43, 44, 45],
                'popularity_score' => 95,
            ],
            [
                'name' => 'Air Force 1 Low 07 White',
                'collection' => 'Lifestyle',
                'price' => 1200000,
                'status' => 'ready',
                'sizes' => [40, 41, 42, 43],
                'popularity_score' => 86,
            ],
            [
                'name' => 'Vans Old Skool Black White',
                'collection' => 'Skateboarding',
                'price' => 750000,
                'status' => 'ready',
                'sizes' => [39, 40, 41, 42, 43],
                'popularity_score' => 84,
            ],
            [
                'name' => 'Nike Pegasus 41 React',
                'collection' => 'Running',
                'price' => 1550000,
                'status' => 'po',
                'sizes' => [40, 41, 42, 43, 44],
                'popularity_score' => 90,
            ],
            [
                'name' => 'Adidas Ultra Boost 22 Core Black',
                'collection' => 'Running',
                'price' => 1800000,
                'status' => 'ready',
                'sizes' => [40, 41, 42, 43],
                'popularity_score' => 89,
            ],
        ];

        foreach ($items as $index => $item) {
            $catalog = Catalog::updateOrCreate(
                ['name' => $item['name']],
                [
                    'collection' => $item['collection'],
                    'description' => null,
                    'price' => $item['price'],
                    'status' => $item['status'],
                    'preorder_min_days' => $item['status'] === 'po' ? 14 : null,
                    'preorder_max_days' => $item['status'] === 'po' ? 21 : null,
                    'popularity_score' => $item['popularity_score'],
                    'is_active' => true,
                    'sort_order' => $index,
                ],
            );

            $catalog->sizes()->delete();

            $sizes = collect($item['sizes'])
                ->map(fn (int $size): array => ['size_eu' => $size])
                ->all();

            if ($sizes !== []) {
                $catalog->sizes()->createMany($sizes);
            }
        }
    }
}
