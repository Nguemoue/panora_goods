<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Supplier;
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
        $supplierPrice = fake()->randomFloat(2, 100, 2000);
        return [
            'category_id' => Category::factory(),
            'brand_id' => Brand::factory(),
            'supplier_id' => Supplier::factory(),
            'name' => fake()->word(),
            'model_number' => fake()->bothify('??-####'),
            'supplier_price' => $supplierPrice,
            'selling_price' => $supplierPrice * 1.2,
            'stock_quantity' => fake()->numberBetween(0, 100),
            'status' => 'active',
        ];
    }
}
