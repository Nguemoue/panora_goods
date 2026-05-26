<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_zone', static function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('zone_id')->constrained('zones')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_zone');
    }
};
