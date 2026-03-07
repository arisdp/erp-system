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
        Schema::create('purchase_order_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->uuid('purchase_order_id');
            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->cascadeOnDelete();
            
            $table->uuid('product_id')->nullable();
            $table->foreign('product_id')->references('id')->on('products')->nullOnDelete();
            
            $table->uuid('unit_id')->nullable();
            $table->foreign('unit_id')->references('id')->on('units')->nullOnDelete();
            
            $table->uuid('tax_rate_id')->nullable();
            $table->foreign('tax_rate_id')->references('id')->on('tax_rates')->nullOnDelete();

            $table->string('description')->nullable();
            $table->decimal('quantity', 18, 6)->default(0);
            $table->decimal('unit_price', 18, 2)->default(0);
            $table->decimal('discount_amount', 18, 2)->default(0);
            $table->decimal('tax_amount', 18, 2)->default(0);
            $table->decimal('subtotal', 18, 2)->default(0);
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order_lines');
    }
};
