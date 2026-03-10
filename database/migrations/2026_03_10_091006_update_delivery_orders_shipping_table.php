<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('delivery_orders', function (Blueprint $table) {
            $table->string('shipping_method')->nullable()->after('shipped_by');
            $table->string('tracking_number')->nullable()->after('shipping_method');
            $table->timestamp('received_at')->nullable()->after('tracking_number');
            $table->string('received_by')->nullable()->after('received_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_orders', function (Blueprint $table) {
            $table->dropColumn(['shipping_method', 'tracking_number', 'received_at', 'received_by']);
        });
    }
};
