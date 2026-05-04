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

        // 2. Create Categories and Specifications
        $phoneCat = Category::query()->create(['name' => 'Telephones', 'description' => 'Mobile phones and smartphones']);
        $fridgeCat = Category::query()->create(['name' => 'Fridges', 'description' => 'Refrigerators and freezers']);
        $tvCat = Category::query()->create(['name' => 'Televisions', 'description' => 'Smart TVs and displays']);

        $ram = Specification::query()->create(['name' => 'RAM', 'input_type' => 'select', 'measure' => 'GB']);
        $storage = Specification::query()->create(['name' => 'Storage', 'input_type' => 'select', 'measure' => 'GB']);
        $cpu = Specification::query()->create(['name' => 'CPU', 'input_type' => 'text']);
        $volume = Specification::query()->create(['name' => 'Volume', 'input_type' => 'number', 'measure' => 'L']);
        $resolution = Specification::query()->create(['name' => 'Resolution', 'input_type' => 'select']);
        $screenSize = Specification::query()->create(['name' => 'Screen Size', 'input_type' => 'number', 'measure' => 'inch']);

        // Link specs to categories
        $phoneCat->specifications()->attach([$ram->id, $storage->id, $cpu->id], ['is_required' => true]);
        $fridgeCat->specifications()->attach([$volume->id], ['is_required' => true]);
        $tvCat->specifications()->attach([$resolution->id, $screenSize->id], ['is_required' => true]);


    }
}
