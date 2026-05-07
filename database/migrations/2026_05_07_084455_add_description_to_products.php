<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', static function (Blueprint $table) {
            $table->text('short_description')->nullable();
            $table->text('long_description')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('products', static function (Blueprint $table) {
            $table->dropColumn('short_description');
            $table->dropColumn('long_description');
        });
    }
};
