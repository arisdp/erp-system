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
        Schema::create('goods_receipt_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('goods_receipt_id');
            $table->uuid('purchase_order_line_id')->nullable();
            $table->uuid('product_id');
            $table->uuid('unit_id');

            $table->foreign('goods_receipt_id')->references('id')->on('goods_receipts')->cascadeOnDelete();
            $table->foreign('purchase_order_line_id')->references('id')->on('purchase_order_lines')->nullOnDelete();
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->foreign('unit_id')->references('id')->on('units')->cascadeOnDelete();

            $table->decimal('quantity_ordered', 18, 6)->default(0);
            $table->decimal('quantity_received', 18, 6)->default(0);
            $table->string('batch_number')->nullable();
            $table->date('expiry_date')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goods_receipt_lines');
    }
};
