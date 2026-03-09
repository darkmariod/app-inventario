<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sku' => strtoupper($this->faker->unique()->bothify('???-#####')),
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(2, 10, 500),
            'cost' => $this->faker->randomFloat(2, 5, 300),
            'stock' => $this->faker->numberBetween(0, 100),
            'min_stock' => $this->faker->numberBetween(0, 10),
            'category_id' => function () {
                return \App\Models\Category::inRandomOrder()->first()->id;
            },
            'supplier_id' => function () {
                return \App\Models\Supplier::factory()->create()->id;
            },
        ];
    }
}
