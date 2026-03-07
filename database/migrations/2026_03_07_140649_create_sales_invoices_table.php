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
        Schema::create('sales_invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('company_id');
            $table->uuid('customer_id');
            $table->uuid('sales_order_id')->nullable();
            $table->uuid('delivery_order_id')->nullable();
            
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();
            $table->foreign('customer_id')->references('id')->on('customers')->cascadeOnDelete();
            $table->foreign('sales_order_id')->references('id')->on('sales_orders')->nullOnDelete();
            $table->foreign('delivery_order_id')->references('id')->on('delivery_orders')->nullOnDelete();

            $table->string('invoice_number')->unique();
            $table->date('invoice_date');
            $table->date('due_date')->nullable();
            $table->string('status')->default('Draft'); // Draft, Unpaid, Partial, Paid, Cancelled
            
            $table->decimal('total_amount', 18, 2)->default(0);
            $table->decimal('tax_amount', 18, 2)->default(0);
            $table->decimal('platform_fee', 18, 2)->default(0);
            $table->decimal('net_amount', 18, 2)->default(0);
            
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_invoices');
    }
};
