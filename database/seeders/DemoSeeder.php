<?php

namespace Database\Seeders;

use App\Enums\UserRoleEnum;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Sale;
use App\Models\Specification;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Users
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@demo.com',
            'password' => Hash::make('password'),
            'role' => UserRoleEnum::ADMIN,
        ]);

        $seller1 = User::factory()->create([
            'name' => 'Seller One',
            'email' => 'seller1@demo.com',
            'password' => Hash::make('password'),
            'role' => UserRoleEnum::SELLER,
        ]);

        $seller2 = User::factory()->create([
            'name' => 'Seller Two',
            'email' => 'seller2@demo.com',
            'password' => Hash::make('password'),
            'role' => UserRoleEnum::SELLER,
        ]);

        $clients = User::factory()->count(5)->create(['role' => UserRoleEnum::CLIENT]);

        // 2. Create Categories and Specifications
        $phoneCat = Category::factory()->create(['name' => 'Telephones', 'description' => 'Mobile phones and smartphones']);
        $fridgeCat = Category::factory()->create(['name' => 'Fridges', 'description' => 'Refrigerators and freezers']);
        $tvCat = Category::factory()->create(['name' => 'Televisions', 'description' => 'Smart TVs and displays']);

        $ram = Specification::factory()->create(['name' => 'RAM', 'input_type' => 'select', 'measure' => 'GB']);
        $storage = Specification::factory()->create(['name' => 'Storage', 'input_type' => 'select', 'measure' => 'GB']);
        $cpu = Specification::factory()->create(['name' => 'CPU', 'input_type' => 'text']);
        $volume = Specification::factory()->create(['name' => 'Volume', 'input_type' => 'number', 'measure' => 'L']);
        $resolution = Specification::factory()->create(['name' => 'Resolution', 'input_type' => 'select']);
        $screenSize = Specification::factory()->create(['name' => 'Screen Size', 'input_type' => 'number', 'measure' => 'inch']);

        // Link specs to categories
        $phoneCat->specifications()->attach([$ram->id, $storage->id, $cpu->id], ['is_required' => true]);
        $fridgeCat->specifications()->attach([$volume->id], ['is_required' => true]);
        $tvCat->specifications()->attach([$resolution->id, $screenSize->id], ['is_required' => true]);

        // 3. Brands and Suppliers
        $brands = Brand::factory()->count(5)->create();
        $suppliers = Supplier::factory()->count(3)->create();

        // 4. Create Products
        // Create 3 Phones
        $phones = Product::factory()->count(3)->create([
            'category_id' => $phoneCat->id,
            'brand_id' => $brands->random()->id,
            'supplier_id' => $suppliers->random()->id,
        ]);

        foreach ($phones as $phone) {
            ProductImage::factory()->count(4)->create(['product_id' => $phone->id]);
            $phone->specifications()->attach([
                $ram->id => ['value' => '8'],
                $storage->id => ['value' => '256'],
                $cpu->id => ['value' => 'Octa-core 3.0GHz'],
            ]);
        }

        // Create 2 Fridges
        $fridges = Product::factory()->count(2)->create([
            'category_id' => $fridgeCat->id,
            'brand_id' => $brands->random()->id,
            'supplier_id' => $suppliers->random()->id,
        ]);

        foreach ($fridges as $fridge) {
            ProductImage::factory()->count(4)->create(['product_id' => $fridge->id]);
            $fridge->specifications()->attach([
                $volume->id => ['value' => '450'],
            ]);
        }

        // 5. Create Sales
        Sale::factory()->count(10)->create([
            'seller_id' => $seller1->id,
            'client_id' => $clients->random()->id,
            'product_id' => $phones->random()->id,
        ]);

        Sale::factory()->count(5)->create([
            'seller_id' => $seller2->id,
            'client_id' => $clients->random()->id,
            'product_id' => $fridges->random()->id,
        ]);
    }
}
