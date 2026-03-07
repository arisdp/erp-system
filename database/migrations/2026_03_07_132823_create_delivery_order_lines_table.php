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
        Schema::create('delivery_order_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('delivery_order_id');
            $table->uuid('sales_order_line_id')->nullable();
            $table->uuid('product_id');
            $table->uuid('unit_id');

            $table->foreign('delivery_order_id')->references('id')->on('delivery_orders')->cascadeOnDelete();
            $table->foreign('sales_order_line_id')->references('id')->on('sales_order_lines')->nullOnDelete();
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->foreign('unit_id')->references('id')->on('units')->cascadeOnDelete();

            $table->decimal('quantity_ordered', 18, 6)->default(0);
            $table->decimal('quantity_shipped', 18, 6)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_order_lines');
    }
};
