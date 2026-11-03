<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    protected $model = ProductVariant::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'sku' => strtoupper(fake()->unique()->bothify('SKU-####-????')),
            'color' => fake()->safeColorName(),
            'size' => fake()->randomElement(['XS', 'S', 'M', 'L', 'XL', 'XXL', null]),
            'unit_name' => fake()->randomElement(['قطعة', 'طقم', 'كرتونة', 'حبة']),
            'quantity_per_unit' => fake()->randomElement([1, 1, 1, 6, 12, 24]),
            'is_active' => fake()->boolean(90),
        ];
    }
}

