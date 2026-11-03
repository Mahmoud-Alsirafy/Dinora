<?php

namespace Database\Factories;

use App\Models\Categoie;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Categoie>
 */
class CategoieFactory extends Factory
{
    protected $model = Categoie::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(fake()->numberBetween(1, 2), true);

        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name) . '-' . fake()->unique()->numberBetween(100, 99999),
            'description' => fake()->sentence(),
            'image' => fake()->imageUrl(640, 480, 'products', true),
            'is_active' => fake()->boolean(90),
        ];
    }
}

