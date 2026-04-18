<?php

namespace Database\Factories;

use App\Models\WhatsappAdmin;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WhatsappAdmin>
 */
class WhatsappAdminFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->firstName();

        return [
            'name' => sprintf('%s - Admin', $name),
            'role' => $this->faker->randomElement([
                'PO / Order / Ketersediaan',
                'Komplain / Tracking / Retur',
                'Kustom / Desain / Konsultasi',
            ]),
            'phone' => sprintf('62%s', $this->faker->numerify('8###########')),
            'initial' => mb_strtoupper(mb_substr($name, 0, 1)),
            'color' => $this->faker->randomElement(WhatsappAdmin::availableColorKeys()),
            'is_active' => $this->faker->boolean(90),
            'sort_order' => $this->faker->numberBetween(0, 20),
        ];
    }
}
