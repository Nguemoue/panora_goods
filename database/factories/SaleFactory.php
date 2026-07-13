<?php

namespace Database\Factories;

use App\Enums\PaymentStatusEnum;
use App\Enums\PaymentTypeEnum;
use App\Enums\SaleStatusEnum;
use App\Enums\UserRoleEnum;
use App\Models\Client;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sale>
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
            'client_id' => Client::factory(),
            'quantity' => $quantity,
            'supplier_price_at_sale' => $supplierPrice,
            'sale_price' => $salePrice,
            'profit' => $profit,
            'customer_name' => fake()->name(),
            'status' => SaleStatusEnum::PENDING->value,
            'payment_status' => PaymentStatusEnum::NOT_INITIATED->value,
            'payment_type' => PaymentTypeEnum::ONE_TIME->value,
            'sold_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
