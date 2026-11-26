<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sale_payments', static function (Blueprint $table): void {
            if (! Schema::hasColumn('sale_payments', 'notes')) {
                $table->text('notes')->nullable()->after('confirmation_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sale_payments', static function (Blueprint $table): void {
            if (Schema::hasColumn('sale_payments', 'notes')) {
                $table->dropColumn('notes');
            }
        });
    }
};
