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
        Schema::create('purchase_invoice_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('purchase_invoice_id');
            $table->uuid('goods_receipt_line_id')->nullable();
            $table->uuid('product_id');
            $table->uuid('unit_id');
            $table->uuid('tax_rate_id')->nullable();

            $table->foreign('purchase_invoice_id')->references('id')->on('purchase_invoices')->cascadeOnDelete();
            $table->foreign('goods_receipt_line_id')->references('id')->on('goods_receipt_lines')->nullOnDelete();
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->foreign('unit_id')->references('id')->on('units')->cascadeOnDelete();
            $table->foreign('tax_rate_id')->references('id')->on('tax_rates')->nullOnDelete();

            $table->decimal('quantity', 18, 6)->default(0);
            $table->decimal('unit_price', 18, 2)->default(0);
            $table->decimal('tax_amount', 18, 2)->default(0);
            $table->decimal('subtotal', 18, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_invoice_lines');
    }
};
