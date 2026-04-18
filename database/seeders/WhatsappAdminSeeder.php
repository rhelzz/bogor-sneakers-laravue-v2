<?php

namespace Database\Seeders;

use App\Models\WhatsappAdmin;
use Illuminate\Database\Seeder;

class WhatsappAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rows = [
            [
                'name' => 'Rizky - Admin',
                'role' => 'PO / Order / Ketersediaan',
                'phone' => '6281234567890',
                'initial' => 'R',
                'color' => 'matcha',
                'is_active' => true,
                'sort_order' => 0,
            ],
            [
                'name' => 'Farhan - CS',
                'role' => 'Komplain / Tracking / Retur',
                'phone' => '6289876543210',
                'initial' => 'F',
                'color' => 'indigo',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Dinda - DIY',
                'role' => 'Kustom / Desain / Konsultasi',
                'phone' => '6285511223344',
                'initial' => 'D',
                'color' => 'sakura',
                'is_active' => true,
                'sort_order' => 2,
            ],
        ];

        foreach ($rows as $row) {
            WhatsappAdmin::updateOrCreate(
                ['phone' => $row['phone']],
                $row,
            );
        }
    }
}
