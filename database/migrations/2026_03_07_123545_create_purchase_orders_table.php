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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->uuid('company_id');
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();
            
            $table->uuid('supplier_id');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->cascadeOnDelete();
            
            $table->uuid('payment_term_id')->nullable();
            $table->foreign('payment_term_id')->references('id')->on('payment_terms')->nullOnDelete();
            
            $table->uuid('currency_id')->nullable();
            $table->foreign('currency_id')->references('id')->on('currencies')->nullOnDelete();

            $table->string('po_number');
            $table->date('order_date');
            $table->date('expected_delivery_date')->nullable();
            
            $table->decimal('total_amount', 18, 2)->default(0);
            $table->decimal('discount_amount', 18, 2)->default(0);
            $table->decimal('tax_amount', 18, 2)->default(0);
            $table->decimal('net_amount', 18, 2)->default(0);
            
            $table->enum('status', ['Draft', 'Pending', 'Approved', 'Rejected', 'Open', 'Closed', 'Cancelled'])->default('Draft');
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'po_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
