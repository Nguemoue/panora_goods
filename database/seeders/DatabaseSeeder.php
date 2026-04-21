<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Phone;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        // Create Brands
        $brands = collect(['Apple', 'Samsung', 'Google', 'Xiaomi', 'Oppo'])->map(fn ($name) => Brand::create(['name' => $name]));

        // Create Suppliers
        $suppliers = collect([
            ['name' => 'Global Tech Solutions', 'email' => 'contact@globaltech.com', 'phone' => '+123456789'],
            ['name' => 'Mobile Wholesalers Inc.', 'email' => 'sales@mobilewholesale.com', 'phone' => '+987654321'],
        ])->map(fn ($data) => Supplier::create($data));

        // Create Phones
        $phones = [
            ['name' => 'iPhone 15 Pro', 'brand' => 'Apple', 'supplier_price' => 800, 'selling_price' => 1100, 'ram' => 8, 'storage' => 256],
            ['name' => 'Galaxy S24 Ultra', 'brand' => 'Samsung', 'supplier_price' => 900, 'selling_price' => 1300, 'ram' => 12, 'storage' => 512],
            ['name' => 'Pixel 8 Pro', 'brand' => 'Google', 'supplier_price' => 700, 'selling_price' => 999, 'ram' => 12, 'storage' => 128],
        ];

        foreach ($phones as $p) {
            Phone::create([
                'brand_id' => Brand::where('name', $p['brand'])->first()->id,
                'supplier_id' => $suppliers->random()->id,
                'name' => $p['name'],
                'model_number' => 'MOD-' . rand(1000, 9999),
                'supplier_price' => $p['supplier_price'],
                'selling_price' => $p['selling_price'],
                'stock_quantity' => rand(5, 20),
                'ram' => $p['ram'],
                'storage' => $p['storage'],
                'status' => 'in_stock',
            ]);
        }
    }
}
