<?php

namespace Database\Factories;

use App\Models\MerchantProfiel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MerchantProfiel>
 */
class MerchantProfielFactory extends Factory
{
    protected $model = MerchantProfiel::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'mer_id' => User::factory()->merchant(),
            'store_name' => fake()->company(),
            'store_address' => fake()->address(),
            'phone' => fake()->phoneNumber(),
            'status' => fake()->randomElement(['pending', 'approved', 'rejected']),
            'store_status' => fake()->boolean(90),
            'last_active' => fake()->optional()->dateTimeThisMonth(),
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
        ]);
    }
}

