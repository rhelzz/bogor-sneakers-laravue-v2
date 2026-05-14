<?php

namespace Database\Factories;

use App\Models\Catalog;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Catalog>
 */
class CatalogFactory extends Factory
{
    protected $model = Catalog::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->words(3, true);
        return [
            'public_id' => (string) Str::ulid(),
            'slug' => Str::slug($name),
            'name' => $name,
            'collection' => $this->faker->word(),
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->numberBetween(500000, 5000000),
            'status' => $this->faker->randomElement(['ready', 'po', 'habis']),
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
