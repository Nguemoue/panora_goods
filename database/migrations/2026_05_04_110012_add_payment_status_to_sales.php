<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->date('payment_date_limit')->nullable();
            $table->string('payment_status',100)->default(\App\Enums\PaymentStatusEnum::NOT_INITIATED->value);
            $table->string('payment_type',100);
            $table->text('more_details')->nullable();


        });
    }

    public function down(): void
    {
        Schema::table('sales', static function (Blueprint $table) {
            $table->dropColumn('payment_status');
            $table->dropColumn('payment_type');
        });
    }
};
