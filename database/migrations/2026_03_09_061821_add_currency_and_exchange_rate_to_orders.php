<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->uuid('currency_id')->nullable();
            $table->decimal('exchange_rate', 15, 6)->default(1.000000);
            $table->foreign('currency_id')->references('id')->on('currencies');
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->decimal('exchange_rate', 15, 6)->default(1.000000);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropForeign(['currency_id']);
            }
            $table->dropColumn(['currency_id', 'exchange_rate']);
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn('exchange_rate');
        });
    }
};
