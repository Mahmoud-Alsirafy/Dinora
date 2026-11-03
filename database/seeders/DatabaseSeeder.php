<?php

namespace Database\Seeders;

use App\Models\Categoie;
use App\Models\Inventory;
use App\Models\MerchantProfiel;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Admin User
        User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        // 2. Merchants & Profiles
        $merchants = User::factory()->merchant()->count(3)->create();
        foreach ($merchants as $merchant) {
            MerchantProfiel::factory()->approved()->create([
                'mer_id' => $merchant->id,
            ]);
        }

        // 3. Regular Customers
        User::factory()->customer()->count(5)->create();

        // 4. Warehouses
        $warehouses = Warehouse::factory()->count(3)->create();

        // 5. Categories with Products, Variants, Prices, and Inventory
        Categoie::factory()
            ->count(5)
            ->create()
            ->each(function (Categoie $category) use ($warehouses) {
                Product::factory()
                    ->count(3)
                    ->create(['cate_id' => $category->id])
                    ->each(function (Product $product) use ($warehouses) {
                        ProductVariant::factory()
                            ->count(2)
                            ->create(['product_id' => $product->id])
                            ->each(function (ProductVariant $variant) use ($warehouses) {
                                // Prices for customer and merchant
                                ProductPrice::factory()->customer()->create([
                                    'product_variant_id' => $variant->id,
                                    'price' => fake()->randomFloat(2, 20, 200),
                                ]);
                                ProductPrice::factory()->merchant()->create([
                                    'product_variant_id' => $variant->id,
                                    'price' => fake()->randomFloat(2, 10, 150),
                                ]);

                                // Inventory across warehouses
                                foreach ($warehouses as $warehouse) {
                                    Inventory::factory()->create([
                                        'warehouse_id' => $warehouse->id,
                                        'product_variant_id' => $variant->id,
                                        'quantity' => fake()->numberBetween(10, 100),
                                    ]);
                                }
                            });
                    });
            });
    }
}
