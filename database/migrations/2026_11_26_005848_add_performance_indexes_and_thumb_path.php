<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', static function (Blueprint $table): void {
            $table->index(['status', 'created_at'], 'products_status_created_at_index');
            $table->index(['status', 'category_id', 'brand_id'], 'products_status_cat_brand_index');
        });

        Schema::table('product_images', static function (Blueprint $table): void {
            if (! Schema::hasColumn('product_images', 'thumb_path')) {
                $table->string('thumb_path')->nullable()->after('path');
            }
            $table->index(['product_id', 'is_primary'], 'product_images_product_primary_index');
        });
    }

    public function down(): void
    {
        Schema::table('products', static function (Blueprint $table): void {
            $table->dropIndex('products_status_created_at_index');
            $table->dropIndex('products_status_cat_brand_index');
        });

        Schema::table('product_images', static function (Blueprint $table): void {
            $table->dropIndex('product_images_product_primary_index');
            if (Schema::hasColumn('product_images', 'thumb_path')) {
                $table->dropColumn('thumb_path');
            }
        });
    }
};
