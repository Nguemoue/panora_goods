<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('products', 'product_code')) {
            return;
        }
        Schema::table('products', static function (Blueprint $table) {
            $table->string('product_code')->nullable()->after('id');
        });
        // adding default product_code to existing products
        \App\Models\Product::query()->chunk(50, function ($products) {
            $products->each(function (\App\Models\Product $product) {
                $product->update(['product_code' => app(\App\Services\ProductCodeGenerator::class)->generate($product->name)]);
            });
        });

    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
};
