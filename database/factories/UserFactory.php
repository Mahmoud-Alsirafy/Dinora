<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('123'),
            'type' => fake()->randomElement(['customer', 'merchant', 'admin']),
            'phone' => fake()->phoneNumber(),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function customer(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => 'customer',
        ]);
    }

    public function merchant(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => 'merchant',
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => 'admin',
        ]);
    }
}