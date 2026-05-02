<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use App\Enums\UserRoleEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sale>
 */
class SaleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $product = Product::factory()->create();
        $quantity = fake()->numberBetween(1, 5);
        $salePrice = $product->selling_price;
        $supplierPrice = $product->supplier_price;
        $profit = ($salePrice - $supplierPrice) * $quantity;

        return [
            'product_id' => $product->id,
            'seller_id' => User::factory()->state(['role' => UserRoleEnum::SELLER]),
            'client_id' => User::factory()->state(['role' => UserRoleEnum::CLIENT]),
            'quantity' => $quantity,
            'supplier_price_at_sale' => $supplierPrice,
            'sale_price' => $salePrice,
            'profit' => $profit,
            'customer_name' => fake()->name(),
            'sold_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
