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
                'code' => 'BGS-NM97-SLV',
                'name' => 'Air Max 97 Silver Bullet',
                'brand' => 'Nike',
                'collection' => 'Lifestyle',
                'price' => 1850000,
                'status' => 'po',
                'sizes' => [39, 40, 41, 42, 43],
                'popularity_score' => 94,
            ],
            [
                'code' => 'BGS-SMB-WHT',
                'name' => 'Samba OG White Gum',
                'brand' => 'Adidas',
                'collection' => 'Lifestyle',
                'price' => 1290000,
                'status' => 'ready',
                'sizes' => [39, 40, 41, 42],
                'popularity_score' => 98,
            ],
            [
                'code' => 'BGS-J1-BRED',
                'name' => 'Jordan 1 Retro High Bred',
                'brand' => 'Jordan',
                'collection' => 'Basketball',
                'price' => 2100000,
                'status' => 'po',
                'sizes' => [40, 41, 42, 43, 44],
                'popularity_score' => 96,
            ],
            [
                'code' => 'BGS-NB574-NVY',
                'name' => 'New Balance 574 Core Navy',
                'brand' => 'New Balance',
                'collection' => 'Lifestyle',
                'price' => 980000,
                'status' => 'ready',
                'sizes' => [39, 40, 41, 42, 43, 44],
                'popularity_score' => 88,
            ],
            [
                'code' => 'BGS-DUNK-PND',
                'name' => 'Nike Dunk Low Retro Panda',
                'brand' => 'Nike',
                'collection' => 'Lifestyle',
                'price' => 1650000,
                'status' => 'habis',
                'sizes' => [],
                'popularity_score' => 91,
            ],
            [
                'code' => 'BGS-FRM-WHT',
                'name' => 'Adidas Forum Low White Blue',
                'brand' => 'Adidas',
                'collection' => 'Lifestyle',
                'price' => 1100000,
                'status' => 'ready',
                'sizes' => [39, 40, 41, 42, 43],
                'popularity_score' => 83,
            ],
            [
                'code' => 'BGS-VNT-CLS',
                'name' => 'Ventela Classic White Low',
                'brand' => 'Ventela',
                'collection' => 'Lifestyle',
                'price' => 420000,
                'status' => 'ready',
                'sizes' => [39, 40, 41, 42, 43, 44],
                'popularity_score' => 87,
            ],
            [
                'code' => 'BGS-J4-BCAT',
                'name' => 'Jordan 4 Retro Black Cat',
                'brand' => 'Jordan',
                'collection' => 'Basketball',
                'price' => 2450000,
                'status' => 'po',
                'sizes' => [41, 42, 43, 44, 45],
                'popularity_score' => 95,
            ],
            [
                'code' => 'BGS-AF1-WHT',
                'name' => 'Air Force 1 Low 07 White',
                'brand' => 'Nike',
                'collection' => 'Lifestyle',
                'price' => 1200000,
                'status' => 'ready',
                'sizes' => [40, 41, 42, 43],
                'popularity_score' => 86,
            ],
            [
                'code' => 'BGS-VNS-OLD',
                'name' => 'Vans Old Skool Black White',
                'brand' => 'Vans',
                'collection' => 'Skateboarding',
                'price' => 750000,
                'status' => 'ready',
                'sizes' => [39, 40, 41, 42, 43],
                'popularity_score' => 84,
            ],
            [
                'code' => 'BGS-PGS-41',
                'name' => 'Nike Pegasus 41 React',
                'brand' => 'Nike',
                'collection' => 'Running',
                'price' => 1550000,
                'status' => 'po',
                'sizes' => [40, 41, 42, 43, 44],
                'popularity_score' => 90,
            ],
            [
                'code' => 'BGS-UB22-BLK',
                'name' => 'Adidas Ultra Boost 22 Core Black',
                'brand' => 'Adidas',
                'collection' => 'Running',
                'price' => 1800000,
                'status' => 'ready',
                'sizes' => [40, 41, 42, 43],
                'popularity_score' => 89,
            ],
        ];

        foreach ($items as $index => $item) {
            $catalog = Catalog::updateOrCreate(
                ['code' => $item['code']],
                [
                    'name' => $item['name'],
                    'brand' => $item['brand'],
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
