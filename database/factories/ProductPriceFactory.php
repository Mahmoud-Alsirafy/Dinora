<?php

namespace Database\Factories;

use App\Models\ProductPrice;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductPrice>
 */
class ProductPriceFactory extends Factory
{
    protected $model = ProductPrice::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_variant_id' => ProductVariant::factory(),
            'customer_type' => fake()->randomElement(['customer', 'merchant']),
            'price' => fake()->randomFloat(2, 10, 500),
        ];
    }

    public function customer(): static
    {
        return $this->state(fn (array $attributes) => [
            'customer_type' => 'customer',
        ]);
    }

    public function merchant(): static
    {
        return $this->state(fn (array $attributes) => [
            'customer_type' => 'merchant',
        ]);
    }
}

